<?php
namespace App\Http\Controllers;

use App\Models\ChannelSubscriber;
use App\Models\ENewspaper;
use App\Models\Language;
use App\Models\NewsLanguage;
use App\Models\NewsLanguageSubscriber;
use App\Models\Post;
use App\Models\Setting;
use App\Models\SmartAd;
use App\Models\SmartAdTracking;
use App\Models\Story;
use App\Models\Topic;
use App\Providers\AppServiceProvider;
use App\Traits\SelectsFields;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

const IMAGE_PATH     = 'public/front_end/classic/images/default/post-placeholder.jpg';
const CHANNELS_TABEL = 'channel:id,name,logo,slug';
const TOPICS_TABEL   = 'topic:id,name,slug';

class HomeController extends Controller
{
    use SelectsFields;

    const TIME_FORMATE = 'Y-m-d H:i';
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $defaultImageSetting = Setting::firstOrCreate(
            ['name' => 'default_image'],
            [
                'value' => 'front_end/classic/images/default/newspaper-advertising-service-500x500-1.png',
                'type'  => 'image',
            ]
        );

        $this->usersCount($request);
        $defaultImage = Setting::where('name', 'default_image')->first()->value ?? null;
        $title        = __('frontend-labels.home.title');
        $userId       = Auth::user()->id ?? 0;

        if ($userId) {
            $subscribedLanguageIds = NewsLanguageSubscriber::where('user_id', $userId)->pluck('news_language_id');
        } else {
            $sessionLanguageId = session('selected_news_language');
            if ($sessionLanguageId) {
                // If user selected a language, use it (even if not active)
                $subscribedLanguageIds = collect([$sessionLanguageId]);
            } else {
                // If not selected, use the first active language
                $defaultActiveLanguage = NewsLanguage::where('is_active', 1)->first();
                $subscribedLanguageIds = $defaultActiveLanguage ? collect([$defaultActiveLanguage->id]) : collect();
            }
        }

        // <><><><><><><> Get all topics with posts <><><><><>
        $frontTopics = Topic::select('id', 'name', 'slug')
            ->where('status', 'active')
            ->whereHas('posts')
            ->take(13)
            ->get();

        foreach ($frontTopics as $topic) {

            $postsQuery = Post::select(
                'posts.id',
                'posts.image',
                'posts.title',
                'posts.type',
                'posts.view_count',
                'posts.reaction',
                'posts.video_thumb',
                'posts.slug',
                'posts.status',
                'comment',
                'publish_date',
                'pubdate',
                'channels.name',
                'channels.logo',
                'channels.slug as channel_slug'
            )
                ->join('channels', 'posts.channel_id', '=', 'channels.id')
                ->where('topic_id', $topic->id)
                ->where('posts.status', 'active')
                ->whereHas('channel', function ($query) {
                    $query->where('status', 'active');
                })
                ->whereHas('topic', function ($q) {
                    $q->where('status', 'active');
                });

            if ($subscribedLanguageIds->isNotEmpty()) {
                $postsQuery->whereIn('posts.news_language_id', $subscribedLanguageIds);
            }

            $topic->posts = $postsQuery->orderBy('publish_date', 'DESC')
                ->take(4)
                ->get()
                ->map(function ($item) use ($defaultImage) {
                    $item->image             = $item->image ?? $defaultImage;
                    $item->publish_date_news = Carbon::parse($item->publish_date)->format(self::TIME_FORMATE);
                    if ($item->publish_date) {
                        $item->publish_date = Carbon::parse($item->publish_date_news)->diffForHumans();
                    } elseif ($item->pubdate) {
                        $item->pubdate = Carbon::parse($item->pubdate)->diffForHumans();
                    }

                    return $item;
                });
        }

        // <><><><><><><> Get all news language subscribers <><><><><>
        $top_posts_query = Post::with([CHANNELS_TABEL, TOPICS_TABEL])
            ->select('id', 'title', 'slug', 'channel_id', 'image', 'pubdate', 'view_count', 'comment', 'status', 'type', 'video_thumb')
            ->where('posts.status', 'active')
            ->whereHas('channel', function ($query) {
                $query->where('status', 'active');
            })
            ->where(function ($q) {
                $q->whereHas('topic', function ($query) {
                    $query->where('status', 'active');
                })
                    ->orWhereDoesntHave('topic'); // 👈 makes topic optional
            });

