<?php
namespace App\Http\Controllers\Apis;

use App\Http\Controllers\Controller;
use App\Models\Feature;
use App\Models\NewsLanguage;
use App\Models\Post;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VideoApiController extends Controller
{
    const STORAGE_PATH         = 'storage/images/';
    const IS_FAVORIT_CONDITION = 'IF(favorites.user_id IS NOT NULL, 1, 0) as is_favorite';

    public function getVideos(Request $request)
    {
        $user               = Auth::user();
        $userId             = $user->id ?? "";
        $perPage            = request()->get('per_page', 10);
        $slug               = request()->get('slug');
        $requestPlanLimit   = (int) $request->plan_article_count;

        $newsLanguageId   = $request->news_language_id;
        $newsLanguageCode = null;

        // Validate news_language_id and fetch its code if valid
        if ($newsLanguageId) {
            $newsLanguage = NewsLanguage::find($newsLanguageId);
            if ($newsLanguage) {
                $newsLanguageCode = $newsLanguage->code;
            }
        }
        $videosQuery = Post::query()
            ->select([
                'posts.*',
                'channels.name as channel_name',
                'channels.slug as channel_slug',
            ])
            ->where('posts.status', 'active')
            ->selectRaw('CONCAT("' . rtrim(url(self::STORAGE_PATH), '/') . '/' . '", channels.logo) as channel_logo')
            ->selectRaw(self::IS_FAVORIT_CONDITION) // Add is_favorite condition
                                                // ->join('channels', 'posts.channel_id', '=', 'channels.id')
            ->join('channels', function ($join) {
                $join->on('posts.channel_id', '=', 'channels.id')
                    ->where('channels.status', 'active');
            })
            ->leftJoin('favorites', function ($join) use ($userId) {
                $join->on('posts.id', '=', 'favorites.post_id')
                    ->where('favorites.user_id', '=', $userId);
            })
            ->where('video', '!=', '');

        if ($newsLanguageId) {
            $videosQuery->where('posts.news_language_id', $newsLanguageId);
        }

        if ($slug) {
            $videosQuery->orderByRaw('CASE WHEN posts.slug = ? THEN 0 ELSE 1 END', [$slug])
                ->orderBy('posts.publish_date', 'desc');
        } else {
            $videosQuery->orderBy('posts.publish_date', 'desc');
        }

        $videos = $videosQuery
            ->latest('posts.publish_date')
            ->paginate($perPage)
            ->through(function ($post) {
                if ($post->publish_date) {
                    $post->publish_date = Carbon::parse($post->publish_date)->diffForHumans();
                } elseif ($post->pubdate) {
                    $post->pubdate = Carbon::parse($post->pubdate)->diffForHumans();
                }

                $postArray = $post->toArray();

                // Replace null values with empty strings
                array_walk_recursive($postArray, function (&$value) {
                    if (is_null($value)) {
                        $value = "";
                    }
                });

                return $postArray;
            });

        $isAdsFree = false;
        if ($user && $user->subscription) {
            $isAdsFree = $user->subscription->feature->is_ads_free ?? false;
        }

        $ads = [];

        // ✅ Fetch multiple ads (collection)
        $adsCollection = DB::table('smart_ad_placements as sap')
            ->join('smart_ads as sa', 'sap.smart_ad_id', '=', 'sa.id')
            ->join('smart_ads_details as sad', 'sap.smart_ad_id', '=', 'sad.smart_ad_id')
            ->where('sap.placement_key', 'video_floating')
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



        // ================= SUBSCRIPTION =================
        $activeSubscription = $user ? Subscription::with('feature')
            ->where('user_id', $user->id ?? "")
            ->where('status', 'active')
            ->whereDate('end_date', '>=', now())
            ->first() : null;

        $features = $activeSubscription
            ? Feature::where('plan_id', $activeSubscription->plan_id)->first()
            : null;



        // ✅ Subscription Article Count Increment (Subscription Flow)
        if ($activeSubscription && $requestPlanLimit == 1) {
            $plan_total_max_articles = $activeSubscription->feature->number_of_articles ?? 0;
            if ($activeSubscription->article_count < $plan_total_max_articles) {
                $activeSubscription->increment('article_count');
                $activeSubscription->refresh();
            }
        }
        // 👉 membership plan data
        $membership_plan_count = [
            'is_ads_free'              => $isAdsFree,
            'news_language_code'       => $newsLanguageCode,
            'plan_status'              => ($activeSubscription && $activeSubscription->status == 'active') ? true : false,
            'plan_article_count'       => optional($activeSubscription)->article_count ?? 0,
            'plan_total_max_articles'  => $features ? $features->number_of_articles : 0,
        ];

        // 👉 get videos
        $videos_data = $videos->items();

        // 👉 inject ads into video data
        if (! empty($ads)) {
            $randomIndex = rand(0, count($videos_data));

            // ensure ads format is correct
            if (is_array($ads)) {
                $ads['type'] = 'ads';
            } elseif (is_object($ads)) {
                $ads->type = 'ads';
            }

            array_splice($videos_data, $randomIndex, 0, [$ads]);
        }

        // 👉 return response with TWO objects
        return response()->json([
            'error'   => false,
            'message' => "Videos retrieved successfully!!",
            'data'    => [
                'video_data'            => $videos_data,
                'membership_plan_count' => $membership_plan_count,
            ],
        ]);
    }
}
