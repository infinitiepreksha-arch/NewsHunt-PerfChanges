<?php
    namespace App\Jobs;

    use App\Events\SendNotification;
    use App\Models\Admin\Notifications;
    use App\Models\Post;
    use App\Models\Setting;
    use App\Services\CachingService;
    use App\Services\NotificationService;
    use Carbon\Carbon;
    use Illuminate\Bus\Queueable;
    use Illuminate\Contracts\Queue\ShouldQueue;
    use Illuminate\Database\Eloquent\Collection;
    use Illuminate\Foundation\Bus\Dispatchable;
    use Illuminate\Http\Client\RequestException;
    use Illuminate\Queue\InteractsWithQueue;
    use Illuminate\Queue\SerializesModels;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Http;
    use Illuminate\Support\Facades\Log;
    use Illuminate\Support\Str;

    class FetchRssFeedJob implements ShouldQueue
    {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $rssFeeds;

    public function __construct(Collection $rssFeeds)
    {
        $this->rssFeeds = $rssFeeds;
    }

    public function handle()
    {
        $results = [
            'saved'                    => 0,
            'skipped'                  => 0,
            'already_exists'           => 0,
            'skipped_description_type' => null,
            'feed_description_type'    => null,
        ];

        foreach ($this->rssFeeds as $rssFeed) {
            $results['feed_description_type'] = $rssFeed->description_type;
            try {
                $response = Http::get($rssFeed->feed_url);
                if ($response->successful()) {
                    $feedData   = simplexml_load_string($response->body(), 'SimpleXMLElement', LIBXML_NOCDATA);
                    $namespaces = $feedData->getNamespaces(true);

                    // Handle different feed types
                    $items = $this->getFeedItems($feedData);

                    foreach ($items as $item) {
                        $contentEncoded = null;
                        if (isset($item->children('content', true)->encoded)) {
                            $contentEncoded = (string) $item->children('content', true)->encoded;
                        }

                        // Extract image from contentEncoded
                        $imageFromContent = $this->extractImageFromContent($contentEncoded);

                        $title = strip_tags(trim((string) $item->title));
                        $slug  = Str::slug($title);
                        if (empty($slug)) {
                            $slug = 'artical-' . uniqid();
                        }

                        $videoData = $this->extractVideoData($item, $namespaces);

                        // Check if the selected description_type is available in the feed item
                        if (! $this->isDescriptionTypeAvailable($item, $rssFeed->description_type, $namespaces)) {
                            $results['skipped']++;
                            $results['skipped_description_type'] = $rssFeed->description_type;
                            // Log::info("Skipping feed item '{$title}' - selected description_type '{$rssFeed->description_type}' not available in RSS feed.");
                            continue;
                        }

                        if ($videoData['video_type'] === 'youtube') {

                            if (Post::where('title', $title)->orWhere('slug', $slug)->exists()) {
                                $results['already_exists']++;
                            } else {
                                $descriptionTag = '';
                                $contentTag     = '';

                                // Get <description>
                                if (isset($item->description)) {
                                    $descriptionTag = trim((string) $item->description);
                                }

                                // Get <content:encoded>
                                if (isset($item->children('content', true)->encoded)) {
                                    $contentTag = trim((string) $item->children('content', true)->encoded);
                                }
                                if ($rssFeed->description_type === 'media:description') {
                                    // Merge logic (ALWAYS append content after description)
                                    if (! empty($descriptionTag) && ! empty($contentTag)) {
                                        $finalDescription = $descriptionTag . '<br><br>' . $contentTag;
                                    } elseif (! empty($descriptionTag)) {
                                        $finalDescription = $descriptionTag;
                                    } elseif (! empty($contentTag)) {
                                        $finalDescription = $contentTag;
                                    } else {
                                        $finalDescription = $videoData['description'] ?? '';
                                    }
                                } else {
                                    // Default fallback for youtube if not media:description
                                    $finalDescription = $videoData['description'] ?? $descriptionTag ?? $contentTag ?? '';
                                }

                                $link           = (string) $item->link;
                                $pubDate        = trim(strip_tags((string) $item->pubDate));
                                $publishDate    = Carbon::parse($item->pubDate);
                                $newsLangaugeId = $rssFeed->news_language_id;

                                DB::enableQueryLog();
                                DB::transaction(function () use ($rssFeed, $title, $slug, $link, $finalDescription, $pubDate, $publishDate, $newsLangaugeId, $videoData) {
                                    Post::create([
                                        'channel_id'       => $rssFeed->channel_id,
                                        'topic_id'         => $rssFeed->topic_id,
                                        'title'            => $title,
                                        'resource'         => $link,
                                        'slug'             => $slug,
                                        'image'            => $videoData['thumbnail_url'] ?? null,
                                        'description'      => $finalDescription,
                                        'status'           => 'active',
                                        'pubdate'          => $pubDate,
                                        'publish_date'     => $publishDate,
                                        'news_language_id' => $newsLangaugeId,
                                        'video_thumb'      => $videoData['thumbnail_url'] ?? null,
                                        'video_url'        => $videoData['video_url'],
                                        'video_embed'      => $videoData['video_embed'],
                                        'video_type'       => $videoData['video_type'],
                                        'is_video'         => $videoData['is_video'],
                                        'is_short_video'   => $videoData['is_short_video'],
                                        'video'            => $videoData['video_embed'],
                                        'type'             => 'youtube',
                                    ]);
                                });
                                $results['saved']++;
                            }
                        } else {
                            $slug = Str::slug($title); // generate slug from title

                            if (empty($slug)) {
                                $slug = 'post-' . time();
                            }

                            $originalSlug = $slug;
                            $counter      = 1;
                            while (Post::where('slug', $slug)->exists()) {
                                $slug = $originalSlug . '-' . $counter++;
                            }

                            if (Post::where('title', $title)->orWhere('slug', $slug)->exists()) {
                                $results['already_exists']++;
                            } else {
                                $descriptionTag = isset($item->description) ? trim((string) $item->description) : '';
                                $contentTag     = isset($item->children('content', true)->encoded) ? trim((string) $item->children('content', true)->encoded) : '';

                                if (empty(strip_tags($descriptionTag)) && empty(strip_tags($contentTag)) && empty(strip_tags($videoData['description'] ?? ''))) {
                                    continue;
                                }

                                $finalDescription = '';
                                if ($rssFeed->description_type === 'content-encoded') {
                                    $finalDescription = ! empty($contentTag) ? $contentTag : $descriptionTag;
                                } elseif ($rssFeed->description_type === 'description-tag') {
                                    $finalDescription = ! empty($descriptionTag) ? $descriptionTag : $contentTag;
                                } elseif ($rssFeed->description_type === 'media:description') {
                                    $finalDescription = ! empty($videoData['description']) ? $videoData['description'] : (! empty($descriptionTag) ? $descriptionTag : $contentTag);
                                } else {
                                    // Fallback for null: Use content:encoded if available, otherwise description
                                    $finalDescription = ! empty($descriptionTag) ? $descriptionTag : (! empty($contentTag) ? $contentTag : ($videoData['description'] ?? ''));
                                }

                                if (empty($finalDescription)) {
                                    $finalDescription = "";
                                }

                                $link           = (string) $item->link;
                                $pubDate        = trim(strip_tags((string) $item->pubDate));
                                $imageUrl       = $this->extractImageUrl($item, $namespaces);
                                $publishDate    = Carbon::parse($item->pubDate);
                                $newsLangaugeId = $rssFeed->news_language_id;
                                $defaultImage   = Setting::where('name', 'default_image')->value('value');
                                DB::enableQueryLog();

                                DB::transaction(function () use ($rssFeed, $title, $slug, $link, $finalDescription, $pubDate, $publishDate, $imageUrl, $newsLangaugeId, $defaultImage, $imageFromContent) {
                                    Post::create([
                                        'channel_id'       => $rssFeed->channel_id,
                                        'topic_id'         => $rssFeed->topic_id,
                                        'title'            => $title,
                                        'resource'         => $link,
                                        'slug'             => $slug,
                                        'image'            => $imageUrl ?? $imageFromContent ?? null,
                                        'description'      => $finalDescription ?? "",
                                        'type'             => 'post',
                                        'status'           => 'active',
                                        'pubdate'          => $pubDate,
                                        'publish_date'     => $publishDate,
                                        'news_language_id' => $newsLangaugeId,
                                    ]);
                                });
                                $results['saved']++;
                            }
                        }
                    }
                } else {
                    Log::error("Failed to fetch RSS feed: " . $rssFeed->feed_url);
                }
            } catch (RequestException $e) {
                Log::error("Error fetching RSS feed: " . $rssFeed->feed_url . " - " . $e->getMessage());
            } catch (\Throwable $e) {
                Log::error("Error processing RSS feed: " . $rssFeed->feed_url . " - " . $e->getMessage());
            }
        }
        // $existPostSlugs = Notifications::select('slug')->pluck('slug')->toArray();
        // $post           = Post::select('id', 'title', 'description', 'image', 'slug')->whereNotIn('slug', $existPostSlugs)->orderBy('publish_date', 'desc')->first();

        // $fcmIds = UserFcm::select(['fcm_id', 'platform'])->get()->toArray();

        // /* Call an event for send notification */
        // if ($post) {
        //     event(new SendNotification($post->title, $post->description, $post->image, $post->slug, $fcmIds));

        $settings = CachingService::getSystemSettings();
        if (($settings['automatic_notifications'] ?? 1) == 1) {
            $existPostSlugs       = Notifications::select('slug')->pluck('slug')->toArray();
            $postsForNotification = Post::select('id', 'title', 'description', 'image', 'slug', 'news_language_id')
                ->whereNotIn('slug', $existPostSlugs)
                ->orderBy('publish_date', 'desc')
                ->get();

            if ($postsForNotification->isNotEmpty()) {
                foreach ($postsForNotification as $p) {
                    if (NotificationService::isNotificationAllowed()) {
                        $fcmIds = NotificationService::getFcmTokensForPost($p);

                        if (! empty($fcmIds)) {
                            event(new SendNotification($p->title, $p->description, $p->image, $p->slug, $fcmIds, $p->news_language_id));
                        }
                    } else {
                        Log::info("Daily notification limit reached. Skipping automatic notification for: " . $p->title);
                        break; // Stop sending if daily limit reached
                    }
                }
            }
        }

        return $results;
    }

    public function extractImageFromContent($contentEncoded)
    {
        $image = null;

        if ($contentEncoded) {
            // Match the first <img> tag and get src
            if (preg_match(
                '/<img[^>]+(?:src|data-src)=["\']([^"\']+\.(jpg|jpeg|png|webp|gif|svg))["\']/i',
                $contentEncoded,
                $matches
            )) {
                return $matches[1];
            }
        }

        return $image;
    }

    /**
     * Get feed items based on feed type (RSS or Atom)
     */
    public function getFeedItems($feedData)
    {
        // Check if it's an Atom feed
        if (isset($feedData->entry)) {
            return $feedData->entry;
        }

        // Default to RSS format
        return $feedData->channel->item ?? [];
    }

    /**
     * Extract publication date from different feed formats
     */
    public function extractPubDate($item)
    {
        // For Atom feeds
        if (isset($item->published)) {
            return trim(strip_tags((string) $item->published));
        }

        // For RSS feeds
        if (isset($item->pubDate)) {
            return trim(strip_tags((string) $item->pubDate));
        }

        // Fallback to current date
        return Carbon::now()->toDateTimeString();
    }

    public function extractVideoData($item, $namespaces)
    {
        $videoData = [
            'video_url'      => null,
            'video_embed'    => null,
            'video_type'     => null,
            'is_video'       => false,
            'thumbnail_url'  => null, // Add thumbnail support
            'is_short_video' => 0,    // Add shorts detection
            'description'    => null, // ✅ ADD THIS
        ];

        // First, check for alternate link with /shorts/ URL (YouTube provides this for Shorts)
        $alternateUrl = $this->extractAlternateLink($item);
        if ($alternateUrl && $this->isYouTubeShortsUrl($alternateUrl)) {
            // Extract video ID from Shorts URL
            if (preg_match('/youtube\.com\/shorts\/([a-zA-Z0-9_-]+)/', $alternateUrl, $matches)) {
                $videoId                     = $matches[1];
                $videoData['video_url']      = $alternateUrl; // Use the actual Shorts URL
                $videoData['video_embed']    = "https://www.youtube.com/embed/" . $videoId;
                $videoData['video_type']     = 'youtube';
                $videoData['is_video']       = true;
                $videoData['is_short_video'] = 1;
                $videoData['thumbnail_url']  = "https://img.youtube.com/vi/" . $videoId . "/maxresdefault.jpg";
                return $videoData; // Return early since we found a Shorts URL
            }
        }

        /* ------------------------------------
     * 1. YouTube namespace (yt:videoId)
     * ------------------------------------ */
        // foreach ($namespaces as $prefix => $namespace) {
        //     if ($prefix === 'yt' && isset($item->children($namespace)->videoId)) {
        //         $videoId = (string) $item->children($namespace)->videoId;

        //         $videoData['video_url']     = "https://www.youtube.com/watch?v={$videoId}";
        //         $videoData['video_embed']   = "https://www.youtube.com/embed/{$videoId}";
        //         $videoData['video_type']    = 'youtube';
        //         $videoData['is_video']      = true;
        //         $videoData['thumbnail_url'] = "https://img.youtube.com/vi/{$videoId}/maxresdefault.jpg";
        //         break;
        //     }
        // }

        foreach ($namespaces as $prefix => $namespace) {
            if ($prefix === 'yt' && isset($item->children($namespace)->videoId)) {
                $videoId = (string) $item->children($namespace)->videoId;
                // Check alternate link again to see if this video is actually a Short
                if ($alternateUrl && $this->isYouTubeShortsUrl($alternateUrl)) {
                    $videoData['video_url']      = $alternateUrl;
                    $videoData['is_short_video'] = 1;
                } else {
                    $videoData['video_url'] = "https://www.youtube.com/watch?v=" . $videoId;
                }
                $videoData['video_embed']   = "https://www.youtube.com/embed/" . $videoId;
                $videoData['video_type']    = 'youtube';
                $videoData['is_video']      = true;
                $videoData['thumbnail_url'] = "https://img.youtube.com/vi/" . $videoId . "/maxresdefault.jpg";
                break;
            }
        }
        /* ------------------------------------
     * 2. media:content (video)
     * ------------------------------------ */
        // if (isset($item->children('media', true)->content)) {
        //     foreach ($item->children('media', true)->content as $mediaContent) {
        //         $type = (string) $mediaContent->attributes()->type;

        //         if (strpos($type, 'video') !== false || $type === 'application/x-shockwave-flash') {
        //             $url = (string) $mediaContent->attributes()->url;

        //             if (preg_match('/youtube\.com\/v\/([a-zA-Z0-9_-]+)/', $url, $matches)) {
        //                 $videoId = $matches[1];

        //                 $videoData['video_url']     = "https://www.youtube.com/watch?v={$videoId}";
        //                 $videoData['video_embed']   = "https://www.youtube.com/embed/{$videoId}";
        //                 $videoData['video_type']    = 'youtube';
        //                  $videoData['is_video']      = true;
        //                 $videoData['thumbnail_url'] = "https://img.youtube.com/vi/{$videoId}/maxresdefault.jpg";
        //             } else {
        //                 $videoData['video_url']   = $url;
        //                 $videoData['video_embed'] = $url;
        //                 $videoData['video_type']  = $this->detectVideoType($url);
        //             }

        //             $videoData['is_video'] = true;
        //             break;
        //         }
        //     }
        // }

        if (isset($item->children('media', true)->content)) {
            foreach ($item->children('media', true)->content as $mediaContent) {
                $type = (string) $mediaContent->attributes()->type;
                if (strpos($type, 'video') !== false || $type === 'application/x-shockwave-flash') {
                    $url = (string) $mediaContent->attributes()->url;

                    // Extract video ID from YouTube URLs and detect shorts
                    if (preg_match('/youtube\.com\/v\/([a-zA-Z0-9_-]+)/', $url, $matches)) {
                        $videoId                    = $matches[1];
                        $videoData['video_url']     = "https://www.youtube.com/watch?v=" . $videoId;
                        $videoData['video_embed']   = "https://www.youtube.com/embed/" . $videoId;
                        $videoData['video_type']    = 'youtube';
                        $videoData['is_video']      = true;
                        $videoData['thumbnail_url'] = "https://img.youtube.com/vi/" . $videoId . "/maxresdefault.jpg";
                    } elseif (preg_match('/youtube\.com\/shorts\/([a-zA-Z0-9_-]+)/', $url, $matches)) {
                        // Detect YouTube Shorts
                        $videoId                     = $matches[1];
                        $videoData['video_url']      = "https://www.youtube.com/shorts/" . $videoId;
                        $videoData['video_embed']    = "https://www.youtube.com/embed/" . $videoId;
                        $videoData['video_type']     = 'youtube';
                        $videoData['is_video']       = true;
                        $videoData['is_short_video'] = 1;
                        $videoData['thumbnail_url']  = "https://img.youtube.com/vi/" . $videoId . "/maxresdefault.jpg";
                    } else {
                        $videoData['video_url']   = $url;
                        $videoData['video_embed'] = $url;
                        $videoData['video_type']  = $this->detectVideoType($url);
                        $videoData['is_video']    = true;
                    }
                    break;
                }
            }
        }

        /* ------------------------------------
     * 3. media:description
     * ------------------------------------ */
        if (! $videoData['description'] && isset($item->children('media', true)->description)) {
            $videoData['description'] = trim(
                strip_tags((string) $item->children('media', true)->description)
            );
        }

        /* ------------------------------------
     * 4. media:group > media:description
     * ------------------------------------ */
        if (! $videoData['description'] && isset($item->children('media', true)->group)) {
            $mediaGroup = $item->children('media', true)->group;

            if (isset($mediaGroup->description)) {
                $videoData['description'] = trim(
                    strip_tags((string) $mediaGroup->description)
                );
            }
        }

        /* ------------------------------------
     * 5. RSS <description>
     * ------------------------------------ */
        if (! $videoData['description'] && isset($item->description)) {
            $videoData['description'] = trim(
                strip_tags((string) $item->description)
            );
        }

        /* ------------------------------------
     * 6. content:encoded (WordPress feeds)
     * ------------------------------------ */
        if (! $videoData['description'] && isset($item->children('content', true)->encoded)) {
            $videoData['description'] = trim(
                strip_tags((string) $item->children('content', true)->encoded)
            );
        }

        /* ------------------------------------
     * 7. Atom <summary>
     * ------------------------------------ */
        if (! $videoData['description'] && isset($item->summary)) {
            $videoData['description'] = trim(
                strip_tags((string) $item->summary)
            );
        }

        /* ------------------------------------
     * 8. media:thumbnail
     * ------------------------------------ */
        if (isset($item->children('media', true)->thumbnail)) {
            $bestThumbnail = null;
            $maxWidth      = 0;

            foreach ($item->children('media', true)->thumbnail as $thumbnail) {
                $width = (int) $thumbnail->attributes()->width;
                $url   = (string) $thumbnail->attributes()->url;

                if ($width > $maxWidth || ! $bestThumbnail) {
                    $maxWidth      = $width;
                    $bestThumbnail = $url;
                }
            }

            if ($bestThumbnail) {
                $videoData['thumbnail_url'] = $bestThumbnail;
            }
        }

        /* ------------------------------------
     * 9. enclosure (video)
     * ------------------------------------ */
        if (isset($item->enclosure)) {
            $type = (string) $item->enclosure['type'];
            if (strpos($type, 'video') !== false) {
                $url = (string) $item->enclosure['url'];

                $videoData['video_url']   = $url;
                $videoData['video_embed'] = $url;
                $videoData['video_type']  = $this->detectVideoType($url);
                $videoData['is_video']    = true;

                // Check if it's a YouTube Shorts URL
                if ($this->isYouTubeShortsUrl($url)) {
                    $videoData['is_short_video'] = 1;
                }
            }
        }

        /* ------------------------------------
     * 10. Link fallback
     * ------------------------------------ */
        if (! $videoData['is_video']) {
            $link = (string) $item->link;

            if ($this->isVideoUrl($link)) {
                $videoData['video_url']  = $link;
                $videoData['video_type'] = $this->detectVideoType($link);
                $videoData['is_video']   = true;

                // Check if it's a YouTube Shorts URL
                if ($this->isYouTubeShortsUrl($link)) {
                    $videoData['is_short_video'] = 1;
                }

                $embedUrl = $this->generateEmbedUrl($link, $videoData['video_type']);
                if ($embedUrl) {
                    $videoData['video_embed'] = $embedUrl;
                }

                if ($videoData['video_type'] === 'youtube' && ! $videoData['thumbnail_url']) {
                    $videoData['thumbnail_url'] = $this->getYouTubeThumbnail($link);
                }
            }
        }

        // Fallback: Check for any image in media:content as potential thumbnail
        if (! $videoData['thumbnail_url'] && isset($item->children('media', true)->content)) {
            foreach ($item->children('media', true)->content as $mediaContent) {
                $type = (string) $mediaContent->attributes()->type;
                if (strpos($type, 'image') !== false) {
                    $videoData['thumbnail_url'] = (string) $mediaContent->attributes()->url;
                    break;
                }
            }
        }

        return $videoData;
    }

    /**
     * Extract alternate link from feed item (YouTube uses this for Shorts URLs)
     */
    private function extractAlternateLink($item)
    {
        // Check for <link rel="alternate"> tag
        if (isset($item->link)) {
            // Handle multiple link elements
            if (is_array($item->link) || $item->link instanceof \Traversable) {
                foreach ($item->link as $link) {
                    $rel  = isset($link['rel']) ? (string) $link['rel'] : '';
                    $href = isset($link['href']) ? (string) $link['href'] : '';

                    if ($rel === 'alternate' && ! empty($href)) {
                        return $href;
                    }
                }
            } else {
                // Single link element with attributes
                if (isset($item->link->attributes()->rel) &&
                    (string) $item->link->attributes()->rel === 'alternate') {
                    return (string) $item->link->attributes()->href;
                }
            }
        }

        return null;
    }

    /**
     * Check if URL is a YouTube Shorts URL
     */
    private function isYouTubeShortsUrl($url)
    {
        return strpos($url, '/shorts/') !== false;
    }

    // Helper method to extract YouTube thumbnail from URL
    private function getYouTubeThumbnail($url)
    {
        // Extract video ID from various YouTube URL formats
        $patterns = [
            '/youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/',
            '/youtube\.com\/embed\/([a-zA-Z0-9_-]+)/',
            '/youtu\.be\/([a-zA-Z0-9_-]+)/',
            '/youtube\.com\/v\/([a-zA-Z0-9_-]+)/',
            '/youtube\.com\/shorts\/([a-zA-Z0-9_-]+)/', // Add shorts pattern
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url, $matches)) {
                return "https://img.youtube.com/vi/" . $matches[1] . "/maxresdefault.jpg";
            }
        }

        return null;
    }

    /**
     * Check if URL is a video URL
     */
    public function isVideoUrl($url)
    {
        $videoPatterns = [
            'youtube.com',
            'youtu.be',
            'vimeo.com',
            'dailymotion.com',
            'twitch.tv',
            'facebook.com/watch',
            'instagram.com/p/',
            'tiktok.com',
        ];

        foreach ($videoPatterns as $pattern) {
            if (strpos($url, $pattern) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Detect video type from URL
     */
    public function detectVideoType($url)
    {
        if (strpos($url, 'youtube.com') !== false || strpos($url, 'youtu.be') !== false) {
            return 'youtube';
        } elseif (strpos($url, 'vimeo.com') !== false) {
            return 'vimeo';
        } elseif (strpos($url, 'dailymotion.com') !== false) {
            return 'dailymotion';
        } elseif (strpos($url, 'twitch.tv') !== false) {
            return 'twitch';
        } elseif (strpos($url, 'facebook.com') !== false) {
            return 'facebook';
        } elseif (strpos($url, 'instagram.com') !== false) {
            return 'instagram';
        } elseif (strpos($url, 'tiktok.com') !== false) {
            return 'tiktok';
        }

        return 'other';
    }

    /**
     * Generate embed URL for supported video platforms
     */
    private function generateEmbedUrl($url, $type)
    {
        switch ($type) {
            case 'youtube':
                // Extract video ID from various YouTube URL formats including Shorts
                preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?|shorts)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $url, $matches);
                if (isset($matches[1])) {
                    return "https://www.youtube.com/embed/" . $matches[1];
                }
                break;

            case 'vimeo':
                preg_match('/vimeo\.com\/(\d+)/', $url, $matches);
                if (isset($matches[1])) {
                    return "https://player.vimeo.com/video/" . $matches[1];
                }
                break;

            case 'dailymotion':
                preg_match('/dailymotion\.com\/video\/([a-zA-Z0-9]+)/', $url, $matches);
                if (isset($matches[1])) {
                    return "https://www.dailymotion.com/embed/video/" . $matches[1];
                }
                break;
        }

        return null;
    }

    public function extractImageUrl($item, $namespaces)
    {
        // Check for thumbnail in namespaces
        foreach ($namespaces as $prefix => $namespace) {
            if (isset($item->children($namespace)->thumbnail)) {
                $data = (string) $item->children($namespace)->thumbnail->attributes()->url;
            }
        }

        // Check for enclosure URL
        if (isset($item->enclosure['url'])) {
            $data = (string) $item->enclosure['url'];
        }

        // Check for media:content URL
        if (isset($item->children('media', true)->content)) {
            foreach ($item->children('media', true)->content as $mediaContent) {
                if (isset($item->children('media', true)->content)) {
                    foreach ($item->children('media', true)->content as $mediaContent) {
                        $type = (string) $mediaContent->attributes()->type;
                        $url  = (string) $mediaContent->attributes()->url;

                        if (
                            str_starts_with($type, 'image/') ||
                            preg_match('/\.(jpg|jpeg|png|webp|gif|svg)$/i', $url)
                        ) {
                            return $url;
                        }
                    }
                }

            }
        }

        // Check for img tag in description
        if (isset($item->description)) {
            $matches = [];
            preg_match(
                '/<img[^>]+(?:src|data-src)=["\']([^"\']+\.(jpg|jpeg|png|webp|gif|svg))["\']/i',
                (string) $item->description,
                $matches
            );

            if (! empty($matches[1])) {
                return $matches[1];
            }
        }

        return $data ?? null;
    }

    /**
     * Check if the selected description_type is available in the RSS feed item.
     * Returns true if the description source exists, false otherwise.
     * If no description_type is selected (null), returns true to allow processing.
     */
    private function isDescriptionTypeAvailable($item, $descriptionType, $namespaces)
    {
        // If no specific description_type is selected, allow all items
        if (empty($descriptionType)) {
            return true;
        }

        switch ($descriptionType) {
            case 'description-tag':
                // Check if <description> tag exists and has non-empty content
                if (isset($item->description)) {
                    $description = trim(strip_tags((string) $item->description));
                    return ! empty($description);
                }
                return false;

            case 'content-encoded':
                // Check if <content:encoded> tag exists and has non-empty content
                if (isset($item->children('content', true)->encoded)) {
                    $content = trim(strip_tags((string) $item->children('content', true)->encoded));
                    return ! empty($content);
                }
                return false;

            case 'media:description':
                // Check if <media:description> exists (direct or inside <media:group>)
                if (isset($item->children('media', true)->description)) {
                    $mediaDesc = trim(strip_tags((string) $item->children('media', true)->description));
                    if (! empty($mediaDesc)) {
                        return true;
                    }
                }
                // Also check inside <media:group>
                if (isset($item->children('media', true)->group)) {
                    $mediaGroup = $item->children('media', true)->group;
                    if (isset($mediaGroup->description)) {
                        $groupDesc = trim(strip_tags((string) $mediaGroup->description));
                        if (! empty($groupDesc)) {
                            return true;
                        }
                    }
                }
                return false;

            default:
                // Unknown description_type, allow processing
                return true;
        }
    }
}