        if ($subscribedLanguageIds->isNotEmpty()) {
            $top_posts_query->whereIn('news_language_id', $subscribedLanguageIds);
        }
        $top_posts = $top_posts_query
            ->orderBy('publish_date', 'desc')
            ->take(10)
            ->get()
            ->map(function ($post) {
                $post->publish_date_news = Carbon::parse($post->pubdate)->format(self::TIME_FORMATE);
                if ($post->publish_date) {
                    $post->publish_date = Carbon::parse($post->publish_date)->diffForHumans();
                } elseif ($post->pubdate) {
                    $post->pubdate = Carbon::parse($post->pubdate)->diffForHumans();
                }
                return $post;
            });

        // <><><><><><><> Fetch post banners with channel and topic details <><><><><>
        $postBanners = $this->getPostsWithBanners(7);

        // <><><><><><><> Fetch most read posts <><><><><>
        $mostReadsQuery = Post::with(['channel:id,name,slug'])
            ->select('id', 'title', 'slug', 'channel_id', 'image', 'pubdate', 'view_count', 'comment', 'reaction', 'status', 'type', 'video_thumb')
            ->whereHas('channel', function ($query) {
                $query->where('status', 'active');
            })
        // ->whereHas('topic', function ($q) {
        //     $q->where('status', 'active');
        // })
            ->where(function ($q) {
                $q->whereHas('topic', function ($query) {
                    $query->where('status', 'active');
                })
                    ->orWhereDoesntHave('topic'); // 👈 makes topic optional
            })
            ->where('posts.status', 'active');

        if ($subscribedLanguageIds->isNotEmpty()) {
            $mostReadsQuery->whereIn('posts.news_language_id', $subscribedLanguageIds);
        }

        $mostReads = $mostReadsQuery
            ->orderBy('view_count', 'desc')
            ->take(20)
            ->get()
            ->map(function ($post) use ($defaultImage) {
                $post->image             = $post->image ?? $defaultImage;
                $post->publish_date_news = Carbon::parse($post->pubdate)->format(self::TIME_FORMATE);
                if ($post->publish_date) {
                    $post->publish_date = Carbon::parse($post->publish_date)->diffForHumans();
                } elseif ($post->pubdate) {
                    $post->pubdate = Carbon::parse($post->pubdate)->diffForHumans();
                }
                return $post;
            });

        // Followed Channels
        $channel_ids = ChannelSubscriber::where('user_id', $userId)->pluck('channel_id')->toArray();

        if (! empty($channel_ids)) {
            $channelFollowedQuery = Post::with(['channel:id,name,slug'])
                ->select('id', 'title', 'slug', 'channel_id', 'image', 'pubdate', 'view_count', 'comment', 'reaction', 'status', 'type', 'video_thumb')
                ->whereHas('channel', function ($query) {
                    $query->where('status', 'active');
                })
            // ->whereHas('topic', function ($q) {
            //     $q->where('status', 'active');
            // })
                ->where(function ($q) {
                    $q->whereHas('topic', function ($query) {
                        $query->where('status', 'active');
                    })
                        ->orWhereDoesntHave('topic'); // 👈 makes topic optional
                })
                ->where('posts.status', 'active');

            if ($subscribedLanguageIds->isNotEmpty()) {
                $channelFollowedQuery->whereIn('posts.news_language_id', $subscribedLanguageIds);
            }

            $channelFollowed = $channelFollowedQuery
                ->orderBy('publish_date', 'desc')
                ->take(20)
                ->get()
                ->map(function ($post) use ($defaultImage) {
                    $post->image             = $post->image ?? $defaultImage;
                    $post->publish_date_news = Carbon::parse($post->pubdate)->format(self::TIME_FORMATE);
                    if ($post->publish_date) {
                        $post->publish_date = Carbon::parse($post->publish_date)->diffForHumans();
                    } elseif ($post->pubdate) {
                        $post->pubdate = Carbon::parse($post->pubdate)->diffForHumans();
                    }

                    return $post;
                });
        } else {
            $channelFollowed = [];
        }

        // <><><><><><><> Fetch latest news posts <><><><><>
        $latesNewsQuery = Post::with([CHANNELS_TABEL, TOPICS_TABEL])
            ->whereHas('channel', function ($query) {
                $query->where('status', 'active');
            })
        // ->whereHas('topic', function ($q) {
        //     $q->where('status', 'active');
        // })
            ->where(function ($q) {
                $q->whereHas('topic', function ($query) {
                    $query->where('status', 'active');
                })
                    ->orWhereDoesntHave('topic'); // 👈 makes topic optional
            })
            ->where('posts.status', 'active');

