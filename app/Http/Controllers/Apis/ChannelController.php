<?php
namespace App\Http\Controllers\Apis;

use App\Http\Controllers\Controller;
use App\Models\Channel;
use App\Models\NewsLanguage;
use App\Models\Post;
use App\Models\Topic;
use App\Services\ResponseService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChannelController extends Controller
{

    const ERROR_MESSAGE = 'Failed to manage subscription. Please try again later.';
    const STORAGE_PATH  = 'storage/images/';
    /**** Fetch chennels *******/

    protected function index(Request $request)
    {
        $user               = auth()->user();
        $followedChannelIds = $user ? $user->subscriptions()->pluck('channel_id')->toArray() : [];

        // Normalize news_language_id(s) from request header
        $newsLanguageIds = $this->normalizeNewsLanguageIds($request->news_language_id);

        // Base query for channels
        $query = Channel::select('id', 'name', 'logo', 'slug')
            ->where('status', 'active');

        // Filter channels by news_language_id if provided
        if (! empty($newsLanguageIds)) {
            $validLanguageIds = NewsLanguage::whereIn('id', $newsLanguageIds)->pluck('id')->toArray();
            if (! empty($validLanguageIds)) {
                $query->whereHas('posts', function ($postQuery) use ($validLanguageIds) {
                    $postQuery->whereIn('news_language_id', $validLanguageIds);
                });
            }
        } else {
            // Fetch default active language
            $defaultActiveLanguage = NewsLanguage::where('is_active', 1)->first();
            if ($defaultActiveLanguage) {
                $query->whereHas('posts', function ($postQuery) use ($defaultActiveLanguage) {
                    $postQuery->where('news_language_id', $defaultActiveLanguage->id);
                });
            }
        }

        $page    = request()->get('page', 1);
        $perPage = request()->get('per_page', 10);
        $offset  = ($page - 1) * $perPage;

        $channels = $query->get()
            ->map(function ($item) use ($followedChannelIds) {
                $item->logo     = asset(self::STORAGE_PATH . $item->logo);
                $item->isFollow = in_array($item->id, $followedChannelIds) ? 1 : 0;
                return $item;
            })
            ->values()->slice($offset, $perPage)
            ->toArray();

        $channels = array_values($channels);

        $isAdsFree = false;
        if ($user && $user->subscription) {
            $isAdsFree = $user->subscription->feature->is_ads_free ?? false;
        }

        // 👉 Fetch ALL ads for placement "all_channels"
        $ads = DB::table('smart_ad_placements as sap')
            ->join('smart_ads as sa', 'sap.smart_ad_id', '=', 'sa.id')
            ->join('smart_ads_details as sad', 'sap.smart_ad_id', '=', 'sad.smart_ad_id')
            ->where('sap.placement_key', 'all_channels')
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
            // increment views
            DB::table('smart_ads')->where('id', $ad->smart_ad_id)->increment('views');

            $image            = $ad->vertical_image ? url('storage/' . $ad->vertical_image) : null;
            $horizontal_image = $ad->horizontal_image ? url('storage/' . $ad->horizontal_image) : null;

            $adsData[] = [
                "id"               => "ad_" . $ad->smart_ad_id,
                "smart_ad_id"      => $ad->smart_ad_id,
                "type"             => "ad",
                "name"             => $ad->name,
                "title"            => $ad->name,
                "description"      => $ad->body,
                "body"             => $ad->body,
                "image"            => $image,
                "horizontal_image" => $horizontal_image,
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

        // 👉 Inject ads randomly into channels
        foreach ($adsData as $ad) {
            $randomIndex = rand(0, count($channels));
            array_splice($channels, $randomIndex, 0, [$ad]);
        }

        return response()->json([
            'error'   => false,
            'message' => 'Channels fetched successfully.',
            'data'    => [
                'isChannelFollow' => $followedChannelIds ? true : false,
                'channels'        => $channels,
                'is_ads_free'     => $isAdsFree,
            ],
        ]);
    }

    /***** Channel Subscribe *****/
    public function subscribeChannel(Request $request, $slug)
    {
        if (! Auth::check()) {
            return response()->json([
                'error'   => true,
                'message' => 'Unauthenticated. Please log in to subscribe to channels.',
            ], 401);
        }

        $user         = Auth::user();
        $channel      = Channel::where('slug', $slug)->first();
        $isSubscribed = $channel->subscribers()->where('user_id', $user->id)->exists();

        try {
            if ($isSubscribed) {
                $channel->subscribers()->detach($user->id);
                $channel->decrement('follow_count');
                $status  = '0';
                $message = 'User unsubscribed from channel successfully.';
            } else {
                $channel->subscribers()->attach($user->id);
                $channel->increment('follow_count');
                $status  = '1';
                $message = 'User subscribed to channel successfully.';
            }
            return response()->json([
                'error'        => false,
                'status'       => $status,
                'channel_slug' => $channel->slug,
                'message'      => $message,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error'        => true,
                'status'       => $status,
                'channel_slug' => $channel->slug,
                'message'      => self::ERROR_MESSAGE,
            ], 500);
        }
    }

    /***** Channel Subscribe *****/
    public function subscribeChannelNew(Request $request, $slug)
    {
        if (! Auth::check()) {
            return response()->json([
                'error'   => true,
                'message' => 'Unauthenticated. Please log in to subscribe to channels.',
            ], 401);
        }

        $user    = Auth::user();
        $channel = Channel::where('slug', $slug)->first();

        $isSubscribed = $channel->subscribers()->where('user_id', $user->id)->exists();
        try {
            if ($isSubscribed) {
                $status  = '1';
                $message = 'Already Subscribed.';
            } else {
                $channel->subscribers()->attach($user->id);
                $channel->increment('follow_count');
                $status  = '1';
                $message = 'User subscribed to channel successfully.';
            }
            return response()->json([
                'error'        => false,
                'status'       => $status,
                'channel_slug' => $channel->slug,
                'message'      => $message,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error'        => true,
                'status'       => $status,
                'channel_slug' => $channel->slug,
                'message'      => self::ERROR_MESSAGE,
            ], 500);
        }
    }

    /***** Channel Unsubscribe *****/
    public function unSubscribeChannel(Request $request, $slug)
    {
        if (! Auth::check()) {
            return response()->json([
                'error'   => true,
                'message' => 'Unauthenticated. Please log in to unsubscribe to channels.',
            ], 401);
        }

        $user         = Auth::user();
        $channel      = Channel::where('slug', $slug)->first();
        $isSubscribed = $channel->subscribers()->where('user_id', $user->id)->exists();

        try {
            if ($isSubscribed) {
                $channel->subscribers()->detach($user->id);
                $channel->decrement('follow_count');
                $status  = '0';
                $message = 'User unsubscribed from channel successfully.';
            } else {
                $status  = '0';
                $message = 'Already Unsubscribed.';
            }
            return response()->json([
                'error'        => false,
                'status'       => $status,
                'channel_slug' => $channel->slug,
                'message'      => $message,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error'        => true,
                'status'       => $status,
                'channel_slug' => $channel->slug,
                'message'      => self::ERROR_MESSAGE,
            ], 500);
        }
    }

    public function getProfileData($slug)
    {
        $user   = Auth::user();
        $userId = $user ? $user->id : null;

        // Normalize news_language_id(s) from request header
        $newsLanguageIds = $this->normalizeNewsLanguageIds(request()->news_language_id);

        // Determine which language IDs to use
        $languageIdsToFilter = [];
        if (! empty($newsLanguageIds)) {
            $languageIdsToFilter = NewsLanguage::whereIn('id', $newsLanguageIds)->pluck('id')->toArray();
        } else {
            // Fetch default active language
            $defaultActiveLanguage = NewsLanguage::where('is_active', 1)->first();
            if ($defaultActiveLanguage) {
                $languageIdsToFilter = [$defaultActiveLanguage->id];
            }
        }

        $data = Channel::select('channels.id', 'name', 'logo', 'description', 'slug', 'follow_count', 'status')
            ->where('slug', $slug)
            ->selectRaw('IF(channel_subscribers.user_id IS NOT NULL, 1, 0) as is_followed')
        // ->selectRaw('(SELECT COUNT(*) FROM posts WHERE posts.channel_id = channels.id) as total_post')
            ->selectRaw('(SELECT COUNT(*) FROM posts WHERE posts.channel_id = channels.id' .
                (! empty($languageIdsToFilter) ? ' AND posts.news_language_id IN (' . implode(',', $languageIdsToFilter) . ')' : '') .
                ') as total_post')
            ->leftJoin('channel_subscribers', function ($join) use ($userId) {
                $join->on('channels.id', '=', 'channel_subscribers.channel_id')
                    ->where('channel_subscribers.user_id', '=', $userId);
            })
            ->get()
            ->map(function ($item) use ($languageIdsToFilter) {
                $item->logo = url(self::STORAGE_PATH . $item->logo);
                $postsQuery = Post::where('channel_id', $item->id);

                if (! empty($languageIdsToFilter)) {
                    $postsQuery->whereIn('news_language_id', $languageIdsToFilter);
                }

                $topicIds = $postsQuery->whereNotNull('topic_id')
                    ->distinct()
                    ->pluck('topic_id')
                    ->toArray();

                $topics = ! empty($topicIds) ? Topic::whereIn('id', $topicIds)->orderBy('categorie_order', 'asc')->get(['id', 'name', 'slug']) : collect([]);

                $item->topics_list = $topics;
                $firstPost         = Post::where('channel_id', $item->id)
                    ->when(! empty($languageIdsToFilter), function ($query) use ($languageIdsToFilter) {
                        return $query->whereIn('news_language_id', $languageIdsToFilter);
                    })
                    ->orderBy('created_at', 'desc')
                    ->first();

                $item->post_image = $firstPost && $firstPost->image ? $firstPost->image : null;

                return $item;
            })
            ->first();

        $isAdsFree = false;
        if ($user && $user->subscription) {
            $isAdsFree = $user->subscription->feature->is_ads_free ?? false;
        }

        return response()->json([
            'error'       => false,
            'message'     => 'Topics fetched successfully.',
            'data'        => $data,
            'is_ads_free' => $isAdsFree,

        ]);
    }

    public function getProfilePosts($slug)
    {
        $user   = Auth::user();
        $userId = $user ? $user->id : null;

        try {
            $short  = request()->get('short');
            $topics = request()->get('topics');

            $page    = (int) request()->get('page', 1);
            $perPage = (int) request()->get('per_page', 10);
            $offset  = ($page - 1) * $perPage;

            // Normalize news_language_id(s) from request header
            $newsLanguageIds = $this->normalizeNewsLanguageIds(request()->news_language_id);

            // Determine which language IDs to use
            $languageIdsToFilter = [];
            if (! empty($newsLanguageIds)) {
                $languageIdsToFilter = NewsLanguage::whereIn('id', $newsLanguageIds)->pluck('id')->toArray();
            } else {
                // Fetch default active language
                $defaultActiveLanguage = NewsLanguage::where('is_active', 1)->first();
                if ($defaultActiveLanguage) {
                    $languageIdsToFilter = [$defaultActiveLanguage->id];
                }
            }

            // Channel validation
            if ($slug === '') {
                return response()->json([
                    'error'   => true,
                    'message' => 'Channel slug is required.',
                ], 400);
            }

            $channel = Channel::select('id')->where('slug', $slug)->first();

            if (! $channel) {
                return response()->json([
                    'error'   => true,
                    'message' => 'Channel not found.',
                    'data'    => [],
                ], 404);
            }
            // Make topic optional - only query if category is provided and not 'all'
            $topicId = null;

            if (! empty($topics) && $topics !== 'all') {
                $topic = Topic::select('id', 'name')->where('id', $topics)->first();

                // Get id when topic available.
                if ($topic) {
                    $topicId = $topic->id;
                } else {
                    // Pass empty data when topic not availabel.
                    return response()->json([
                        'error'       => false,
                        'message'     => 'No posts found for the specified topic.',
                        'data'        => [],
                        'is_ads_free' => false,
                    ]);
                }
            }

            // Build post query
            $postQuery = Post::select(
                'posts.id',
                'posts.title',
                'posts.slug',
                'posts.type',
                'posts.video_thumb',
                'posts.video',
                'posts.image',
                'posts.publish_date',
                'posts.shere',
                'posts.view_count',
                'posts.favorite',
                'posts.comment',
                'topics.name as topic_name',
                'topics.slug as topic_slug',
                'channels.name as channel_name',
                'channels.slug as channel_slug',
                'channels.logo as channel_logo'
            )
                ->selectRaw('IF(favorites.user_id IS NOT NULL, 1, 0) as is_favorite')
            // ->join('channels', 'posts.channel_id', '=', 'channels.id')
            // ->leftJoin('topics', 'posts.topic_id', '=', 'topics.id')
            // ->where('channels.status', 'active')
            // ->where('topics.status', 'active')
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
                ->where('posts.channel_id', $channel->id);

            // Apply news language filter
            if (! empty($languageIdsToFilter)) {
                $postQuery->whereIn('posts.news_language_id', $languageIdsToFilter);
            }

            if ($topicId) {
                $postQuery->where('posts.topic_id', $topicId);
            }

            if ($short === 'most_read') {
                $postQuery->orderBy('posts.view_count', 'desc');
            } elseif ($short === 'all') {
                $postQuery->orderBy('posts.publish_date', 'desc');
            } elseif ($short === 'oldest') {
                $postQuery->orderBy('posts.publish_date', 'asc');
            }

            $results = $postQuery->orderBy('posts.publish_date', 'desc')
                ->offset($offset)
                ->limit($perPage)
                ->get()
                ->map(function ($item) {
                    // For video posts, use video_thumb in the image field if image is empty
                    if (in_array($item->type, ['video', 'youtube']) && empty($item->image) && ! empty($item->video_thumb)) {
                        $item->image = $item->video_thumb;
                    }

                    // Set default values for all fields (keep existing logic)
                    $item->image       = $item->image == null ? "" : $item->image;
                    $item->video_thumb = $item->video_thumb == null ? "" : $item->video_thumb;
                    $item->video       = $item->video == null ? "" : $item->video;

                    // Handle optional topic fields
                    $item->topic_name = $item->topic_name ?? "";
                    $item->topic_slug = $item->topic_slug ?? "";

                    $item->publish_date = Carbon::parse($item->publish_date)->diffForHumans();
                    $item->channel_logo = url(self::STORAGE_PATH . $item->channel_logo);

                    return $item;
                });

            $isAdsFree = false;
            if ($user && $user->subscription) {
                $isAdsFree = $user->subscription->feature->is_ads_free ?? false;
            }
            $ads = [];

            // fetch all ads for placement `all_channels`
            $adsCollection = DB::table('smart_ad_placements as sap')
                ->join('smart_ads as sa', 'sap.smart_ad_id', '=', 'sa.id')
                ->join('smart_ads_details as sad', 'sap.smart_ad_id', '=', 'sad.smart_ad_id')
                ->where('sap.placement_key', 'channels_floating')
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

            if (! empty($ads)) {
                $randomIndex = rand(0, $results->count());
                $results->splice($randomIndex, 0, $ads);
            }

            return response()->json([
                'error'       => false,
                'message'     => 'Data fetched successfully.',
                'data'        => $results,
                'is_ads_free' => $isAdsFree,
            ]);

        } catch (\Exception $e) {
            ResponseService::logErrorResponse($e, 'Failed to fetch Recommendation posts: ' . $e->getMessage());
            return response()->json([
                'error' => 'Unable to fetch posts at this time. Please try again later.',
            ], 500);
        }
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

    public function fetchTopics(Request $request)
    {
        $user = Auth::user();

        // Normalize news_language_id(s) from request header
        $newsLanguageIds = $this->normalizeNewsLanguageIds($request->news_language_id);

        // Base query for topics
        $query = Topic::select('id', 'slug', 'name', 'logo', 'status')
            ->where('status', 'active')
            ->orderBy('categorie_order', 'asc')
            ->whereHas('posts');

        // Filter topics by news_language_id if provided
        if (! empty($newsLanguageIds)) {
            $validLanguageIds = NewsLanguage::whereIn('id', $newsLanguageIds)->pluck('id')->toArray();
            if (! empty($validLanguageIds)) {
                $query->whereHas('posts', function ($postQuery) use ($validLanguageIds) {
                    $postQuery->whereIn('news_language_id', $validLanguageIds);
                });
            }
        } else {
            // Fetch default active language
            $defaultActiveLanguage = NewsLanguage::where('is_active', 1)->first();
            if ($defaultActiveLanguage) {
                $query->whereHas('posts', function ($postQuery) use ($defaultActiveLanguage) {
                    $postQuery->where('news_language_id', $defaultActiveLanguage->id);
                });
            }
        }

        // Pagination logic
        $page    = (int) request()->get('page', 1);
        $perPage = (int) request()->get('per_page', 10000);
        $offset  = ($page - 1) * $perPage;

        // Fetch and transform topics
        $topics = $query->offset($offset)
            ->limit($perPage)
            ->get()
            ->map(function ($item) {
                if (empty($item->logo)) {
                    $item->logo = asset('assets/images/no_image_available.png');
                } else {
                    $item->logo = asset(self::STORAGE_PATH . $item->logo);
                }
                return $item;
            })
            ->toArray();

        // Check if user has an ad-free subscription
        $isAdsFree = false;
        if ($user && $user->subscription) {
            $isAdsFree = $user->subscription->feature->is_ads_free ?? false;
        }
        // Fetch ads only if not ads-free
        $ads = DB::table('smart_ad_placements as sap')
            ->join('smart_ads as sa', 'sap.smart_ad_id', '=', 'sa.id')
            ->join('smart_ads_details as sad', 'sap.smart_ad_id', '=', 'sad.smart_ad_id')
            ->where('sap.placement_key', 'topics_page')
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
            // increment views
            DB::table('smart_ads')->where('id', $ad->smart_ad_id)->increment('views');

            $image            = $ad->vertical_image ? url('storage/' . $ad->vertical_image) : null;
            $horizontal_image = $ad->horizontal_image ? url('storage/' . $ad->horizontal_image) : null;

            $adsData[] = [
                "id"               => "ad_" . $ad->smart_ad_id,
                "smart_ad_id"      => $ad->smart_ad_id,
                "type"             => "ad",
                "name"             => $ad->name,
                "title"            => $ad->name,
                "description"      => $ad->body,
                "body"             => $ad->body,
                "image"            => $image,
                "horizontal_image" => $horizontal_image,
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

        // 👉 Inject ads randomly into channels
        foreach ($adsData as $ad) {
            $randomIndex = rand(0, count($topics));
            array_splice($topics, $randomIndex, 0, [$ad]);
        }
        return response()->json([
            'error'       => false,
            'message'     => 'Topics fetched successfully.',
            'data'        => $topics,
            'is_ads_free' => $isAdsFree,
        ]);
    }

}
