<?php
namespace App\Http\Controllers\Apis;

use App\Http\Controllers\Controller;
use App\Models\Channel;
use App\Models\NewsLanguage;
use App\Models\NewsLanguageSubscriber;
use App\Models\Post;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SuggestionPostController extends Controller
{
    /******* Get Suggestions *******/
    public function getsuggestion(Request $request)
    {
        $searchQuery = $request->input('search');
        $perPage     = $request->get('per_page', 5);

        $newsLanguageId   = $request->news_language_id;
        $newsLanguageCode = null;

        if ($newsLanguageId) {
            $newsLanguage = NewsLanguage::find($newsLanguageId);
            if ($newsLanguage) {
                $newsLanguageCode = $newsLanguage->code;
            }
        }
        // Split query into keywords
        $keywords = preg_split('/\s+/', $searchQuery, -1, PREG_SPLIT_NO_EMPTY);

        // Posts
        $posts = Post::select(
            'image',
            'video_thumb',
            'type',
            'title',
            'slug',
            DB::raw("'post' as source")
        )
            ->where('status', 'active')
            ->whereIn('type', ['video', 'youtube', 'post', 'audio'])
            ->when($newsLanguageId, function ($query) use ($newsLanguageId) {
                $query->where('news_language_id', $newsLanguageId);
            })
            ->when($keywords, function ($query) use ($keywords) {
                $query->where(function ($q) use ($keywords) {
                    foreach ($keywords as $word) {
                        $q->orWhere('title', 'LIKE', "%{$word}%");
                    }
                });
            });

        // Channels
        $channels = Channel::select(
            DB::raw('CONCAT("' . url('storage/images/') . '/", logo) as image'),
            'name as title',
            'slug',
            DB::raw('"" as video_thumb'),
            DB::raw('"" as type'),
            DB::raw("'channel' as source")
        )
            ->when($newsLanguageId, function ($query) use ($newsLanguageId) {
                $query->where('news_language_id', $newsLanguageId);
            })
            ->when($keywords, function ($query) use ($keywords) {
                $query->where(function ($q) use ($keywords) {
                    foreach ($keywords as $word) {
                        $q->orWhere('name', 'LIKE', "%{$word}%");
                    }
                });
            });

        // Topics
        $topics = Topic::select(
            DB::raw('"" as image'),
            DB::raw('"" as video_thumb'),
            DB::raw('"" as type'),
            'name as title',
            'slug',
            DB::raw("'topic' as source")
        )
            ->when($newsLanguageId, function ($query) use ($newsLanguageId) {
                $query->where('news_language_id', $newsLanguageId);
            })
            ->when($keywords, function ($query) use ($keywords) {
                $query->where(function ($q) use ($keywords) {
                    foreach ($keywords as $word) {
                        $q->orWhere('name', 'LIKE', "%{$word}%");
                    }
                });
            });

        // Merge all queries
        $suggestions = $posts
            ->union($channels)
            ->union($topics)
            ->orderBy('title', 'asc')
            ->paginate($perPage);

        $result = collect($suggestions->items())->unique(fn($item) => strtolower($item->title))->map(function ($suggestion) {
            return [
                'image'  => $suggestion->image ?: $suggestion->video_thumb,
                'title'  => $suggestion->title,
                'slug'   => $suggestion->slug,
                'source' => $suggestion->source,
            ];
        })->values();

        return response()->json([
            'error'   => false,
            'message' => 'Get suggestion successfully.',
            'data'    => $result,
        ]);
    }

    /****** Get Search Data ******/
    public function search(Request $request)
    {
        // $perPage     = $request->get('per_page', 10);
        $perPage     = $request->get('per_page', 10);
        $page        = max(1, (int) $request->get('page', 1)); // ADD THIS
        $offset      = ($page - 1) * $perPage;                 // ADD THIS
        $searchQuery = trim(strtolower($request->input('search', '')));
        $filterType  = $request->get('filter_type', 'all');
        $userId      = Auth::check() ? Auth::user()->id : 0;

        // Detect correct language(s)
        $newsLanguageId        = $request->news_language_id;
        $subscribedLanguageIds = collect();

        if ($newsLanguageId) {
            $subscribedLanguageIds = collect([$newsLanguageId]);
        } else {
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
        }

        if ($searchQuery === '') {
            return response()->json([
                'error'   => false,
                'message' => 'No search keyword provided.',
                'data'    => [],
            ]);
        }

        /**
         * 🔹 BASE POST QUERY
         */
        $postQuery = Post::select(
            'posts.id', 'posts.image', 'posts.type', 'posts.video_thumb',
            'posts.title', 'posts.slug', 'posts.view_count',
            'channels.name as channel_name', 'channels.logo as channel_logo',
            'topics.name as topic_name',
            'posts.publish_date'
        )
            ->selectRaw('IF(favorites.user_id IS NOT NULL, 1, 0) as is_favorite')

            ->selectRaw("
            CASE
                WHEN LOWER(posts.title) = ? OR LOWER(posts.slug) = ? THEN 0
                ELSE 1
            END AS suggestion_priority
        ", [$searchQuery, $searchQuery])

            ->selectRaw("
            CASE
                WHEN LOWER(posts.title) LIKE ? THEN 1
                WHEN LOWER(posts.slug) LIKE ? THEN 2
                WHEN LOWER(channels.name) LIKE ? THEN 3
                WHEN LOWER(topics.name) LIKE ? THEN 4
                ELSE 5
            END AS relevance
        ", [
                "%{$searchQuery}%",
                "%{$searchQuery}%",
                "%{$searchQuery}%",
                "%{$searchQuery}%",
            ])

            ->join('channels', 'posts.channel_id', '=', 'channels.id')
            ->join('topics', 'posts.topic_id', '=', 'topics.id')

            ->leftJoin('favorites', function ($join) use ($userId) {
                $join->on('posts.id', '=', 'favorites.post_id')
                    ->where('favorites.user_id', $userId);
            })

            ->where('posts.status', 'active')
            ->when($subscribedLanguageIds->isNotEmpty(), function ($query) use ($subscribedLanguageIds) {
                $query->whereIn('posts.news_language_id', $subscribedLanguageIds);
            })

            ->where(function ($q) use ($searchQuery) {
                $q->where('posts.title', 'LIKE', "%{$searchQuery}%")
                    ->orWhere('posts.slug', 'LIKE', "%{$searchQuery}%")
                    ->orWhere('channels.name', 'LIKE', "%{$searchQuery}%")
                    ->orWhere('topics.name', 'LIKE', "%{$searchQuery}%");
            });

        /**
         * 🔥 APPLY FILTER TYPE ON QUERY (BEFORE GET → OPTIMIZED)
         */
        if ($filterType === 'article') {
            $postQuery->where('posts.type', 'post');
        } elseif ($filterType === 'video') {
            $postQuery->whereIn('posts.type', ['video', 'youtube']);
        } elseif ($filterType === 'audio') {
            $postQuery->where('posts.type', 'audio');
        }

        /**
         * 🔹 ORDERING
         */
        $posts = $postQuery
            ->orderBy('suggestion_priority', 'ASC') // Only 1 exact match first
            ->orderBy('relevance', 'ASC')           // Then keyword-based
            ->orderBy('posts.publish_date', 'DESC')

            ->limit($perPage)
            ->offset($offset)
            ->get();

        /**
         * 🔹 FORMAT POSTS
         */
        $posts->transform(function ($item) {
            $item->image        = $item->image ? url('storage/posts/' . $item->image) : '';
            $item->video_thumb  = $item->video_thumb ?? '';
            $item->publish_date = $item->publish_date
                ? \Carbon\Carbon::parse($item->publish_date)->diffForHumans()
                : '';
            $item->channel_logo = $item->channel_logo
                ? url('storage/images/' . $item->channel_logo)
                : '';

            // data_type for ALL
            $item->data_type = $item->type === 'post'
                ? 'article'
                : $item->type;

            return $item;
        });

        /**
         * 🔥 FINAL DATA BASED ON FILTER
         */
        if ($filterType === 'all') {

            // CHANNELS
            $channels = Channel::where('name', 'LIKE', "%{$searchQuery}%")
                ->when($subscribedLanguageIds->isNotEmpty(), function ($query) use ($subscribedLanguageIds) {
                    $query->whereIn('news_language_id', $subscribedLanguageIds);
                })
                ->limit($perPage)
                ->offset($offset) // ✅ ADD THIS
                ->get()
                ->map(function ($item) {
                    return [
                        'id'        => $item->id,
                        'title'     => $item->name, // Changed from name to title for consistency
                        'slug'      => $item->slug,
                        'logo'      => url('storage/images/' . $item->logo),
                        'data_type' => 'channel',
                    ];
                });

            // TOPICS
            $topics = Topic::where('name', 'LIKE', "%{$searchQuery}%")
                ->when($subscribedLanguageIds->isNotEmpty(), function ($query) use ($subscribedLanguageIds) {
                    $query->whereIn('news_language_id', $subscribedLanguageIds);
                })
                ->limit($perPage)
                ->offset($offset) // ✅ ADD THIS
                ->get()
                ->map(function ($item) {
                    return [
                        'id'        => $item->id,
                        'title'     => $item->name, // Changed from name to title for consistency
                        'slug'      => $item->slug,
                        'data_type' => 'topic',
                    ];
                });

            $finalData = collect()
                ->merge($posts)
                ->merge($channels)
                ->merge($topics)
                ->unique(fn($item) => ($item['data_type'] ?? $item->data_type) . '_' . ($item['id'] ?? $item->id))
                ->values();

        } elseif ($filterType === 'channel') {

            $finalData = Channel::where('name', 'LIKE', "%{$searchQuery}%")
                ->when($subscribedLanguageIds->isNotEmpty(), function ($query) use ($subscribedLanguageIds) {
                    $query->whereIn('news_language_id', $subscribedLanguageIds);
                })
                ->limit($perPage)
                ->get()
                ->map(function ($item) {
                    return [
                        'id'        => $item->id,
                        'title'     => $item->name,
                        'slug'      => $item->slug,
                        'logo'      => url('storage/images/' . $item->logo),
                        'data_type' => 'channel',
                    ];
                })->unique(fn($item) => strtolower($item['title']))->values();

        } elseif ($filterType === 'topic') {

            $finalData = Topic::where('name', 'LIKE', "%{$searchQuery}%")
                ->when($subscribedLanguageIds->isNotEmpty(), function ($query) use ($subscribedLanguageIds) {
                    $query->whereIn('news_language_id', $subscribedLanguageIds);
                })
                ->limit($perPage)
                ->get()
                ->map(function ($item) {
                    return [
                        'id'        => $item->id,
                        'title'     => $item->name,
                        'slug'      => $item->slug,
                        'logo'      => url('storage/images/' . $item->logo),
                        'data_type' => 'topic',
                    ];
                })->unique(fn($item) => strtolower($item['title']))->values();

        } else {
            // article, video, audio
            $finalData = $posts->values();
        }

        /**
         * ✅ RESPONSE
         */
        return response()->json([
            'error'   => false,
            'message' => 'Search results fetched successfully.',
            'data'    => $finalData,
        ]);
    }
}