        if ($subscribedLanguageIds->isNotEmpty()) {
            $latesNewsQuery->whereIn('posts.news_language_id', $subscribedLanguageIds);
        }
        $latesNews = $latesNewsQuery
            ->orderBy('publish_date', 'desc')
            ->take(12)
            ->get()
            ->map(function ($post) use ($defaultImage) {
                $post->image = $post->image ?? $defaultImage;
                // Fallback: if image is null, use video_thumb
                if (empty($post->image) && ! empty($post->video_thumb)) {
                    $post->image = $post->video_thumb;
                }
                $post->publish_date_news = Carbon::parse($post->pubdate)->format(self::TIME_FORMATE);
                if ($post->publish_date) {
                    $post->publish_date = Carbon::parse($post->publish_date)->diffForHumans();
                } elseif ($post->pubdate) {
                    $post->pubdate = Carbon::parse($post->pubdate)->diffForHumans();
                }

                return $post;
            });

        // <><><><><><><> Fetch Popular news posts <><><><><>
        $popularPostsQuery = Post::with(['channel:id,name,slug'])
            ->select('id', 'title', 'slug', 'channel_id', 'image', 'pubdate', 'view_count', 'comment', 'status')
            ->whereHas('channel', function ($query) {
                $query->where('status', 'active');
            })
        // ->whereHas('topic', function ($q) {
        //     $q->where('status', 'active');
        // })
            ->where(function ($q) {
                $q->whereHas('topic', function ($query) {
                    $query->where('status', 'active');
                })
                    ->orWhereDoesntHave('topic'); // 👈 makes topic optional
            })
            ->where('posts.status', 'active');

        if ($subscribedLanguageIds->isNotEmpty()) {
            $popularPostsQuery->whereIn('posts.news_language_id', $subscribedLanguageIds);
        }

        $popularPosts = $popularPostsQuery
            ->orderBy('view_count', 'desc')
            ->take(4)
            ->get()
            ->map(function ($item) {
                $item->pubdate_news = Carbon::parse($item->pubdate)->format(self::TIME_FORMATE);
                if ($item->publish_date) {
                    $item->publish_date = Carbon::parse($item->publish_date)->diffForHumans();
                } elseif ($item->pubdate) {
                    $item->pubdate = Carbon::parse($item->pubdate)->diffForHumans();
                }
                return $item;
            });

        // <><><><><><><> Fetch weather API key <><><><><>
        $weather_api_key    = Setting::select('value')->where('name', 'weather_api_key')->first();
        $weatherCardStatus  = Setting::select('value')->where('name', 'weather_card_status')->first();
        $cookiesPopupStatus = Setting::select('value')->where('name', 'cookies_popup_status')->first();

        $videoPosts = $this->getPostsWithVideos(4);
        $audioPosts = $this->getPostsWithAudios(4);
        $location   = 'bhuj';
        $latitude   = '23d2469d67';
        $longitude  = '69d67';

        // <><><><><><><> Fetch stories <><><><><>
        $stories   = $this->getStoriesWithLanguages($subscribedLanguageIds);
        $magazines = $this->getMagazinesWithLanguages($subscribedLanguageIds);

        $enewspapers = $this->getEnewspapersWithLanguages($subscribedLanguageIds);

        $socialsettings   = Setting::pluck('value', 'name');
        $getEnewsSettings = $this->getEnewsSettings();

        // Check limits without incrementing
        $dailyLimitReached        = false;
        $subscriptionLimitReached = false;

        $user         = auth()->user();
        $subscription = $user ? $user->subscription : null;

        if ($subscription) {
            if ($subscription->hasReachedEPaperLimits()) {
                $subscriptionLimitReached = true;
                session(['show_modal' => 'subscription']);
            }
        } else {
            $freeTrialLimit = Setting::where('name', 'free_trial_e_papers_and_magazines_limit')->value('value') ?? 5;

            $freeTrialLimit = (int) $freeTrialLimit;

            $sessionKey   = 'free_tier_e_paper_count';
            $currentCount = session($sessionKey, 0);

            if ($freeTrialLimit !== -1 && $currentCount >= $freeTrialLimit) {
                $dailyLimitReached = true;
                session(['show_modal' => 'daily']);
            }
        }

