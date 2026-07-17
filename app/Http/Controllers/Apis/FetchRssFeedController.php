<?php
namespace App\Http\Controllers\Apis;

use App\Constants\DatabaseFields;
use App\Http\Controllers\Controller;
use App\Models\Admin\Notifications as AdminNotifications;
use App\Models\AppPostView;
use App\Models\Channel;
use App\Models\ChannelSubscriber;
use App\Models\Feature;
use App\Models\NewsLanguage;
use App\Models\Post;
use App\Models\ReadNotification;
use App\Models\Setting;
use App\Models\Subscription;
use App\Services\ResponseService;
use App\Traits\SelectsFields;
use Carbon\Carbon;
use DevDojo\LaravelReactions\Models\Reaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FetchRssFeedController extends Controller
{
    const IS_FAVORIT_CONDITION = 'IF(favorites.user_id IS NOT NULL, 1, 0) as is_favorite';
    const STORAGE_PATH         = 'storage/images/';
    use SelectsFields;

    /***** Fetch the post description*****/
    public function postDescription(Request $request, $slug, $device_id = null, $fcm_id = "")
    {
        $user   = Auth::user();
        $userId = $user->id ?? null;

        try {
            if ($device_id != "") {
                $this->viewCount($slug, $device_id);
            }

            $newsLanguageIds = $this->normalizeNewsLanguageIds($request->news_language_id);

            $requestPlanLimit = (int) $request->plan_article_count;
            // Get news_language_id from the header
            $newsLanguageId   = $request->news_language_id;
            $newsLanguageCode = null;

            // Validate news_language_id and fetch its code if valid
            if ($newsLanguageId) {
                $newsLanguage = NewsLanguage::find($newsLanguageId);
                if ($newsLanguage) {
                    $newsLanguageCode = $newsLanguage->code;
                }
            }

            $post = Post::with('images')
                ->select($this->selectPostDescriptionFields())
                ->selectRaw(self::IS_FAVORIT_CONDITION)
                ->whereIn('posts.news_language_id', $newsLanguageIds)
                ->join('channels', function ($join) {
                    $join->on('posts.channel_id', '=', 'channels.id')
                        ->where('channels.status', 'active');
                })
                ->leftJoin('topics', function ($join) {
                    $join->on('posts.topic_id', '=', 'topics.id')
                        ->where('topics.status', 'active');
                })
                ->leftJoin('favorites', function ($join) use ($userId) {
                    $join->on('posts.id', '=', 'favorites.post_id')
                        ->where('favorites.user_id', '=', $userId);
                })
                ->where('posts.status', 'active')
                ->where('posts.slug', $slug)
                ->first();

            if ($post) {
                $post->publish_date = Carbon::parse($post->publish_date)->diffForHumans();
                $post->channel_logo = url(self::STORAGE_PATH . $post->channel_logo);
                $post->description  = html_entity_decode($post->description ?? '');

                $mainImage = $post->image;

                $galleryImages = $post->images
                    ->pluck('image')
                    ->filter(fn($img) => $img != $mainImage)
                    ->values()
                    ->toArray();

                $postImages  = array_merge([$mainImage], $galleryImages);
                $post->image = $postImages;

                $userHasReacted         = $post->reactions()->where('responder_id', $userId)->first();
                $post->user_has_reacted = isset($userHasReacted) ? true : false;
                $post->emoji_type       = isset($userHasReacted) ? $userHasReacted->name : "";

                $getReactCountsData = $post->getReactionsSummary();

                $post->reaction_list = $getReactCountsData->sortByDesc(function ($reaction) {
                    $reactionEmoji  = Reaction::where('name', $reaction->name)->first();
                    $reaction->uuid = $reactionEmoji->uuid;
                    return $reaction->count;
                })->values();

                $newsLanguageIds    = $this->normalizeNewsLanguageIds($request->news_language_id);
                $post->releted_post = Post::select($this->selectPostDescriptionFields())
                    ->join('channels', function ($join) {
                        $join->on('posts.channel_id', '=', 'channels.id')
                            ->where('channels.status', 'active');
                    })
                    ->leftJoin('topics', function ($join) {
                        $join->on('posts.topic_id', '=', 'topics.id')
                            ->where('topics.status', 'active');
                    })
                    ->where('posts.status', 'active')
                    ->whereIn('posts.news_language_id', $newsLanguageIds)
                    ->where('topics.name', $post->topic_name)
                    ->where('posts.slug', '!=', $slug)
                    ->inRandomOrder()
                    ->orderBy('posts.publish_date', 'desc')
                    ->take(4)
                    ->get()
                    ->map(function ($item) {
                        $item->video_thumb      = $item->video_thumb == null ? "" : $item->video_thumb;
                        $item->video            = $item->video == null ? "" : $item->video;
                        $item->image            = $item->image ?? url('public/front_end/classic/images/default/post-placeholder.jpg');
                        $item->publish_date_org = $item->publish_date;
                        $item->publish_date     = Carbon::parse($item->publish_date)->diffForHumans();
                        $item->channel_logo     = url(self::STORAGE_PATH . $item->channel_logo);
                        return $item;
                    });

                // Filter by selected languages if provided
                if (! empty($newsLanguageIds)) {
                    $validLanguageIds = NewsLanguage::whereIn('id', $newsLanguageIds)->pluck('id')->toArray();
                    if (! empty($validLanguageIds)) {
                        $post->releted_post->whereIn('posts.news_language_id', $validLanguageIds);
                    }
                } else {
                    $defaultActiveLanguage = NewsLanguage::where('is_active', 1)->first();
                    if ($defaultActiveLanguage) {
                        $post->releted_post->where('posts.news_language_id', $defaultActiveLanguage->id);
                    }
                }

                $isAdsFree = false;
                if ($user && $user->subscription) {
                    $isAdsFree = $user->subscription->feature->is_ads_free ?? false;
                }
                if ($fcm_id != "") {
                    $this->readNotification($fcm_id, $slug);
                }
                $ads = DB::table('smart_ad_placements as sap')
                    ->join('smart_ads as sa', 'sap.smart_ad_id', '=', 'sa.id')
                    ->join('smart_ads_details as sad', 'sap.smart_ad_id', '=', 'sad.smart_ad_id')
                    ->where('sap.placement_key', 'after_read_more')
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
                    ->get();

                $adsData = [];

                foreach ($ads as $ad) {
                    // increment views for each ad
                    DB::table('smart_ads')
                        ->where('id', $ad->smart_ad_id)
                        ->increment('views');

                    $adsData[] = [
                        "id"               => "ad_" . $ad->smart_ad_id,
                        "smart_ad_id"      => $ad->smart_ad_id,
                        "type"             => "ad",
                        "name"             => $ad->name,
                        "title"            => $ad->name,
                        "description"      => $ad->body,
                        "body"             => $ad->body,
                        "image"            => $ad->vertical_image ? url('storage/' . $ad->vertical_image) : null,
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
                        "publish_date"     => Carbon::parse($ad->created_at)->diffForHumans(),
                    ];
                }

                if (! empty($adsData) && $post->releted_post instanceof \Illuminate\Support\Collection) {
                    $randomIndex = rand(0, $post->releted_post->count());
                    $post->releted_post->splice($randomIndex, 0, $adsData);
                }

                // ================= SUBSCRIPTION =================
                $activeSubscription = $user ? Subscription::with('feature')
                    ->where('user_id', $user->id ?? "")
                    ->where('status', 'active')
                    ->whereDate('end_date', '>=', now())
                    ->first() : null;

                $features = $activeSubscription
                    ? Feature::where('plan_id', $activeSubscription->plan_id)->first()
                    : null;

                $plan_article_count       = 0;
                $article_read_count       = 0;
                $total_article_read_count = 0;

                if ($user && $activeSubscription) {
                    $plan_article_count = $activeSubscription->feature->number_of_articles ?? 0;

                    if ($requestPlanLimit == 1) {
                        if ($activeSubscription->article_count < $plan_article_count) {
                            $activeSubscription->increment('article_count');
                            $activeSubscription->refresh();
                        }
                    }
                }
                $planUsed = optional($activeSubscription)->article_count ?? 0;
                $planMax  = $features ? $features->number_of_articles : 0;

                // ✅ If equal → show +1
                if ($activeSubscription != null) {
                    if ($planUsed == $planMax) {
                        $planUsed = $planUsed + 1;
                    }
                }

                // ================= FINAL RESPONSE =================
                return response()->json([
                    'error'   => false,
                    'message' => DatabaseFields::POST_DESCRIPTION_FETCHED_SECCUSSES,
                    'data'    => array_merge(
                        $post->toArray(),
                        [
                            'is_ads_free'             => $isAdsFree,
                            'news_language_code'      => $newsLanguageCode,
                            'plan_article_count'      => $planUsed ?? 0,
                            'plan_total_max_articles' => $features ? $features->number_of_articles : 0,
                            'plan_status'             => ($activeSubscription && $activeSubscription->status == 'active') ? true : false,
                        ]
                    ),
                ]);
            }

            return response()->json([
                'error'   => false,
                'message' => 'No post found.',
                'data'    => [],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error'   => true,
                'message' => 'An error occurred while fetching the post: ' . $e->getMessage(),
                'data'    => [],
            ], 500);
        }
    }

    /***** Fetch Banner Posts *****/

    protected function fetchBannerPosts(Request $request)
    {
        $user = Auth::user();

        // Normalize news_language_id(s) from request header
        $newsLanguageIds = $this->normalizeNewsLanguageIds($request->news_language_id);

        // Base query for posts
        $query = Post::select($this->selectBannerPosts())
            ->join('channels', function ($join) {
                $join->on('posts.channel_id', '=', 'channels.id')
                    ->where('channels.status', 'active');
            })
            ->leftjoin('topics', function ($join) {
                $join->on('posts.topic_id', '=', 'topics.id')
                    ->where('topics.status', 'active');
            })
            ->where('posts.status', 'active')
            ->where('posts.image', '!=', '')
            ->where('posts.description', '!=', '');

        // Filter by selected languages if provided
        if (! empty($newsLanguageIds)) {
            $validLanguageIds = NewsLanguage::whereIn('id', $newsLanguageIds)->pluck('id')->toArray();
            if (! empty($validLanguageIds)) {
                $query->whereIn('posts.news_language_id', $validLanguageIds);
            }
        } else {
            // Default active language
            $defaultActiveLanguage = NewsLanguage::where('is_active', 1)->first();
            if ($defaultActiveLanguage) {
                $query->where('posts.news_language_id', $defaultActiveLanguage->id);
            }
        }

        // Execute query
        $posts = $query
            ->orderBy('publish_date', 'desc')
            ->take(5)
            ->get()
            ->map(function ($item) {
                $item->image        = $item->image ?? '';
                $item->video_thumb  = $item->video_thumb ?? '';
                $item->video        = $item->video ?? '';
                $item->pubdate      = $item->publish_date;
                $item->publish_date = Carbon::parse($item->publish_date)->diffForHumans();
                $item->channel_logo = url(self::STORAGE_PATH . ($item->channel->logo ?? ''));
                $item->type         = 'post';
                return $item;
            });

        // Subscription check
        $isAdsFree = false;
        if ($user && $user->subscription) {
            $isAdsFree = $user->subscription->feature->is_ads_free ?? false;
        }

        $ads = [];

        $adsCollection = DB::table('smart_ad_placements as sap')
            ->join('smart_ads as sa', 'sap.smart_ad_id', '=', 'sa.id')
            ->join('smart_ads_details as sad', 'sap.smart_ad_id', '=', 'sad.smart_ad_id')
            ->where('sap.placement_key', 'app_banner_slider')
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
            ->get();

        if ($adsCollection->isNotEmpty()) {
            foreach ($adsCollection as $ad) {
                DB::table('smart_ads')
                    ->where('id', $ad->smart_ad_id)
                    ->increment('views');

                $ads[] = [
                    "id"               => "ad_" . $ad->smart_ad_id,
                    "smart_ad_id"      => $ad->smart_ad_id,
                    "type"             => "ad",
                    "name"             => $ad->name,
                    "title"            => $ad->name,
                    "description"      => $ad->body,
                    "body"             => $ad->body,
                    "image"            => $ad->vertical_image ? url('storage/' . $ad->vertical_image) : null,
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
                    "publish_date"     => Carbon::parse($ad->created_at)->diffForHumans(),
                ];
            }
        }

        // Merge ads into posts randomly
        $postsArray = $posts->toArray();
        if (! empty($ads)) {
            $randomIndex = rand(0, count($postsArray));
            array_splice($postsArray, $randomIndex, 0, $ads);
            $posts = $postsArray;
        }

        return response()->json([
            'error'       => false,
            'message'     => 'Banner Posts fetched successfully.',
            'data'        => $posts,
            'is_ads_free' => $isAdsFree,
        ]);
    }

    protected function normalizeNewsLanguageIds($newsLanguageIds)
    {
        if (is_null($newsLanguageIds)) {
            return [];
        }
        if (! is_array($newsLanguageIds)) {
            $newsLanguageIds = explode(',', str_replace(['[', ']'], '', $newsLanguageIds));
        }
        return array_filter($newsLanguageIds, 'is_numeric');
    }

    /**
     * Fetch popular posts for the home page.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function fetchPopularPosts(Request $request)
    {
        $user   = Auth::user();
        $userId = $user ? $user->id : null;

        try {
            $page    = (int) request()->get('page', 1);
            $perPage = (int) request()->get('per_page', 10);
            $offset  = ($page - 1) * $perPage;

            $type = request()->get('type', 'popular'); // default popular

            // ✅ Get popular post range from settings table
            $popularRange = Setting::where('name', 'popular_post_range')->value('value') ?? '';

            // Normalize and validate news_language_id(s)
            $newsLanguageIds = $this->normalizeNewsLanguageIds($request->news_language_id);

            $popularPostsQuery = Post::with(['channel', 'topic'])
                ->select('posts.*')
                ->selectRaw(self::IS_FAVORIT_CONDITION)
                ->selectRaw('CONCAT("' . rtrim(url(self::STORAGE_PATH), '/') . '/' . '", channels.logo) as channel_logo')
                ->selectRaw('channels.slug as channel_slug, channels.name as channel_name')
                ->where('posts.status', 'active')
                ->leftJoin('favorites', function ($join) use ($userId) {
                    $join->on('posts.id', '=', 'favorites.post_id')
                        ->where('favorites.user_id', '=', $userId);
                })
                ->join('channels', function ($join) {
                    $join->on('posts.channel_id', '=', 'channels.id')
                        ->where('channels.status', 'active');
                })
                ->leftjoin('topics', function ($join) {
                    $join->on('posts.topic_id', '=', 'topics.id')
                        ->where('topics.status', 'active');
                });

            // Filter by selected languages if provided
            if (! empty($newsLanguageIds)) {
                $validLanguageIds = NewsLanguage::whereIn('id', $newsLanguageIds)->pluck('id')->toArray();
                if (! empty($validLanguageIds)) {
                    $popularPostsQuery->whereIn('posts.news_language_id', $validLanguageIds);
                }
            } else {
                $defaultActiveLanguage = NewsLanguage::where('is_active', 1)->first();
                if ($defaultActiveLanguage) {
                    $popularPostsQuery->where('posts.news_language_id', $defaultActiveLanguage->id);
                }
            }

            // ✅ Apply popular post time range filter
            if ($popularRange !== 'lifetime') {
                $fromDate = match ($popularRange) {
                    '24 hours' => Carbon::now()->subHours(24),
                    '48 hours' => Carbon::now()->subHours(48),
                    '72 hours' => Carbon::now()->subHours(72),
                    '1 week'   => Carbon::now()->subWeek(),
                    '1 month'  => Carbon::now()->subMonth(),
                    '1 year'   => Carbon::now()->subYear(),
                    default    => null,
                };

                if ($fromDate) {
                    $popularPostsQuery->where('posts.publish_date', '>=', $fromDate);
                }
            }

            // ✅ Handle type (latest / oldest / popular)
            if ($type === 'latest') {
                $popularPostsQuery->orderByDesc('posts.publish_date');
            } elseif ($type === 'oldest') {
                $popularPostsQuery->orderBy('posts.publish_date', 'asc');
            } else {
                // default: popular (latest by default for now, or you can add your own logic)
                $popularPostsQuery->orderByDesc('posts.view_count', 'DESC');
            }

            $popularPosts = $popularPostsQuery
                ->offset($offset)
                ->limit($perPage)
                ->get()
                ->map(function ($item) {
                    $item->image        = $item->image ?? '';
                    $item->video_thumb  = $item->video_thumb ?? '';
                    $item->video        = $item->video ?? '';
                    $item->publish_date = Carbon::parse($item->publish_date)->diffForHumans();
                    return $item;
                });

            $isAdsFree = false;
            if ($user && $user->subscription) {
                $isAdsFree = $user->subscription->feature->is_ads_free ?? false;
            }

            return response()->json([
                'error'       => false,
                'message'     => ucfirst($type) . ' posts fetched successfully.',
                'data'        => $popularPosts,
                'is_ads_free' => $isAdsFree,
            ]);
        } catch (\Exception $e) {
            ResponseService::logErrorResponse($e, 'Failed to fetch popular posts: ' . $e->getMessage());
            return ResponseService::errorResponse('Unable to fetch popular posts at this time.', 500);
        }
    }

    /***** Recommanded content & Content May you Like *****/
    public function fetchPosts(Request $request)
    {
        try {
            $user    = Auth::user();
            $perPage = request()->get('per_page', 10);
            $userId  = $user ? $user->id : null;

            $channel_ids     = ChannelSubscriber::where('user_id', $userId)->pluck('channel_id')->toArray();
            $newsLanguageIds = $request->news_language_id;

            if (! is_array($newsLanguageIds)) {
                $newsLanguageIds = explode(',', str_replace(['[', ']'], '', (string) $newsLanguageIds));
            }
            $newsLanguageIds = array_filter($newsLanguageIds); // Remove empty values

            $post = Post::with(['channel', 'topic'])
                ->select('posts.*')
                ->selectRaw('CONCAT("' . rtrim(url(self::STORAGE_PATH), '/') . '/' . '", channels.logo) as channel_logo')
                ->selectRaw(self::IS_FAVORIT_CONDITION)
                ->selectRaw('channels.slug as channel_slug, channels.name as channel_name')
                ->leftJoin('favorites', function ($join) use ($userId) {
                    $join->on('posts.id', '=', 'favorites.post_id')
                        ->where('favorites.user_id', '=', $userId);
                })
                ->where('posts.status', 'active')
                ->join('channels', function ($join) {
                    $join->on('posts.channel_id', '=', 'channels.id')
                        ->where('channels.status', 'active');
                })
                ->leftjoin('topics', function ($join) {
                    $join->on('posts.topic_id', '=', 'topics.id')
                        ->where('topics.status', 'active');
                });

            // Filter by selected languages if provided
            if (! empty($newsLanguageIds)) {
                $post->whereIn('posts.news_language_id', $newsLanguageIds);
            } else {
                $defaultActiveLanguage = NewsLanguage::where('is_active', 1)->first();
                if ($defaultActiveLanguage) {
                    $post->where('posts.news_language_id', $defaultActiveLanguage->id);
                }
            }

            if ($user) {
                $post = $post->whereNotIn('posts.channel_id', $channel_ids);
            }

            // Paginate first
            $paginator = $post->orderBy('posts.publish_date', 'desc')
                ->where('posts.image', '!=', '')
                ->paginate($perPage);

            // Map the paginator's collection (not the paginator itself)
            $mapped = $paginator->getCollection()->map(function ($item) {
                $item->image        = $item->image ?? "";
                $item->video_thumb  = $item->video_thumb ?? "";
                $item->video        = $item->video ?? "";
                $item->publish_date = Carbon::parse($item->publish_date)->diffForHumans();
                return $item;
            });

            // Convert to a plain array for array_splice
            $results = $mapped->values()->all();

            // Ads-free?
            $isAdsFree = false;
            if ($user && $user->subscription) {
                $isAdsFree = $user->subscription->feature->is_ads_free ?? false;
            }
            $ads      = [];
            $adsQuery = DB::table('smart_ad_placements as sap')
                ->join('smart_ads as sa', 'sap.smart_ad_id', '=', 'sa.id')
                ->join('smart_ads_details as sad', 'sap.smart_ad_id', '=', 'sad.smart_ad_id')
                ->where('sap.placement_key', 'above_recommendations')
                ->where('sad.ad_publish_status', 'approved')
                ->where('sad.payment_status', 'success')
                ->where('sap.start_date', '<=', now())
                ->where('sap.end_date', '>=', now())
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
                ->inRandomOrder()
                ->get();

            if ($adsQuery->isNotEmpty()) {
                foreach ($adsQuery as $ad) {
                    // Increment views for each ad
                    DB::table('smart_ads')
                        ->where('id', $ad->smart_ad_id)
                        ->increment('views');

                    $ads[] = [
                        "id"               => "ad_" . $ad->smart_ad_id,
                        "smart_ad_id"      => $ad->smart_ad_id,
                        "type"             => "ad",
                        "name"             => $ad->name,
                        "title"            => $ad->name,
                        "description"      => $ad->body,
                        "body"             => $ad->body,
                        "image"            => $ad->vertical_image ? url('storage/' . $ad->vertical_image) : null,
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
                        "publish_date"     => Carbon::parse($ad->created_at)->diffForHumans(),
                    ];
                }
            }

            // Inject ads randomly into results
            if (! empty($results) && ! empty($ads)) {
                foreach ($ads as $ad) {
                    $randomIndex = rand(0, count($results));
                    array_splice($results, $randomIndex, 0, [$ad]);
                }
            } else {
                // No posts available → DO NOT send ads
                $ads = [];
            }

            return response()->json([
                'error'       => false,
                'message'     => 'Recomanded fetched successfully.',
                'data'        => $results,
                'is_ads_free' => $isAdsFree,
            ]);
        } catch (\Exception $e) {
            ResponseService::logErrorResponse($e, 'Failed to fetch Recomandation posts: ' . $e->getMessage());
            return response()->json([
                'error' => 'Unable to fetch posts at this time. Please try again later.',
            ], 500);
        }
    }

    /***** Followed channels posts *****/

    public function followedChannelsPosts(Request $request)
    {
        try {
            if (Auth::check()) {
                $perPage     = request()->get('per_page', 10);
                $user        = Auth::user();
                $userId      = $user ? $user->id : null;
                $channel_ids = ChannelSubscriber::where('user_id', $userId)->pluck('channel_id')->toArray();

                // Normalize news_language_id(s) from request header
                $newsLanguageIds = $this->normalizeNewsLanguageIds($request->news_language_id);

                $postQuery = Post::select($this->recommandedfetchPosts())
                    ->selectRaw(self::IS_FAVORIT_CONDITION)
                    ->join('channels', 'posts.channel_id', '=', 'channels.id')
                    ->leftjoin('topics', 'posts.topic_id', '=', 'topics.id')
                    ->leftJoin('favorites', function ($join) use ($userId) {
                        $join->on('posts.id', '=', 'favorites.post_id')
                            ->where('favorites.user_id', '=', $userId);
                    })
                    ->where('posts.status', 'active')
                    ->whereIn('posts.channel_id', $channel_ids)
                    ->where('posts.image', '!=', '')
                    ->orderBy('posts.publish_date', 'desc');

                // Filter posts by news_language_id if provided
                if (! empty($newsLanguageIds)) {
                    $validLanguageIds = NewsLanguage::whereIn('id', $newsLanguageIds)->pluck('id')->toArray();
                    if (! empty($validLanguageIds)) {
                        $postQuery->whereIn('posts.news_language_id', $validLanguageIds);
                    }
                } else {
                    // Fetch default active language
                    $defaultActiveLanguage = \App\Models\NewsLanguage::where('is_active', 1)->first();
                    if ($defaultActiveLanguage) {
                        $postQuery->where('posts.news_language_id', $defaultActiveLanguage->id);
                    }
                }

                $posts = $postQuery->paginate($perPage);

                $results = $posts->map(function ($item) {
                    $item->image        = $item->image ?? '';
                    $item->video_thumb  = $item->video_thumb ?? '';
                    $item->video        = $item->video ?? '';
                    $item->publish_date = Carbon::parse($item->publish_date)->diffForHumans();
                    $item->channel_logo = url(self::STORAGE_PATH . $item->channel_logo);

                    return $item;
                });

                $isAdsFree = false;
                if ($user && $user->subscription) {
                    $isAdsFree = $user->subscription->feature->is_ads_free ?? false;
                }

                return response()->json([
                    'error'       => false,
                    'message'     => 'Followed Channels posts fetched successfully.',
                    'data'        => $results,
                    'is_ads_free' => $isAdsFree,
                ]);
            } else {
                return response()->json([
                    'error'   => false,
                    'message' => 'Unauthorized User',
                    'data'    => [],
                ]);
            }
        } catch (\Exception $e) {
            ResponseService::logErrorResponse($e, 'Failed to fetch Recommendation posts: ' . $e->getMessage());
            return response()->json([
                'error' => 'Unable to fetch posts at this time.',
            ], 500);
        }
    }

    public function fetchFollowedAndRecommendedPosts(Request $request)
    {
        try {
            $user    = Auth::user();
            $userId  = $user ? $user->id : null;
            $perPage = request()->get('per_page', 10);
            $page    = request()->get('page', 1);

            /*----------------------------------------------
        | USER FOLLOWED CHANNEL IDS
        ----------------------------------------------*/
            $followedChannelIds = ChannelSubscriber::where('user_id', $userId)
                ->pluck('channel_id')
                ->toArray();

            /*----------------------------------------------
        | NORMALIZE LANGUAGE IDS
        ----------------------------------------------*/
            $newsLanguageIds = $request->news_language_id;

            if (! is_array($newsLanguageIds)) {
                $newsLanguageIds = explode(',', str_replace(['[', ']'], '', (string) $newsLanguageIds));
            }

            $newsLanguageIds = array_filter($newsLanguageIds);

            if (empty($newsLanguageIds)) {
                $defaultLang = NewsLanguage::where('is_active', 1)->first();
                if ($defaultLang) {
                    $newsLanguageIds = [$defaultLang->id];
                }
            }

            /*----------------------------------------------
        | CHECK USER FOLLOWS ALL CHANNELS
        ----------------------------------------------*/
            $totalChannels = Channel::where('status', 'active')->count();
            $followedCount = count($followedChannelIds);
            $followAll     = ($followedCount === $totalChannels);

            $channel_ids = ChannelSubscriber::where('user_id', $userId)->pluck('channel_id')->toArray();

            // Normalize news_language_id(s) from request header
            $newsLanguageIds = $this->normalizeNewsLanguageIds($request->news_language_id);

            // FOLLOWED POSTS
            $followedQuery = Post::select($this->recommandedfetchPosts())
                ->selectRaw(self::IS_FAVORIT_CONDITION)
            // ->join('channels', 'posts.channel_id', '=', 'channels.id')
            // ->join('topics', 'posts.topic_id', '=', 'topics.id')
                ->join('channels', function ($join) {
                    $join->on('posts.channel_id', '=', 'channels.id')
                        ->where('channels.status', 'active');
                })
                ->leftjoin('topics', function ($join) {
                    $join->on('posts.topic_id', '=', 'topics.id')
                        ->where('topics.status', 'active');
                })
                ->selectRaw('channels.slug as channel_slug, channels.name as channel_name')
                ->selectRaw('CONCAT("' . rtrim(url(self::STORAGE_PATH), '/') . '/' . '", channels.logo) as channel_logo')
                ->leftJoin('favorites', function ($join) use ($userId) {
                    $join->on('posts.id', '=', 'favorites.post_id')
                        ->where('favorites.user_id', '=', $userId);
                })
                ->where('posts.status', 'active')
                ->whereIn('posts.channel_id', $channel_ids)
                ->whereIn('posts.news_language_id', $newsLanguageIds) // ✔ FIXED
                ->where('posts.image', '!=', '')
                ->orderBy('posts.publish_date', 'desc');

            // RECOMMENDED POSTS (NOT FROM FOLLOWED CHANNELS)
            $recommendedQuery = Post::select('posts.*')
                ->selectRaw(self::IS_FAVORIT_CONDITION)
            // ->join('channels', 'posts.channel_id', '=', 'channels.id')
            // ->join('topics', 'posts.topic_id', '=', 'topics.id')
                ->join('channels', function ($join) {
                    $join->on('posts.channel_id', '=', 'channels.id')
                        ->where('channels.status', 'active');
                })
                ->leftjoin('topics', function ($join) {
                    $join->on('posts.topic_id', '=', 'topics.id')
                        ->where('topics.status', 'active');
                })
                ->selectRaw('channels.slug as channel_slug, channels.name as channel_name')
                ->selectRaw('CONCAT("' . rtrim(url(self::STORAGE_PATH), '/') . '/' . '", channels.logo) as channel_logo')
                ->leftJoin('favorites', function ($join) use ($userId) {
                    $join->on('posts.id', '=', 'favorites.post_id')
                        ->where('favorites.user_id', '=', $userId);
                })
                ->where('posts.status', 'active')
                ->whereNotIn('posts.channel_id', $channel_ids) // ✔ FIXED
                ->whereIn('posts.news_language_id', $newsLanguageIds)->orderBy('posts.publish_date', 'desc');
            /*-------------------------------------------------------------
        | CONDITIONAL PAGINATION
        -------------------------------------------------------------*/
            if ($followAll) {
                // USER FOLLOWS ALL → Only paginate FOLLOWED posts
                $followed    = $followedQuery->paginate($perPage, ['*'], 'page', $page);
                $recommended = $recommendedQuery->paginate($perPage, ['*'], 'page', $page);

            } else {
                $followed    = $followedQuery->paginate($perPage, ['*'], 'page', $page);
                $recommended = $recommendedQuery->paginate($perPage, ['*'], 'page', $page);
            }

            /*----------------------------------------------
        | CLEAN PAGINATION FORMAT (REMOVE EXTRAS)
        ----------------------------------------------*/
            $cleanPagination = function ($paginator) {
                if (! ($paginator instanceof \Illuminate\Pagination\Paginator) &&
                    ! ($paginator instanceof \Illuminate\Pagination\LengthAwarePaginator)) {
                    return $paginator; // Non paginated collection
                }

                return $paginator->items();
            };

            $followed->transform(function ($post) {
                $post->publish_date = \Carbon\Carbon::parse($post->publish_date)->diffForHumans();
                return $post;
            });

            $recommended->transform(function ($post) {
                $post->publish_date = \Carbon\Carbon::parse($post->publish_date)->diffForHumans();
                return $post;
            });

            $followedFormatted    = $cleanPagination($followed);
            $recommendedFormatted = $cleanPagination($recommended);

            /*----------------------------------------------
        | ADS FREE CHECK
        ----------------------------------------------*/
            $isAdsFree = false;
            if ($user && $user->subscription) {
                $isAdsFree = $user->subscription->feature->is_ads_free ?? false;
            }

            /*----------------------------------------------
        | FINAL RESPONSE
        ----------------------------------------------*/
            return response()->json([
                'error'       => false,
                'message'     => 'Data fetched successfully.',
                'data'        => [
                    'followed_channel_posts' => $followedFormatted,
                    'recommended_posts'      => $recommendedFormatted,
                ],
                'is_ads_free' => $isAdsFree,
            ]);

        } catch (\Exception $e) {

            ResponseService::logErrorResponse($e, 'Merged fetch failed: ' . $e->getMessage());

            return response()->json([
                'error'   => true,
                'message' => 'Unable to fetch posts.',
            ], 500);
        }
    }

    public function fetchPostsByTopic($topic, Request $request)
    {
        $user   = Auth::user();
        $userId = $user ? $user->id : null;

        try {
            $page    = (int) request()->get('page', 1);
            $perPage = (int) request()->get('per_page', 10);
            $offset  = ($page - 1) * $perPage;

            $newsLanguageIds = $this->normalizeNewsLanguageIds($request->news_language_id);

            $postQuery = Post::select($this->recommandedfetchPosts())
                ->selectRaw(self::IS_FAVORIT_CONDITION)
                ->selectRaw('CONCAT("' . rtrim(url(self::STORAGE_PATH), '/') . '/' . '", channels.logo) as channel_logo')
            // ->join('channels', 'posts.channel_id', '=', 'channels.id')
            // ->join('topics', 'posts.topic_id', '=', 'topics.id')
                ->join('channels', function ($join) {
                    $join->on('posts.channel_id', '=', 'channels.id')
                        ->where('channels.status', 'active');
                })
                ->leftjoin('topics', function ($join) {
                    $join->on('posts.topic_id', '=', 'topics.id')
                        ->where('topics.status', 'active');
                })
                ->leftJoin('favorites', function ($join) use ($userId) {
                    $join->on('posts.id', '=', 'favorites.post_id')
                        ->where('favorites.user_id', '=', $userId);
                })
                ->where('posts.status', 'active')
                ->where('topics.name', $topic)
                ->where('posts.image', '!=', '')
                ->orderBy('posts.publish_date', 'desc');

            // Filter by selected languages if provided
            if (! empty($newsLanguageIds)) {
                $validLanguageIds = NewsLanguage::whereIn('id', $newsLanguageIds)->pluck('id')->toArray();
                if (! empty($validLanguageIds)) {
                    $postQuery->whereIn('posts.news_language_id', $validLanguageIds);
                }
            } else {
                // Fetch default active language
                $defaultActiveLanguage = NewsLanguage::where('is_active', 1)->first();
                if ($defaultActiveLanguage) {
                    $postQuery->where('posts.news_language_id', $defaultActiveLanguage->id);
                }
            }

            $results = $postQuery->offset($offset)
                ->limit($perPage)
                ->get()
                ->map(function ($item) {
                    $item->image        = $item->image ?? '';
                    $item->video_thumb  = $item->video_thumb ?? '';
                    $item->video        = $item->video ?? '';
                    $item->publish_date = Carbon::parse($item->publish_date)->diffForHumans();
                    return $item;
                });

            $isAdsFree = false;
            if ($user && $user->subscription) {
                $isAdsFree = $user->subscription->feature->is_ads_free ?? false;
            }

            $ads       = [];
            $ads       = [];
            $adRecords = DB::table('smart_ad_placements as sap')
                ->join('smart_ads as sa', 'sap.smart_ad_id', '=', 'sa.id')
                ->join('smart_ads_details as sad', 'sap.smart_ad_id', '=', 'sad.smart_ad_id')
                ->where('sap.placement_key', 'app_category_news_page')
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
                ->get(); // changed from ->first()

            if ($adRecords->isNotEmpty()) {
                foreach ($adRecords as $ad) {
                    DB::table('smart_ads')
                        ->where('id', $ad->smart_ad_id)
                        ->increment('views');

                    $ads[] = [
                        "id"               => "ad_" . $ad->smart_ad_id,
                        "smart_ad_id"      => $ad->smart_ad_id,
                        "type"             => "ad",
                        "name"             => $ad->name,
                        "title"            => $ad->name,
                        "description"      => $ad->body,
                        "body"             => $ad->body,
                        "image"            => $ad->vertical_image ? url('storage/' . $ad->vertical_image) : null,
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
                        "publish_date"     => Carbon::parse($ad->created_at)->diffForHumans(),
                    ];
                }
            }

            // Inject ads randomly if any
            if (! empty($ads)) {
                $randomIndex = rand(0, $results->count());
                $results->splice($randomIndex, 0, $ads);
            }

            return response()->json([
                'error'       => false,
                'message'     => 'Topic fetched successfully.',
                'data'        => $results,
                'is_ads_free' => $isAdsFree,
            ]);
        } catch (\Exception $e) {
            ResponseService::logErrorResponse($e, 'Failed to fetch recommendation posts: ' . $e->getMessage());
            return ResponseService::errorResponse('Unable to fetch posts at this time.', 500);
        }
    }

    /* Manage posts view count */
    // public function viewCount($slug, $device_id)
    // {

    //     $post = Post::where('slug', $slug)->first();
    //     if ($post !== null) {

    //         $viewexist = AppPostView::where('post_id', $post->id)
    //             ->where('device_id', $device_id)
    //             ->first();

    //         if (! $viewexist) {
    //             AppPostView::create([
    //                 'device_id' => $device_id,
    //                 'post_id'   => $post->id,
    //             ]);
    //             $post->increment('view_count');
    //         }
    //     }
    //     return true;
    // }

    public function viewCount($slug, $device_id)
    {
        $post = Post::where('slug', $slug)->first();

        if ($post) {

            $viewexist = AppPostView::where('post_id', $post->id)
                ->where('device_id', $device_id)
                ->whereDate('created_at', today())
                ->first();

            // If view count reset OR device not found
            if (! $viewexist || $post->view_count == 0) {

                if (! $viewexist) {
                    AppPostView::create([
                        'device_id' => $device_id,
                        'post_id'   => $post->id,
                    ]);
                }

                $post->increment('view_count');
            }
        }

        return true;
    }

    public function readNotification($fcm_id, $slug)
    {
        $notificationId = AdminNotifications::where('slug', $slug)->first();

        if ($notificationId != null) {
            $alreadyRead = ReadNotification::where('notification_id', $notificationId->id)->where('fcm_id', $fcm_id)->first();
            if ($alreadyRead === null) {

                ReadNotification::create([
                    'notification_id' => $notificationId->id,
                    'fcm_id'          => $fcm_id,
                ]);
            }
        }
        return true;
    }
}
