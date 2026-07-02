<?php
namespace App\Http\Controllers;

use App\Models\Channel;
use App\Models\ChannelSubscriber;
use App\Models\NewsLanguage;
use App\Models\NewsLanguageSubscriber;
use App\Models\Post;
use App\Models\Setting;
use App\Models\Topic;
use App\Models\TopicFollower;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SearchPostController extends Controller
{
    const TIME_FORMATE = 'Y-m-d H:i';
    public function search(Request $request)
    {
        $userId = Auth::user()->id ?? 0;

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

        $searchQuery = $request->input('search');
        $channels    = $request->input('channel');
        $topics      = $request->input('topic');
        $filter      = $request->input('filter');

        $channel_ids = ChannelSubscriber::where('user_id', Auth::user()->id ?? 0)->pluck('channel_id')->toArray();
        $topic_ids   = TopicFollower::where('user_id', Auth::user()->id ?? 0)->pluck('topic_id')->toArray();

        $getPosts = Post::select(
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
            $getPosts->whereIn('posts.news_language_id', $subscribedLanguageIds);
        }
        if ($searchQuery) {
            $getPosts->where(function ($subQuery) use ($searchQuery) {
                $subQuery->where('posts.slug', 'LIKE', "%$searchQuery%")
                    ->orWhere('posts.title', 'LIKE', "%$searchQuery%")
                    ->orWhere('channels.slug', 'LIKE', "%$searchQuery%")
                    ->orWhere('channels.name', 'LIKE', "%$searchQuery%")
                    ->orWhere('topics.slug', 'LIKE', "%$searchQuery%")
                    ->orWhere('topics.name', 'LIKE', "%$searchQuery%");
            })
            ->groupBy('posts.id');
        }
        if (! empty($channels)) {
            $getPosts->whereIn('channels.slug', $channels);
        }
        if (! empty($topics)) {
            $getPosts->whereIn('topics.slug', $topics);
        }

        if ($filter == "most-read") {
            $getPosts->where('publish_date', '>', now()->subDays(7)->endOfDay())->orderBy('posts.view_count', 'DESC');
        } elseif ($filter == "most-liked") {

            $getPosts->orderBy('posts.favorite', 'DESC');
        } elseif ($filter == "most-recent") {

            $getPosts->orderBy('posts.publish_date', 'DESC');
        } elseif ($filter == "channels-followed") {

            $getPosts->whereIn('posts.channel_id', $channel_ids);
        } elseif ($filter == "topics-followed") {

            $getPosts->whereIn('posts.topic_id', $topic_ids);
        } else {
            $getPosts->orderBy('posts.publish_date', 'DESC');
        }

        $getPosts     = $getPosts->paginate(15)->withQueryString();
        $channels     = Channel::select('id', 'name', 'slug')->where('status', 'active')->get();
        $topics       = Topic::select('id', 'name', 'slug')->where('status', 'active')->get();
        $post_label   = Setting::get()->where('name', 'news_label_place_holder')->first();
        $defaultImage = Setting::get()->where('name', 'default_image')->first();

        foreach ($getPosts as $post) {

            $post->image = $post->image ?? url($defaultImage->value ?? 'public/front_end/classic/images/default/post-placeholder.jpg');
            if ($post->image === url('storage')) {
                $post->image = $defaultImage->value;
                // Set default values for video fields
                $post->video_thumb = $post->video_thumb ?? $defaultImage->value;
                $post->video       = $post->video ?? $defaultImage->value;
            } else {
                $post->image = $post->image;
                // Set default values for video fields
                $post->video_thumb = $post->video_thumb ?? $defaultImage->value;
                $post->video       = $post->video ?? $defaultImage->value;
            }
            if ($post->publish_date) {
                $post->publish_date_news = Carbon::parse($post->pubdate)->format(self::TIME_FORMATE);
                $post->publish_date      = Carbon::parse($post->publish_date)->diffForHumans();
            } else {
                $post->pubdate = Carbon::parse($post->pubdate)->diffForHumans();
            }
        }

        $title = $searchQuery ?? (isset($post_label->value) ? $post_label->value : __('frontend-labels.posts.all_posts'));
        $theme = getTheme();

        return view('front_end/' . $theme . '/pages/search-result', compact('getPosts', 'title', 'searchQuery', 'post_label', 'channels', 'topics', 'theme'));
    }

    public function autocomplete(Request $request)
    {
        /* ----------------------------------------------------
        STEP 1: Detect user selected news language
        ---------------------------------------------------- */
        $userId = Auth::user()->id ?? 0;

        if ($userId) {
            $subscribedLanguageIds = NewsLanguageSubscriber::where('user_id', $userId)
                ->pluck('news_language_id');
        } else {
            $sessionLanguageId = session('selected_news_language');
            if ($sessionLanguageId) {
                $subscribedLanguageIds = collect([$sessionLanguageId]);
            } else {
                $defaultActiveLanguage = NewsLanguage::where('is_active', 1)->first();
                $subscribedLanguageIds = $defaultActiveLanguage
                    ? collect([$defaultActiveLanguage->id])
                    : collect();
            }
        }

        /* ----------------------------------------------------
        STEP 2: Apply Search
        ---------------------------------------------------- */
        $searchQuery = $request->input('search');

        /* ------------------- POSTS -------------------------- */
        $posts = Post::selectRaw('title, slug, image, "post" as type, publish_date')
            ->when($subscribedLanguageIds->isNotEmpty(), function ($q) use ($subscribedLanguageIds) {
                return $q->whereIn('news_language_id', $subscribedLanguageIds);
            })
            ->when($searchQuery, function ($q) use ($searchQuery) {
                return $q->where('title', 'LIKE', "%$searchQuery%");
            });

        /* ------------------- TOPICS ------------------------- */
        $topics = Topic::selectRaw('name as title, slug, logo as image, "topic" as type, created_at as publish_date')
            ->when($subscribedLanguageIds->isNotEmpty(), function ($q) use ($subscribedLanguageIds) {
                return $q->whereIn('news_language_id', $subscribedLanguageIds);
            })
            ->when($searchQuery, function ($q) use ($searchQuery) {
                return $q->where('name', 'LIKE', "%$searchQuery%");
            });

        /* ------------------- CHANNELS ------------------------ */
        $channels = Channel::selectRaw('name as title, slug, logo as image, "channel" as type, created_at as publish_date')
            ->when($subscribedLanguageIds->isNotEmpty(), function ($q) use ($subscribedLanguageIds) {
                return $q->whereIn('news_language_id', $subscribedLanguageIds);
            })
            ->when($searchQuery, function ($q) use ($searchQuery) {
                return $q->where('name', 'LIKE', "%$searchQuery%");
            });

        /* ----------------------------------------------------
        STEP 3: Combine & Format
        --------------------------------------------------- */
        $combinedQuery = $posts->union($channels)->union($topics);

        $results = $combinedQuery->orderBy('publish_date', 'desc')->take(10)->get();

        $formattedResults = $results->map(function ($item) {
            $image = $item->image;
            if ($item->type === 'channel' || $item->type === 'topic') {
                $image = $item->image ? url('storage/images/' . $item->image) : null;
            }

            return [
                'title' => $item->title,
                'type'  => $item->type,
                'image' => $image,
            ];
        })->unique(fn($item) => strtolower($item['title']))->values();

        return response()->json($formattedResults);
    }

    public function getChannel($id)
    {
        $channel = Channel::select('id', 'name', 'logo', 'follow_count')->find($id);

        if ($channel) {
            $channel->channel_logo = url('storage/images/' . $channel->logo);
            return response()->json($channel);
        }

        return response()->json(['error' => 'Channel not found'], 404);
    }

    /**
     * AJAX search for sidebar tabs
     */
    public function ajaxSearch(Request $request)
    {
        $userId = Auth::user()->id ?? 0;

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

        $searchQuery = $request->input('search');
        $tab = $request->input('tab', 'all');

        if (empty($searchQuery)) {
            return response()->json(['results' => [], 'total' => 0]);
        }

        $defaultImage = Setting::get()->where('name', 'default_image')->first();
        $defaultImageUrl = $defaultImage->value ?? asset('front_end/classic/images/default/post-placeholder.jpg');
        $results = [];

        // For channels tab
        if ($tab === 'channels') {
            $channels = Channel::select('id', 'name', 'slug', 'logo')
                ->where('status', 'active')
                ->when($subscribedLanguageIds->isNotEmpty(), fn($q) => $q->whereIn('news_language_id', $subscribedLanguageIds))
                ->where(function ($q) use ($searchQuery) {
                    $q->where('name', 'LIKE', "%$searchQuery%")
                      ->orWhere('slug', 'LIKE', "%$searchQuery%");
                })
                ->get();

            foreach ($channels as $channel) {
                $results[] = [
                    'title' => $channel->name,
                    'url'   => url('channels/' . $channel->slug),
                    'image' => $channel->logo ? url('storage/images/' . $channel->logo) : $defaultImageUrl,
                    'type'  => 'channel',
                ];
            }

            return response()->json(['results' => $results, 'total' => count($results)]);
        }

        // For topics tab
        if ($tab === 'topics') {
            $topics = Topic::select('id', 'name', 'slug', 'logo')
                ->where('status', 'active')
                ->when($subscribedLanguageIds->isNotEmpty(), fn($q) => $q->whereIn('news_language_id', $subscribedLanguageIds))
                ->where(function ($q) use ($searchQuery) {
                    $q->where('name', 'LIKE', "%$searchQuery%")
                      ->orWhere('slug', 'LIKE', "%$searchQuery%");
                })
                ->get();

            foreach ($topics as $topic) {
                $results[] = [
                    'title' => $topic->name,
                    'url'   => url('topics/' . $topic->slug),
                    'image' => $topic->logo ? url('storage/images/' . $topic->logo) : $defaultImageUrl,
                    'type'  => 'topic',
                ];
            }

            return response()->json(['results' => $results, 'total' => count($results)]);
        }

        // For post types: all, post, video, audio
        $getPosts = Post::select(
            'posts.id',
            'posts.slug',
            'posts.image',
            'posts.title',
            'posts.type',
            'posts.video_thumb',
            'channels.name as channel_name',
            'channels.slug as channel_slug',
            'topics.name as topic_name',
            'topics.slug as topic_slug',
        )
            ->where('posts.status', 'active')
            ->leftJoin('channels', function ($join) {
                $join->on('posts.channel_id', '=', 'channels.id')
                    ->where('channels.status', 'active');
            })
            ->leftjoin('topics', function ($join) {
                $join->on('posts.topic_id', '=', 'topics.id')
                    ->where('topics.status', 'active');
            });

        if ($subscribedLanguageIds->isNotEmpty()) {
            $getPosts->whereIn('posts.news_language_id', $subscribedLanguageIds);
        }

        $getPosts->where(function ($subQuery) use ($searchQuery) {
            $subQuery->where('posts.title', 'LIKE', "%$searchQuery%")
                ->orWhere('posts.slug', 'LIKE', "%$searchQuery%")
                ->orWhere('channels.name', 'LIKE', "%$searchQuery%")
                ->orWhere('topics.name', 'LIKE', "%$searchQuery%");
        })->groupBy('posts.id');

        // Filter by post type for specific tabs
        if ($tab === 'post') {
            $getPosts->where('posts.type', 'post');
        } elseif ($tab === 'video') {
            $getPosts->whereIn('posts.type', ['video', 'youtube']);
        } elseif ($tab === 'audio') {
            $getPosts->where('posts.type', 'audio');
        }

        $getPosts->orderBy('posts.publish_date', 'DESC');
        $posts = $getPosts->get();

        foreach ($posts as $post) {
            $img = $post->image ?? $defaultImageUrl;
            if ($post->type === 'video' || $post->type === 'youtube') {
                $img = $post->video_thumb ?? $img;
            }
            if ($img === url('storage')) {
                $img = $defaultImageUrl;
            }

            $typeLabel = ucfirst($post->type);
            if ($post->type === 'youtube') {
                $typeLabel = 'Video';
            }

            $results[] = [
                'title'        => $post->title,
                'url'          => url('posts/' . $post->slug),
                'image'        => $img,
                'type'         => $typeLabel,
                'channel_name' => $post->channel_name,
                'topic_name'   => $post->topic_name,
            ];
        }

        // If tab is 'all', also include channels and topics
        if ($tab === 'all') {
            // Add matching channels
            $matchedChannels = Channel::select('id', 'name', 'slug', 'logo')
                ->where('status', 'active')
                ->when($subscribedLanguageIds->isNotEmpty(), fn($q) => $q->whereIn('news_language_id', $subscribedLanguageIds))
                ->where(function ($q) use ($searchQuery) {
                    $q->where('name', 'LIKE', "%$searchQuery%")
                        ->orWhere('slug', 'LIKE', "%$searchQuery%");
                })
                ->get();

            foreach ($matchedChannels as $channel) {
                array_unshift($results, [
                    'title' => $channel->name,
                    'url'   => url('channels/' . $channel->slug),
                    'image' => $channel->logo ? url('storage/images/' . $channel->logo) : $defaultImageUrl,
                    'type'  => 'Channel',
                ]);
            }

            // Add matching topics
            $matchedTopics = Topic::select('id', 'name', 'slug', 'logo')
                ->where('status', 'active')
                ->when($subscribedLanguageIds->isNotEmpty(), fn($q) => $q->whereIn('news_language_id', $subscribedLanguageIds))
                ->where(function ($q) use ($searchQuery) {
                    $q->where('name', 'LIKE', "%$searchQuery%")
                        ->orWhere('slug', 'LIKE', "%$searchQuery%");
                })
                ->get();

            foreach ($matchedTopics as $topic) {
                array_unshift($results, [
                    'title' => $topic->name,
                    'url'   => url('topics/' . $topic->slug),
                    'image' => $topic->logo ? url('storage/images/' . $topic->logo) : $defaultImageUrl,
                    'type'  => 'Topic',
                ]);
            }
        }

        $finalResults = collect($results)->unique(fn($item) => strtolower($item['title']))->values()->all();
        return response()->json(['results' => $finalResults, 'total' => count($finalResults)]);
    }
}
