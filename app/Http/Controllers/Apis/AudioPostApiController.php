<?php
namespace App\Http\Controllers\Apis;

use App\Http\Controllers\Controller;
use App\Models\NewsLanguage;
use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AudioPostApiController extends Controller
{

    const STORAGE_PATH         = 'storage/images/';
    const IS_FAVORIT_CONDITION = 'IF(favorites.user_id IS NOT NULL, 1, 0) as is_favorite';

    protected function getAudio(Request $request)
    {
        $user    = Auth::user();
        $userId  = $user->id ?? null;
        $perPage = request()->get('per_page', 10);
        $slug    = request()->get('slug');

        $newsLanguageId   = $request->news_language_id;
        $newsLanguageCode = null;

        if ($newsLanguageId) {
            $newsLanguage = NewsLanguage::find($newsLanguageId);
            if ($newsLanguage) {
                $newsLanguageCode = $newsLanguage->code;
            }
        }

        $audiosQuery = Post::query()
            ->select([
                'posts.*',
                'channels.name as channel_name',
                'channels.slug as channel_slug',
            ])
            ->where('posts.status', 'active')
            ->selectRaw('CONCAT("' . rtrim(url(self::STORAGE_PATH), '/') . '/' . '", channels.logo) as channel_logo')
            ->selectRaw(self::IS_FAVORIT_CONDITION)
            ->join('channels', 'posts.channel_id', '=', 'channels.id')
            ->leftJoin('favorites', function ($join) use ($userId) {
                $join->on('posts.id', '=', 'favorites.post_id')
                    ->where('favorites.user_id', '=', $userId);
            })
            ->where('type', 'audio')
            ->where('audio', '!=', '');

        if ($newsLanguageId) {
            $audiosQuery->where('posts.news_language_id', $newsLanguageId);
        }

        if ($slug) {
            $audiosQuery->orderByRaw('CASE WHEN posts.slug = ? THEN 0 ELSE 1 END', [$slug])
                ->orderBy('posts.publish_date', 'desc');
        } else {
            $audiosQuery->orderBy('posts.publish_date', 'desc');
        }

        $audios = $audiosQuery
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
        $finalData = $audios->items();
        if (empty($finalData)) {
            return response()->json([
                'error'   => false,
                'message' => "audios retrieved successfully!!",
                'data'    => [],
            ]);
        } else {
            return response()->json([
                'error'              => false,
                'message'            => "audios retrieved successfully!!",
                'data'               => $finalData,
                'is_ads_free'        => $isAdsFree,
                'news_language_code' => $newsLanguageCode,
            ]);
        }
    }
}
