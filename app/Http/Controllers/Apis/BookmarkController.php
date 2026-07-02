<?php
namespace App\Http\Controllers\Apis;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use App\Models\NewsLanguage;
use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BookmarkController extends Controller
{
    const STORAGE_PATH         = '/storage/';
    const IS_FAVORIT_CONDITION = 'IF(favorites.user_id IS NOT NULL, 1, 0) as is_favorite';

    /**
     * This Method Show Bookmark Posts
     */
    public function index(Request $request)
    {
        if (Auth::check()) {
            $userId = Auth::user()->id;
            // Get pagination parameters
            $page      = $request->get('page', 1);
            $perPage   = $request->get('per_page', 10);
            $offset    = ($page - 1) * $perPage;
            $bookmarks = Favorite::select('posts.id', 'posts.title', 'posts.image', 'posts.publish_date', 'posts.favorite', 'posts.status')
                ->join('posts', 'favorites.post_id', '=', 'posts.id')
                ->where('favorites.user_id', $userId)
                ->orderBy('favorites.id', 'desc')
                ->where('posts.status', 'active')
                ->offset($offset)
                ->limit($perPage)
                ->get()
                ->map(function ($item) {
                    $item->publish_date = Carbon::parse($item->publish_date)->format('d M Y');
                    return $item;
                });

            $isAdsFree = false;
            if ($user && $user->subscription) {
                $isAdsFree = $user->subscription->feature->is_ads_free ?? false;
            }
            return response()->json([
                'error'       => false,
                'message'     => $bookmarks->isEmpty() ? 'No posts found' : 'Posts fetched successfully',
                'data'        => $bookmarks,
                'is_ads_free' => $isAdsFree,

            ]);
        } else {
            return response()->json([
                'error'   => true,
                'message' => "User is not authenticated.",
            ]);
        }
    }

    /**
     * Normalize news language IDs from request header
     *
     * @param string|null $newsLanguageIdHeader
     * @return array
     */
    protected function normalizeNewsLanguageIds($newsLanguageIdHeader)
    {
        if (empty($newsLanguageIdHeader)) {
            return [];
        }

        // Handle comma-separated values
        if (strpos($newsLanguageIdHeader, ',') !== false) {
            return array_map('intval', explode(',', $newsLanguageIdHeader));
        }

        // Handle single value
        return [(int) $newsLanguageIdHeader];
    }
    /**
     * Discover Posts
     */

    public function discoverPosts(Request $request)
    {
        $user   = Auth::user();
        $userId = $user ? $user->id : null;

        try {
            $page    = $request->get('page', 1);
            $perPage = $request->get('per_page', 10);
            $offset  = ($page - 1) * $perPage;

            // Normalize and validate news_language_id(s)
            $newsLanguageIds = $this->normalizeNewsLanguageIds($request->news_language_id);

            $postsQuery = Post::select([
                'posts.id',
                'posts.image',
                'posts.title',
                'posts.slug',
                'posts.description',
                'posts.favorite',
                'posts.publish_date',
                'channels.name as channel_name',
                'channels.slug as channel_slug',
                'posts.status',
            ])
                ->selectRaw('IF(favorites.user_id IS NOT NULL, 1, 0) as is_favorite')
                // ->join('channels', 'posts.channel_id', '=', 'channels.id')
                ->join('channels', function ($join) {
                    $join->on('posts.channel_id', '=', 'channels.id')
                        ->where('channels.status', 'active');
                })
                ->leftjoin('topics', function ($join) {
                    $join->on('posts.topic_id', '=', 'topics.id')
                        ->where('topics.status', 'active');
                })
                ->where('posts.status', 'active')
                ->leftJoin('favorites', function ($join) use ($userId) {
                    $join->on('posts.id', '=', 'favorites.post_id')
                        ->where('favorites.user_id', '=', $userId);
                })
                ->where('posts.image', '!=', '');

            // Filter by selected languages if provided
            if (! empty($newsLanguageIds)) {
                $validLanguageIds = NewsLanguage::whereIn('id', $newsLanguageIds)->pluck('id')->toArray();
                if (! empty($validLanguageIds)) {
                    $postsQuery->whereIn('posts.news_language_id', $validLanguageIds);
                }
            } else {
                // Fetch default active language
                $defaultActiveLanguage = \App\Models\NewsLanguage::where('is_active', 1)->first();
                if ($defaultActiveLanguage) {
                    $postsQuery->where('posts.news_language_id', $defaultActiveLanguage->id);
                }
            }

            $postsCollection = $postsQuery
                ->orderBy('posts.publish_date', 'desc')
                ->offset($offset)
                ->limit($perPage)
                ->get()
                ->map(function ($item) {
                    $item->publish_date = Carbon::parse($item->publish_date)->format('D, d M Y');
                    $item->description  = strip_tags(html_entity_decode($item->description));
                    return $item;
                });

            // Convert collection -> plain array BEFORE using array_splice
            $posts = $postsCollection->values()->all();

            $isAdsFree = false;
            if ($user && $user->subscription) {
                $isAdsFree = $user->subscription->feature->is_ads_free ?? false;
            }

            // Fetch ads only if not ads-free
            $ads = DB::table('smart_ad_placements as sap')
                ->join('smart_ads as sa', 'sap.smart_ad_id', '=', 'sa.id')
                ->join('smart_ads_details as sad', 'sap.smart_ad_id', '=', 'sad.smart_ad_id')
                ->where('sap.placement_key', 'discover_floating')
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
                $randomIndex = rand(0, count($posts));
                array_splice($posts, $randomIndex, 0, [$ad]);
            }
            return response()->json([
                'success'     => true,
                'message'     => 'Posts fetched successfully.',
                'data'        => $posts,
                'is_ads_free' => $isAdsFree,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to fetch posts at this time. Please try again later.',
            ], 500);
        }
    }
}