        $theme = getTheme();
        $data  = compact('title', 'top_posts', 'postBanners', 'dailyLimitReached', 'subscriptionLimitReached', 'magazines', 'getEnewsSettings', 'enewspapers', 'socialsettings', 'defaultImage', 'frontTopics', 'latesNews', 'videoPosts', 'audioPosts', 'mostReads', 'popularPosts', 'theme', 'location', 'latitude', 'longitude', 'weather_api_key', 'stories', 'channelFollowed', 'cookiesPopupStatus', 'weatherCardStatus');
        return view('front_end/' . $theme . '/pages/index', $data);
    }

    public function trackClick(Request $request)
    {
        $request->validate([
            'ad_id' => 'required|integer|exists:smart_ads,id',
        ]);

        $adId      = $request->ad_id;
        $userIp    = $request->ip();
        $userAgent = $request->header('User-Agent');
        $sessionId = session()->getId();

        // Create a unique identifier for this click attempt
        $clickKey = md5($adId . $userIp . $userAgent . $sessionId);

        // Check if this exact click was already tracked in the last 5 seconds
        $recentClick = Cache::get("click_" . $clickKey);
        if ($recentClick) {
            return response()->json([
                'success' => false,
                'message' => 'Duplicate click detected',
            ]);
        }

        // Mark this click as tracked for 5 seconds
        Cache::put("click_" . $clickKey, true, 5);

        try {
            DB::beginTransaction();

            // Update total clicks on smart_ads table
            $ad = SmartAd::findOrFail($adId);
            $ad->increment('clicks');

            // Update / create tracking record
            $tracking = SmartAdTracking::firstOrCreate(
                ['smart_ad_id' => $adId],
                ['ad_clicks' => [], 'totalClicks' => 0]
            );

            // Add timestamp to ad_clicks array
            $clicks   = $tracking->ad_clicks ?? [];
            $clicks[] = [
                'timestamp'  => now()->toDateTimeString(),
                'ip'         => $userIp,
                'user_agent' => substr($userAgent, 0, 255), // Limit length
            ];

            $tracking->ad_clicks   = $clicks;
            $tracking->totalClicks = $tracking->totalClicks + 1;
            $tracking->save();

            DB::commit();

            return response()->json([
                'success'      => true,
                'total_clicks' => $ad->fresh()->clicks,
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Ad click tracking failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Click tracking failed',
            ], 500);
        }
    }

    public function getRandomAdByPlacement($placementKey)
    {
        $ad = DB::table('smart_ad_placements as sap')
            ->join('smart_ads as sa', 'sap.smart_ad_id', '=', 'sa.id')
            ->join('smart_ads_details as sad', 'sap.smart_ad_id', '=', 'sad.smart_ad_id')
            ->where('sap.placement_key', $placementKey)
            ->where('sad.ad_publish_status', 'approved')
            ->where('sad.payment_status', 'success')
            ->where('sap.start_date', '<=', now())
            ->where('sap.end_date', '>=', now())
            ->inRandomOrder()
            ->select(
                'sa.id as smart_ad_id',
                'sa.name',
                'sa.body',
                'sa.adType as ad_type',
                'sa.horizontal_image',
                'sa.vertical_image',
                'sa.imageUrl',
                'sa.imageAlt as image_alt',
                'sap.start_date',
                'sap.end_date'
            )
            ->first();

        if ($ad) {
            DB::table('smart_ads')->where('id', $ad->smart_ad_id)->increment('views');

            return response()->json([
                "id"               => "ad_" . $ad->smart_ad_id,
                "title"            => $ad->name,
                "description"      => $ad->body,
                "horizontal_image" => $ad->horizontal_image ? url('storage/' . $ad->horizontal_image) : null,
                "vertical_image"   => $ad->vertical_image ? url('storage/' . $ad->vertical_image) : null,
                "imageUrl"         => $ad->imageUrl,
                "image_alt"        => $ad->image_alt,
                "placement_key"    => $placementKey,
            ]);
        }

        return response()->json(null);
    }

    private function getEnewsSettings()
    {
        $settings = Setting::whereIn('name', [
            'enews_paper_image',
            'enews_paper_title',
        ])->pluck('value', 'name');

        return [
            'paperimage' => isset($settings['enews_paper_image']) && $settings['enews_paper_image']
                ? url('storage/' . $settings['enews_paper_image'])
                : asset('front_end/classic/images/default/newspaper-advertising-service-500x500-1.png'),

            'papertitle' => $settings['enews_paper_title'] ?? 'Newshunt',
        ];
    }

    public function usersCount($request)
    {
        $userId     = Auth::id();
        $visitorKey = $userId ?? $request->ip();
        $cookieName = 'active_user_' . $visitorKey;
        if (! Cookie::has($cookieName)) {
            Cookie::queue($cookieName, true, 21600);
            $setting = Setting::firstOrCreate(
                ['name' => 'active_user_count'],
                ['value' => 0, 'type' => 'number']
            );

            $setting->increment('value');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function getRandomAdBanner()
    {
        $ad = DB::table('smart_ad_placements as sap')
            ->join('smart_ads as sa', 'sap.smart_ad_id', '=', 'sa.id')
            ->join('smart_ads_details as sad', 'sap.smart_ad_id', '=', 'sad.smart_ad_id')
            ->where('sap.placement_key', 'banner_slider')
            ->where('sad.ad_publish_status', 'approved')
            ->where('sad.payment_status', 'success')
            ->where('sap.start_date', '<=', now())
            ->where('sap.end_date', '>=', now())
            ->inRandomOrder()
            ->select(
                'sa.id as smart_ad_id',
                'sa.name',
                'sa.slug',
                'sa.body',
                'sa.adType as ad_type',
                'sa.vertical_image',
                'sa.horizontal_image',
                'sa.imageUrl',
                'sa.imageAlt as image_alt',
                'sa.views',
                'sa.clicks',
                'sa.created_at',
                'sad.contact_name',
                'sad.contact_email',
                'sad.contact_phone',
                'sap.start_date',
                'sap.end_date'
            )
            ->first();

        if ($ad) {
            // increment views
            DB::table('smart_ads')
                ->where('id', $ad->smart_ad_id)
                ->increment('views');

            return response()->json([
                "id"               => "ad_" . $ad->smart_ad_id,
                "smart_ad_id"      => $ad->smart_ad_id,
                "type"             => "ad",
                "name"             => $ad->name,
                "title"            => $ad->name,
                "description"      => $ad->body,
                "body"             => $ad->body,
                "vertical_image"   => $ad->vertical_image ? url('storage/' . $ad->vertical_image) : null,
                "horizontal_image" => $ad->horizontal_image ? url('storage/' . $ad->horizontal_image) : null,
                "image_alt"        => $ad->image_alt,
                "imageUrl"         => $ad->imageUrl,
                "ad_type"          => $ad->ad_type,
                "slug"             => $ad->slug,
                "views"            => $ad->views + 1,
                "clicks"           => $ad->clicks,
                "contact_info"     => [
                    "name"  => $ad->contact_name,
                    "email" => $ad->contact_email,
                    "phone" => $ad->contact_phone,
                ],
                "created_at"       => $ad->created_at,
                "publish_date"     => \Carbon\Carbon::parse($ad->created_at)->diffForHumans(),
            ]);
        }

        return response()->json(null);
    }

    private function getPostsWithBanners($limit)
    {
        $userId = Auth::id() ?? 0;

        // Get subscribed language IDs
        if ($userId) {
            $subscribedLanguageIds = NewsLanguageSubscriber::where('user_id', $userId)
                ->pluck('news_language_id');
        } else {
            $sessionLanguageId     = session('selected_news_language');
            $subscribedLanguageIds = $sessionLanguageId
                ? collect([$sessionLanguageId])
                : (NewsLanguage::where('is_active', 1)->first() ? collect([NewsLanguage::where('is_active', 1)->first()->id]) : collect());
        }

        $posts = Post::select(
            'posts.id',
            'posts.slug',
            'posts.image',
            'posts.comment',
            'channels.name as channel_name',
            'channels.logo as channel_logo',
            'channels.slug as channel_slug',
            'topics.name as topic_name',
            'topics.slug as topic_slug',
            'posts.title',
            'posts.favorite',
            'posts.description',
            'posts.status',
            'posts.publish_date',
            'posts.view_count',
            'posts.type',
            'posts.video_thumb',
            'posts.pubdate',
            'posts.reaction',
        )
            ->where('posts.status', 'active')
            ->join('channels', function ($join) {
                $join->on('posts.channel_id', '=', 'channels.id')
                    ->where('channels.status', 'active');
            })
            ->leftjoin('topics', function ($join) {
                $join->on('posts.topic_id', '=', 'topics.id')
                    ->where('topics.status', 'active');
            });

        if ($subscribedLanguageIds->isNotEmpty()) {
            $posts->whereIn('posts.news_language_id', $subscribedLanguageIds);
        }

        $posts = $posts->orderBy('posts.publish_date', 'DESC')->take($limit)->get();

        // Format publish dates and add type identifier
        $posts->transform(function ($post) {
            // Fallback: if image is null, use video_thumb
            if (empty($post->image) && ! empty($post->video_thumb)) {
                $post->image = $post->video_thumb;
            }
            if ($post->publish_date) {
                $post->publish_date_news = Carbon::parse($post->pubdate)->format(self::TIME_FORMATE);
                $post->publish_date      = Carbon::parse($post->publish_date)->diffForHumans();
            } elseif ($post->pubdate) {
                $post->pubdate = Carbon::parse($post->pubdate)->diffForHumans();
            }
            $post->item_type = 'post'; // Add identifier
            return $post;
        });

        // Get user's available smart ad placements for 'banner_slider'
        $userPlacement = DB::table('smart_ad_placements as sap')
            ->join('smart_ads as sa', 'sap.smart_ad_id', '=', 'sa.id')
            ->join('smart_ads_details as sad', 'sap.smart_ad_id', '=', 'sad.smart_ad_id')
            ->where('sap.placement_key', 'banner_slider')
            ->where('sad.ad_publish_status', 'approved')
            ->where('sad.payment_status', 'success')
            ->where('sap.start_date', '<=', now())
            ->where('sap.end_date', '>=', now())
            ->inRandomOrder()
            ->select(
                'sa.id as smart_ad_id',
                'sa.name',
                'sa.slug',
                'sa.body',
                'sa.adType as ad_type',
                'sa.vertical_image',
                'sa.horizontal_image',
                'sa.imageUrl',
                'sa.imageAlt as image_alt',
                'sa.views',
                'sa.clicks',
                'sa.created_at',
                'sad.contact_name',
                'sad.contact_email',
                'sad.contact_phone',
                'sap.start_date',
                'sap.end_date'
            )
            ->first(); // Get only one ad

        // Convert to collection of ads and add type identifier
        $ads = $userPlacement ? collect([$userPlacement])->map(function ($ad) {
            $ad->item_type = 'ad'; // Add identifier
            return $ad;
        }) : collect(); // Empty collection if no ad found

        // Insert ads at random positions in posts
        $final     = [];
        $postCount = $posts->count();
        $adCount   = $ads->count();

        if ($adCount > 0) {
            $positions = range(0, $postCount - 1);
            shuffle($positions);
            $selectedPositions = array_slice($positions, 0, min($adCount, $postCount));
        } else {
            $selectedPositions = [];
        }

        foreach ($posts as $index => $post) {
            $final[] = $post;

            // Add an ad at a random position if any left
            if ($ads->isNotEmpty() && in_array($index, $selectedPositions)) {
                $final[] = $ads->shift(); // Add first ad and remove from collection
            }
        }

        return $final;
    }

    private function getPostsWithVideos($limit)
    {
        $userId       = Auth::user()->id ?? 0;
        $defaultImage = Setting::where('name', 'default_image')->first()->value ?? null;

        // Determine subscribed language IDs
        if ($userId) {
            $subscribedLanguageIds = NewsLanguageSubscriber::where('user_id', $userId)->pluck('news_language_id');
        } else {
            $sessionLanguageId = session('selected_news_language');
            if ($sessionLanguageId) {
                // If user selected a language, use it (even if not active)
                $subscribedLanguageIds = collect([$sessionLanguageId]);
            } else {
                // If not selected, use the first active language
                $defaultActiveLanguage = NewsLanguage::where('is_active', 1)->first();
                $subscribedLanguageIds = $defaultActiveLanguage ? collect([$defaultActiveLanguage->id]) : collect();
            }
        }

        // Build the query for video posts
        $query = Post::select(
            'posts.title',
            'posts.slug',
            'posts.video_thumb',
            'posts.video',
            'posts.description',
            'posts.status',
            'posts.publish_date',
            'posts.view_count',
            'posts.reaction',
            'posts.shere',
            'posts.comment',
            'posts.pubdate',
            'posts.status',
            'posts.type'
        )
            ->whereIn('type', ['video', 'youtube'])
            ->where('posts.status', 'active')
        // ->join('channels', 'posts.channel_id', '=', 'channels.id')
        // ->whereHas('channel', function ($query) {
        //     $query->where('status', 'active');
        // })
        // ->whereHas('topic', function ($q) {
        //     $q->where('status', 'active');
        // });
            ->join('channels', function ($join) {
                $join->on('posts.channel_id', '=', 'channels.id')
                    ->where('channels.status', 'active');
            })
            ->leftjoin('topics', function ($join) {
                $join->on('posts.topic_id', '=', 'topics.id')
                    ->where('topics.status', 'active');
            });

        // Apply language filter if subscribed languages exist
        if ($subscribedLanguageIds->isNotEmpty()) {
            $query->whereIn('posts.news_language_id', $subscribedLanguageIds);
        }

        // Execute the query and format the results
        return $query->orderBy('posts.publish_date', 'DESC')
            ->take($limit)
            ->get()
            ->map(function ($post) use ($defaultImage) {
                $post->image             = $post->video_thumb ?? $defaultImage;
                $post->publish_date_news = Carbon::parse($post->pubdate)->format(self::TIME_FORMATE);
                if ($post->publish_date) {
                    $post->publish_date = Carbon::parse($post->publish_date)->diffForHumans();
                } elseif ($post->pubdate) {
                    $post->pubdate = Carbon::parse($post->pubdate)->diffForHumans();
                }
                return $post;
            });
    }

    private function getPostsWithAudios($limit)
    {
        $userId       = Auth::user()->id ?? 0;
        $defaultImage = Setting::where('name', 'default_image')->first()->value ?? null;

        // Determine subscribed language IDs
        if ($userId) {
            $subscribedLanguageIds = NewsLanguageSubscriber::where('user_id', $userId)->pluck('news_language_id');
        } else {
            $sessionLanguageId = session('selected_news_language');
            if ($sessionLanguageId) {
                // If user selected a language, use it (even if not active)
                $subscribedLanguageIds = collect([$sessionLanguageId]);
            } else {
                // If not selected, use the first active language
                $defaultActiveLanguage = NewsLanguage::where('is_active', 1)->first();
                $subscribedLanguageIds = $defaultActiveLanguage ? collect([$defaultActiveLanguage->id]) : collect();
            }
        }

        // Build the query for video posts
        $query = Post::select(
            'posts.title',
            'posts.slug',
            'posts.image',
            'posts.audio',
            'posts.description',
            'posts.status',
            'posts.publish_date',
            'posts.view_count',
            'posts.reaction',
            'posts.shere',
            'posts.comment',
            'posts.pubdate',
            'posts.status',
            'posts.type'
        )
            ->where('type', 'audio')
            ->where('posts.status', 'active')
        // ->join('channels', 'posts.channel_id', '=', 'channels.id')
        // ->join('topics', 'posts.topic_id', '=', 'topics.id')
        // ->whereHas('channel', function ($query) {
        //     $query->where('status', 'active');
        // })
        // ->whereHas('topic', function ($q) {
        //     $q->where('status', 'active');
        // });
            ->join('channels', function ($join) {
                $join->on('posts.channel_id', '=', 'channels.id')
                    ->where('channels.status', 'active');
            })
            ->leftjoin('topics', function ($join) {
                $join->on('posts.topic_id', '=', 'topics.id')
                    ->where('topics.status', 'active');
            });

        // Apply language filter if subscribed languages exist
        if ($subscribedLanguageIds->isNotEmpty()) {
            $query->whereIn('posts.news_language_id', $subscribedLanguageIds);
        }

        // Execute the query and format the results
        return $query->orderBy('posts.publish_date', 'DESC')
            ->take($limit)
            ->get()
            ->map(function ($post) use ($defaultImage) {
                $post->image             = $post->image ?? $defaultImage;
                $post->publish_date_news = Carbon::parse($post->pubdate)->format(self::TIME_FORMATE);
                if ($post->publish_date) {
                    $post->publish_date = Carbon::parse($post->publish_date)->diffForHumans();
                } elseif ($post->pubdate) {
                    $post->pubdate = Carbon::parse($post->pubdate)->diffForHumans();
                }
                return $post;
            });
    }

    public function setWebLanguage(Request $request)
    {
        $languageCode = $request->input('language_code');
        $language     = Language::where('code', $languageCode)->first();

        if (! empty($language)) {
            Session::put('web_locale', $language->code);
            Session::put('web_language', (object) $language->toArray());
            Session::save();

            return response()->json([
                'success' => true,
                'message' => __('frontend-labels.settings.language_changed_success'),
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Language not found']);
    }

    private function getStoriesWithLanguages($subscribedLanguageIds)
    {
        $query = Story::with(['story_slides', 'topic'])
            ->whereHas('story_slides')
            ->whereHas('topic', function ($q) {
                $q->where('status', 'active');
            })
            ->orderBy('created_at', 'desc');

        if ($subscribedLanguageIds->isNotEmpty()) {
            $query->whereIn('news_language_id', $subscribedLanguageIds);
        }

        return $query->get();
    }

    private function getMagazinesWithLanguages($subscribedLanguageIds)
    {
        $query = ENewspaper::with(['channel', 'newsLanguage'])
            ->whereIn('news_language_id', $subscribedLanguageIds)
            ->where('type', 'magazine')
            ->whereHas('channel', function ($query) {
                $query->where('status', 'active');
            })
            ->whereHas('topic', function ($q) {
                $q->where('status', 'active');
            })
            ->orderBy('date', 'desc');

        if ($subscribedLanguageIds->isNotEmpty()) {
            $query->whereIn('news_language_id', $subscribedLanguageIds);
        }

        return $query->take(4)->get();
    }

    private function getEnewspapersWithLanguages($subscribedLanguageIds)
    {
        $query = ENewspaper::with(['channel', 'newsLanguage'])
            ->whereIn('news_language_id', $subscribedLanguageIds)
            ->where('type', 'paper')
            ->whereHas('channel', function ($query) {
                $query->where('status', 'active');
            })
            ->whereHas('topic', function ($q) {
                $q->where('status', 'active');
            })
            ->orderBy('date', 'desc');

        if ($subscribedLanguageIds->isNotEmpty()) {
            $query->whereIn('news_language_id', $subscribedLanguageIds);
        }

        return $query->take(3)->get();
    }

    // Helper method to format publish_date
    private function formatPubdate($publish_date)
    {
        return $publish_date ? Carbon::parse($publish_date)->diffForHumans() : null;
    }

    public function themeNotFound()
    {
        $title   = '404 Not Found';
        $appName = Setting::where('name', 'app_name')->value('value') ?? 'News5';

        $theme = getTheme();
        $data  = compact('title', 'appName', 'theme');
        return view('front_end/' . $theme . '/pages/404', $data);
    }

    public function changeAuthPasswordViaEmail(Request $request)
    {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => [
                'required',
                'string',
                'confirmed',
                'min:8',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*?&]/',
            ],
        ], [
            'token.required'     => 'The token field is required.',
            'email.required'     => 'The email field is required.',
            'email.email'        => 'The email must be a valid email address.',
            'password.required'  => 'The password field is required.',
            'password.confirmed' => 'The password confirmation does not match.',
            'password.min'       => 'The password must be at least 8 characters.',
            'password.regex'     => 'The password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.',
        ]);

        $user = null;

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($foundUser, $password) use (&$user) {
                $foundUser->password = Hash::make($password);
                $foundUser->setRememberToken(Str::random(60));
                $foundUser->save();

                $user = $foundUser;
            }
        );

        if ($status == Password::PASSWORD_RESET) {

            // Redirect to home page with success message
            return redirect()->route('home')->with('success', 'Sua senha foi alterada com sucesso!');
        } else {
            return back()->withErrors(['email' => ['Falha ao redefinir a senha. Por favor, tente novamente.']]);
        }
    }

    public function previewAboutUs()
    {
        $title   = 'About Us';
        $appName = Setting::where('name', 'app_name')->value('value') ?? 'News5';

        // Fetch about_us content
        $about_us = Setting::select('name', 'value', 'updated_at')
            ->where('name', 'about_us')
            ->first();

        $theme = getTheme();
        $data  = compact('title', 'appName', 'theme', 'about_us');

        return view('front_end/' . $theme . '/pages/about_preview', $data);
    }

    public function previewPrivacyPolicies()
    {
        $title   = 'Privacy Policies';
        $appName = Setting::where('name', 'app_name')->value('value') ?? 'News5';

        // Fetch privacy_policies content
        $privacy_policies = Setting::select('name', 'value', 'updated_at')
            ->where('name', 'privacy_policy')
            ->first();

        $theme = getTheme();
        $data  = compact('title', 'appName', 'theme', 'privacy_policies');

        return view('front_end/' . $theme . '/pages/privacy_preview', $data);
    }

    public function previewTermsConditions()
    {
        $title   = 'Terms & Conditions';
        $appName = Setting::where('name', 'app_name')->value('value') ?? 'News5';

        // Fetch terms_conditions content
        $terms_conditions = Setting::select('name', 'value', 'updated_at')
            ->where('name', 'terms_conditions')
            ->first();

        $theme = getTheme();
        $data  = compact('title', 'appName', 'theme', 'terms_conditions');

        return view('front_end/' . $theme . '/pages/terms_preview', $data);
    }

    public function getChannelPosts($channelId)
    {
        try {
            $appServiceProvider = new AppServiceProvider(app());
            $posts              = $appServiceProvider->getChannelPostsData($channelId);

            return response()->json([
                'success' => true,
                'posts'   => $posts,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching posts: ' . $e->getMessage(),
            ], 500);
        }
    }

}
