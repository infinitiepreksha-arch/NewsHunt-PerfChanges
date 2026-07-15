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

    // changes done here by P
    private $allSettings = null;
    private $subscribedLanguageIds = null;
    private $defaultImage = null;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // changes done here by P
        // Initialize settings once
        if ($request->attributes->has('settings_cache')) {
            $this->allSettings = $request->attributes->get('settings_cache')->map(fn($item) => $item->value);
        } else {
            // Query DB directly to prevent Eloquent from instantiating 146 Setting models.
            $settingsList = \Illuminate\Support\Facades\DB::table('settings')->select('name', 'value', 'type')->get();
            $this->allSettings = $settingsList->mapWithKeys(function ($item) {
                $value = $item->value;
                if ($item->type === 'file') {
                    $value = !empty($value) ? url(\Illuminate\Support\Facades\Storage::url($value)) : '';
                }
                return [$item->name => $value];
            });
            $request->attributes->set('settings_cache', $settingsList->keyBy('name'));
        }
        
        // Ensure default image exists in db (similar to original Setting::firstOrCreate)
        if (!$this->allSettings->has('default_image')) {
            $defaultImageSetting = Setting::firstOrCreate(
                ['name' => 'default_image'],
                [
                    'value' => 'front_end/classic/images/default/newspaper-advertising-service-500x500-1.png',
                    'type'  => 'image',
                ]
            );
            $this->allSettings->put('default_image', $defaultImageSetting->value);
            $request->attributes->get('settings_cache')->put('default_image', (object)['value' => $defaultImageSetting->value]);
        }

        $this->usersCount($request);

        // changes done here by P
        // $defaultImage = Setting::where('name', 'default_image')->first()->value ?? null;
        $defaultImage = $this->allSettings->get('default_image');
        $this->defaultImage = $defaultImage;

        $title        = __('frontend-labels.home.title');
        $userId       = Auth::user()->id ?? 0;

        // changes done here by P
        /* OLD CODE:
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
        */
        if ($userId) {
            $subscribedLanguageIds = NewsLanguageSubscriber::where('user_id', $userId)->pluck('news_language_id');
            if ($subscribedLanguageIds->isEmpty()) {
                if ($request->attributes->has('active_language_cache')) {
                    $defaultLanguage = $request->attributes->get('active_language_cache');
                } else {
                    $defaultLanguage = NewsLanguage::where('is_active', 1)->first();
                    $request->attributes->set('active_language_cache', $defaultLanguage);
                }
                if ($defaultLanguage) {
                    NewsLanguageSubscriber::create([
                        'user_id'          => $userId,
                        'news_language_id' => $defaultLanguage->id,
                    ]);
                    $subscribedLanguageIds = collect([$defaultLanguage->id]);
                }
            }
        } else {
            $sessionLanguageId = session('selected_news_language');
            if ($sessionLanguageId) {
                $subscribedLanguageIds = collect([$sessionLanguageId]);
            } else {
                if ($request->attributes->has('active_language_cache')) {
                    $defaultActiveLanguage = $request->attributes->get('active_language_cache');
                } else {
                    $defaultActiveLanguage = NewsLanguage::where('is_active', 1)->first();
                    $request->attributes->set('active_language_cache', $defaultActiveLanguage);
                }
                $subscribedLanguageIds = $defaultActiveLanguage ? collect([$defaultActiveLanguage->id]) : collect();
            }
        }
        $this->subscribedLanguageIds = $subscribedLanguageIds;

        // <><><><><><><> Get all topics with posts <><><><><>
        $langKey = implode('_', $subscribedLanguageIds->toArray());

        $frontTopics = Cache::remember("home_topics_{$langKey}", 600, function() use ($subscribedLanguageIds, $defaultImage) {
            $frontTopics = Topic::select('id', 'name', 'slug')
                ->where('status', 'active')
                ->whereHas('posts')
                ->take(13)
                ->get();

            $topicIds = $frontTopics->pluck('id');
            $allTopicPostsQuery = null;

            if ($topicIds->isNotEmpty()) {
                foreach ($topicIds as $id) {
                    $subQuery = Post::select(
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
                        'posts.topic_id',
                        'channels.name',
                        'channels.logo',
                        'channels.slug as channel_slug'
                    )
                        ->join('channels', 'posts.channel_id', '=', 'channels.id')
                        ->where('topic_id', $id)
                        ->where('posts.status', 'active')
                        ->whereHas('channel', function ($query) {
                            $query->where('status', 'active');
                        })
                        ->whereHas('topic', function ($q) {
                            $q->where('status', 'active');
                        });

                    if ($subscribedLanguageIds->isNotEmpty()) {
                        $subQuery->whereIn('posts.news_language_id', $subscribedLanguageIds);
                    }

                    $subQuery->orderBy('publish_date', 'DESC')->take(4);

                    if ($allTopicPostsQuery === null) {
                        $allTopicPostsQuery = $subQuery;
                    } else {
                        $allTopicPostsQuery->unionAll($subQuery);
                    }
                }
            }

            $allTopicPosts = $allTopicPostsQuery ? $allTopicPostsQuery->get()->groupBy('topic_id') : collect();

            foreach ($frontTopics as $topic) {
                $topic->posts = ($allTopicPosts->get($topic->id) ?? collect())
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

            return $frontTopics;
        });

        // <><><><><><><> Fetch Consolidated Latest Global Posts <><><><><>
        $latestGlobalPosts = Cache::remember("home_latest_posts_feed_{$langKey}", 600, function() use ($subscribedLanguageIds, $defaultImage) {
            $query = Post::with([CHANNELS_TABEL, TOPICS_TABEL])
                ->select('posts.id', 'posts.title', 'posts.slug', 'posts.channel_id', 'posts.image', 'posts.pubdate', 'posts.view_count', 'posts.comment', 'posts.reaction', 'posts.status', 'posts.type', 'posts.video_thumb', 'posts.publish_date')
                ->where('posts.status', 'active')
                ->whereHas('channel', function ($query) {
                    $query->where('status', 'active');
                })
                ->where(function ($q) {
                    $q->whereHas('topic', function ($query) {
                        $query->where('status', 'active');
                    })
                        ->orWhereDoesntHave('topic'); // makes topic optional
                });

            if ($subscribedLanguageIds->isNotEmpty()) {
                $query->whereIn('posts.news_language_id', $subscribedLanguageIds);
            }

            return $query->orderBy('publish_date', 'desc')
                ->take(45)
                ->get()
                ->unique('title')
                ->take(32)
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
        });

        // Shuffle in PHP memory to randomize distribution on refresh
        $shuffledPosts = $latestGlobalPosts->shuffle();

        // Sequential Slice Allocation
        $top_posts      = $shuffledPosts->slice(0, 4);
        $postBannersRaw = $shuffledPosts->slice(4, 7);
        $sidebarPosts   = $shuffledPosts->slice(11, 4);
        $latesNews      = $shuffledPosts->slice(15, 12);

        // Inject smart ads into the Banner Slider slice
        $postBanners = $this->injectAdsIntoBanners($postBannersRaw);

        // Compile all global post IDs displayed on this page load to exclude from personalized Followed Channels
        $displayedGlobalIds = $shuffledPosts->pluck('id')->toArray();

        // <><><><><><><> Fetch most read posts <><><><><>
        $mostReads = Cache::remember("home_most_reads_{$langKey}", 600, function() use ($subscribedLanguageIds, $defaultImage) {
            $mostReadsQuery = Post::with(['channel:id,name,slug'])
                ->select('id', 'title', 'slug', 'channel_id', 'image', 'pubdate', 'view_count', 'comment', 'reaction', 'status', 'type', 'video_thumb')
                ->whereHas('channel', function ($query) {
                    $query->where('status', 'active');
                })
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

            return $mostReadsQuery
                ->orderBy('view_count', 'desc')
                ->take(7)
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
        });

        // Followed Channels
        $channelFollowed = [];
        if ($userId) {
            $channel_ids = ChannelSubscriber::where('user_id', $userId)->pluck('channel_id')->toArray();

            if (! empty($channel_ids)) {
                $channelFollowed = Cache::remember("home_followed_channels_user_{$userId}", 600, function() use ($channel_ids, $subscribedLanguageIds, $defaultImage, $displayedGlobalIds) {
                    $channelFollowedQuery = Post::with(['channel:id,name,slug'])
                        ->select('id', 'title', 'slug', 'channel_id', 'image', 'pubdate', 'view_count', 'comment', 'reaction', 'status', 'type', 'video_thumb')
                        ->whereHas('channel', function ($query) {
                            $query->where('status', 'active');
                        })
                        ->where(function ($q) {
                            $q->whereHas('topic', function ($query) {
                                $query->where('status', 'active');
                            })
                                ->orWhereDoesntHave('topic'); // 👈 makes topic optional
                        })
                        ->where('posts.status', 'active')
                        ->whereNotIn('posts.id', $displayedGlobalIds); // 👈 Exclude already displayed global posts

                    if ($subscribedLanguageIds->isNotEmpty()) {
                        $channelFollowedQuery->whereIn('posts.news_language_id', $subscribedLanguageIds);
                    }
                    return $channelFollowedQuery
                        ->orderBy('publish_date', 'desc')
                        ->whereIn('posts.channel_id', $channel_ids)
                        ->take(15)
                        ->get()
                        ->unique('title')
                        ->take(5)
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
                });
            }
        }

        // <><><><><><><> Fetch Popular news posts <><><><><>
        $popularPosts = Cache::remember("home_popular_posts_{$langKey}", 600, function() use ($subscribedLanguageIds) {
            $popularPostsQuery = Post::with(['channel:id,name,slug'])
                ->select('id', 'title', 'slug', 'channel_id', 'image', 'pubdate', 'view_count', 'comment', 'status')
                ->whereHas('channel', function ($query) {
                    $query->where('status', 'active');
                })
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

            return $popularPostsQuery
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
        });

        // changes done here by P
        // $weather_api_key    = Setting::select('value')->where('name', 'weather_api_key')->first();
        // $weatherCardStatus  = Setting::select('value')->where('name', 'weather_card_status')->first();
        // $cookiesPopupStatus = Setting::select('value')->where('name', 'cookies_popup_status')->first();
        $weather_api_key    = (object) ['value' => $this->allSettings->get('weather_api_key')];
        $weatherCardStatus  = (object) ['value' => $this->allSettings->get('weather_card_status')];
        $cookiesPopupStatus = (object) ['value' => $this->allSettings->get('cookies_popup_status')];

        $videoPosts = Cache::remember("home_video_posts_{$langKey}", 600, function() {
            return $this->getPostsWithVideos(4);
        });

        $audioPosts = Cache::remember("home_audio_posts_{$langKey}", 600, function() {
            return $this->getPostsWithAudios(4);
        });

        $location   = 'bhuj';
        $latitude   = '23d2469d67';
        $longitude  = '69d67';
        // <><><><><><><> Fetch stories <><><><><>
        $stories = Cache::remember("home_stories_{$langKey}", 600, function() use ($subscribedLanguageIds) {
            return $this->getStoriesWithLanguages($subscribedLanguageIds, 7);
        });

        $magazines = Cache::remember("home_magazines_{$langKey}", 600, function() use ($subscribedLanguageIds) {
            return $this->getMagazinesWithLanguages($subscribedLanguageIds);
        });

        $enewspapers = Cache::remember("home_enewspapers_{$langKey}", 600, function() use ($subscribedLanguageIds) {
            return $this->getEnewspapersWithLanguages($subscribedLanguageIds);
        });

        // changes done here by P
        // $socialsettings   = Setting::pluck('value', 'name');
        $socialsettings   = $this->allSettings;
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
            // changes done here by P
            // $freeTrialLimit = Setting::where('name', 'free_trial_e_papers_and_magazines_limit')->value('value') ?? 5;
            $freeTrialLimit = $this->allSettings->get('free_trial_e_papers_and_magazines_limit') ?? 5;

            $freeTrialLimit = (int) $freeTrialLimit;

            $sessionKey   = 'free_tier_e_paper_count';
            $currentCount = session($sessionKey, 0);

            if ($freeTrialLimit !== -1 && $currentCount >= $freeTrialLimit) {
                $dailyLimitReached = true;
                session(['show_modal' => 'daily']);
            }
        }

        $theme = getTheme();
        $data  = compact('title', 'top_posts', 'postBanners', 'sidebarPosts', 'dailyLimitReached', 'subscriptionLimitReached', 'magazines', 'getEnewsSettings', 'enewspapers', 'socialsettings', 'defaultImage', 'frontTopics', 'latesNews', 'videoPosts', 'audioPosts', 'mostReads', 'popularPosts', 'theme', 'location', 'latitude', 'longitude', 'weather_api_key', 'stories', 'channelFollowed', 'cookiesPopupStatus', 'weatherCardStatus');
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
        $settings = $this->allSettings;

        $paperImage = isset($settings['enews_paper_image']) ? $settings['enews_paper_image'] : '';
        if ($paperImage) {
            if (str_starts_with($paperImage, 'http://') || str_starts_with($paperImage, 'https://')) {
                // Do nothing, it is already a full URL
            } elseif (str_starts_with($paperImage, 'storage/')) {
                $paperImage = url($paperImage);
            } else {
                $paperImage = url('storage/' . $paperImage);
            }
        } else {
            $paperImage = asset('front_end/classic/images/default/newspaper-advertising-service-500x500-1.png');
        }

        return [
            'paperimage' => $paperImage,
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

    private function injectAdsIntoBanners($posts)
    {
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

        // Format publish dates and add type identifier for posts
        $posts->transform(function ($post) {
            $post->item_type = 'post'; // Add identifier
            return $post;
        });

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

        return collect($final);
    }

    private function getPostsWithVideos($limit)
    {
        // changes done here by P
        /* OLD CODE:
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
        */
        $defaultImage = $this->defaultImage;
        $subscribedLanguageIds = $this->subscribedLanguageIds;

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
        // changes done here by P
        /* OLD CODE:
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
        */
        $defaultImage = $this->defaultImage;
        $subscribedLanguageIds = $this->subscribedLanguageIds;

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
    private function getStoriesWithLanguages($subscribedLanguageIds, $limit = null)
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

        if ($limit !== null) {
            $query->take($limit);
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
            ]);        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching posts: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getTopicPosts($topicId)
    {
        try {
            $userId = Auth::id() ?? 0;
            $request = request();

            if ($request->attributes->has('settings_cache')) {
                $allSettings = $request->attributes->get('settings_cache');
            } else {
                $allSettings = \Illuminate\Support\Facades\DB::table('settings')->select('name', 'value', 'updated_at')->get()->keyBy('name');
                $request->attributes->set('settings_cache', $allSettings);
            }
            $defaultImageSetting = $allSettings->get('default_image');
            $defaultImage = $defaultImageSetting ? url('storage/' . $defaultImageSetting->value) : '';

            if ($userId) {
                $subscribedLanguageIds = NewsLanguageSubscriber::where('user_id', $userId)->pluck('news_language_id');
            } else {
                $sessionLanguageId = session('selected_news_language');
                if ($sessionLanguageId) {
                    $subscribedLanguageIds = collect([$sessionLanguageId]);
                } else {
                    $defaultActiveLanguage = NewsLanguage::where('is_active', 1)->first();
                    $subscribedLanguageIds = $defaultActiveLanguage ? collect([$defaultActiveLanguage->id]) : collect();
                }
            }

            $posts = Post::select('id', 'image', 'video', 'video_thumb', 'type', 'title', 'slug', 'comment', 'publish_date', 'pubdate', 'status', 'view_count', 'reaction', 'topic_id')
                ->where('posts.status', 'active')
                ->where('topic_id', $topicId)
                ->whereHas('channel', function ($query) {
                    $query->where('status', 'active');
                })
                ->when($subscribedLanguageIds->isNotEmpty(), function ($q) use ($subscribedLanguageIds) {
                    $q->whereIn('posts.news_language_id', $subscribedLanguageIds);
                })
                ->orderBy('publish_date', 'DESC')
                ->take(5)
                ->get()
                ->map(function ($item) use ($defaultImage) {
                    $item->image = $item->image ?? $defaultImage;
                    if ($item->publish_date) {
                        $item->publish_date = Carbon::parse($item->publish_date)->diffForHumans();
                    } elseif ($item->pubdate) {
                        $item->pubdate = Carbon::parse($item->pubdate)->diffForHumans();
                    }
                    return $item;
                });

            return response()->json([
                'success' => true,
                'posts'   => $posts,
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching topic posts: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching posts: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getRemainingMostRead(Request $request)
    {
        try {
            $offset = (int) $request->input('offset', 7);
            $limit = 7;

            $userId = Auth::id() ?? 0;

            if ($request->attributes->has('settings_cache')) {
                $allSettings = $request->attributes->get('settings_cache');
            } else {
                $allSettings = \Illuminate\Support\Facades\DB::table('settings')->select('name', 'value', 'updated_at')->get()->keyBy('name');
                $request->attributes->set('settings_cache', $allSettings);
            }
            $defaultImageSetting = $allSettings->get('default_image');
            $defaultImage = $defaultImageSetting ? url('storage/' . $defaultImageSetting->value) : '';

            if ($userId) {
                $subscribedLanguageIds = NewsLanguageSubscriber::where('user_id', $userId)->pluck('news_language_id');
            } else {
                $sessionLanguageId = session('selected_news_language');
                if ($sessionLanguageId) {
                    $subscribedLanguageIds = collect([$sessionLanguageId]);
                } else {
                    $defaultActiveLanguage = NewsLanguage::where('is_active', 1)->first();
                    $subscribedLanguageIds = $defaultActiveLanguage ? collect([$defaultActiveLanguage->id]) : collect();
                }
            }

            $mostReadsQuery = Post::with(['channel:id,name,slug'])
                ->select('id', 'title', 'slug', 'channel_id', 'image', 'pubdate', 'view_count', 'comment', 'reaction', 'status', 'type', 'video_thumb')
                ->whereHas('channel', function ($query) {
                    $query->where('status', 'active');
                })
                ->where(function ($q) {
                    $q->whereHas('topic', function ($query) {
                        $query->where('status', 'active');
                    })
                        ->orWhereDoesntHave('topic');
                })
                ->where('posts.status', 'active');

            if ($subscribedLanguageIds->isNotEmpty()) {
                $mostReadsQuery->whereIn('posts.news_language_id', $subscribedLanguageIds);
            }

            $displayedIds = $request->input('displayed_ids', []);
            if (!empty($displayedIds)) {
                $mostReadsQuery->whereNotIn('posts.id', $displayedIds);
            } else {
                $mostReadsQuery->skip($offset);
            }

            $posts = $mostReadsQuery
                ->orderBy('view_count', 'desc')
                ->take($limit * 3)
                ->get()
                ->unique('title')
                ->take($limit)
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

            return response()->json([
                'success'  => true,
                'posts'    => $posts,
                'has_more' => $posts->count() === $limit,
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching remaining most read posts: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getRemainingStories(Request $request)
    {
        try {
            $offset = (int) $request->input('offset', 7);
            $limit = 7;

            $userId = Auth::id() ?? 0;

            if ($userId) {
                $subscribedLanguageIds = NewsLanguageSubscriber::where('user_id', $userId)->pluck('news_language_id');
            } else {
                $sessionLanguageId = session('selected_news_language');
                if ($sessionLanguageId) {
                    $subscribedLanguageIds = collect([$sessionLanguageId]);
                } else {
                    $defaultActiveLanguage = NewsLanguage::where('is_active', 1)->first();
                    $subscribedLanguageIds = $defaultActiveLanguage ? collect([$defaultActiveLanguage->id]) : collect();
                }
            }

            $query = Story::with(['story_slides', 'topic'])
                ->whereHas('story_slides')
                ->whereHas('topic', function ($q) {
                    $q->where('status', 'active');
                })
                ->orderBy('created_at', 'desc');

            if ($subscribedLanguageIds->isNotEmpty()) {
                $query->whereIn('news_language_id', $subscribedLanguageIds);
            }

            $displayedIds = $request->input('displayed_ids', []);
            if (!empty($displayedIds)) {
                $query->whereNotIn('stories.id', $displayedIds);
            } else {
                $query->skip($offset);
            }

            $stories = $query
                ->take($limit * 3)
                ->get()
                ->unique('title')
                ->take($limit)
                ->map(function ($story) {
                    $story->publish_date = $story->created_at->diffForHumans();
                    return $story;
                });

            return response()->json([
                'success'  => true,
                'posts'    => $stories,
                'has_more' => $stories->count() === $limit,
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching remaining stories: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getRemainingTopPosts(Request $request)
    {
        try {
            $offset = (int) $request->input('offset', 7);
            $limit = 7;

            $userId = Auth::id() ?? 0;

            if ($request->attributes->has('settings_cache')) {
                $allSettings = $request->attributes->get('settings_cache');
            } else {
                $allSettings = \Illuminate\Support\Facades\DB::table('settings')->select('name', 'value', 'updated_at')->get()->keyBy('name');
                $request->attributes->set('settings_cache', $allSettings);
            }
            $defaultImageSetting = $allSettings->get('default_image');
            $defaultImage = $defaultImageSetting ? url('storage/' . $defaultImageSetting->value) : '';

            if ($userId) {
                $subscribedLanguageIds = NewsLanguageSubscriber::where('user_id', $userId)->pluck('news_language_id');
            } else {
                $sessionLanguageId = session('selected_news_language');
                if ($sessionLanguageId) {
                    $subscribedLanguageIds = collect([$sessionLanguageId]);
                } else {
                    $defaultActiveLanguage = NewsLanguage::where('is_active', 1)->first();
                    $subscribedLanguageIds = $defaultActiveLanguage ? collect([$defaultActiveLanguage->id]) : collect();
                }
            }

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
                        ->orWhereDoesntHave('topic');
                });

            if ($subscribedLanguageIds->isNotEmpty()) {
                $top_posts_query->whereIn('news_language_id', $subscribedLanguageIds);
            }

            $displayedIds = $request->input('displayed_ids', []);
            if (!empty($displayedIds)) {
                $top_posts_query->whereNotIn('posts.id', $displayedIds);
            } else {
                $top_posts_query->skip($offset);
            }

            $posts = $top_posts_query
                ->orderBy('publish_date', 'desc')
                ->take($limit * 3)
                ->get()
                ->unique('title')
                ->take($limit)
                ->map(function ($post) {
                    $post->publish_date_news = Carbon::parse($post->pubdate)->format(self::TIME_FORMATE);
                    if ($post->publish_date) {
                        $post->publish_date = Carbon::parse($post->publish_date)->diffForHumans();
                    } elseif ($post->pubdate) {
                        $post->pubdate = Carbon::parse($post->pubdate)->diffForHumans();
                    }
                    return $post;
                });

            return response()->json([
                'success'  => true,
                'posts'    => $posts,
                'has_more' => $posts->count() === $limit,
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching remaining top posts: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getRemainingFollowedChannels(Request $request)
    {
        try {
            $offset = (int) $request->input('offset', 7);
            $limit = 7;

            $userId = Auth::id() ?? 0;
            if (!$userId) {
                return response()->json([
                    'success'  => true,
                    'html'     => '',
                    'has_more' => false,
                ]);
            }

            $channel_ids = ChannelSubscriber::where('user_id', $userId)->pluck('channel_id')->toArray();
            if (empty($channel_ids)) {
                return response()->json([
                    'success'  => true,
                    'html'     => '',
                    'has_more' => false,
                ]);
            }

            if ($request->attributes->has('settings_cache')) {
                $allSettings = $request->attributes->get('settings_cache');
            } else {
                $allSettings = \Illuminate\Support\Facades\DB::table('settings')->select('name', 'value', 'updated_at')->get()->keyBy('name');
                $request->attributes->set('settings_cache', $allSettings);
            }
            $defaultImageSetting = $allSettings->get('default_image');
            $defaultImage = $defaultImageSetting ? url('storage/' . $defaultImageSetting->value) : '';

            if ($userId) {
                $subscribedLanguageIds = NewsLanguageSubscriber::where('user_id', $userId)->pluck('news_language_id');
            } else {
                $sessionLanguageId = session('selected_news_language');
                if ($sessionLanguageId) {
                    $subscribedLanguageIds = collect([$sessionLanguageId]);
                } else {
                    $defaultActiveLanguage = NewsLanguage::where('is_active', 1)->first();
                    $subscribedLanguageIds = $defaultActiveLanguage ? collect([$defaultActiveLanguage->id]) : collect();
                }
            }

            $channelFollowedQuery = Post::with(['channel:id,name,slug'])
                ->select('id', 'title', 'slug', 'channel_id', 'image', 'pubdate', 'view_count', 'comment', 'reaction', 'status', 'type', 'video_thumb')
                ->whereHas('channel', function ($query) {
                    $query->where('status', 'active');
                })
                ->where(function ($q) {
                    $q->whereHas('topic', function ($query) {
                        $query->where('status', 'active');
                    })
                        ->orWhereDoesntHave('topic');
                })
                ->where('posts.status', 'active')
                ->whereIn('posts.channel_id', $channel_ids);

            if ($subscribedLanguageIds->isNotEmpty()) {
                $channelFollowedQuery->whereIn('posts.news_language_id', $subscribedLanguageIds);
            }

            $displayedIds = $request->input('displayed_ids', []);
            if (!empty($displayedIds)) {
                $channelFollowedQuery->whereNotIn('posts.id', $displayedIds);
            } else {
                $channelFollowedQuery->skip($offset);
            }

            $posts = $channelFollowedQuery
                ->orderBy('publish_date', 'desc')
                ->take($limit * 3)
                ->get()
                ->unique('title')
                ->take($limit)
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

            return response()->json([
                'success'  => true,
                'posts'    => $posts,
                'has_more' => $posts->count() === $limit,
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching remaining followed channels posts: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }
}

