<?php
namespace App\Http\Controllers;

use App\Models\Channel;
use App\Models\ChannelSubscriber;
use App\Models\ENewspaper;
use App\Models\NewsLanguage;
use App\Models\NewsLanguageSubscriber;
use App\Models\Post;
use App\Models\Setting;
use App\Models\Story;
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

        $searchQuery   = $request->input('search');
        $channels      = $request->input('channel', []);
        $topics        = $request->input('topic', []);
        $selectedTypes = $request->input('post_type', []);
        $filter        = $request->input('filter');

        $channel_ids = ChannelSubscriber::where('user_id', Auth::user()->id ?? 0)->pluck('channel_id')->toArray();
        $topic_ids   = TopicFollower::where('user_id', Auth::user()->id ?? 0)->pluck('topic_id')->toArray();

        $builders = [];

        // 1. Post Builder (Articles, Videos, YouTube, Audios)
        $includePosts = empty($selectedTypes) || array_intersect(['post', 'video', 'youtube', 'audio'], $selectedTypes);
        if ($includePosts) {
            $postBuilder = Post::selectRaw("
                posts.id as id,
                posts.title as title,
                posts.slug as slug,
                posts.image as image,
                posts.type as type,
                posts.publish_date as publish_date,
                posts.view_count as view_count,
                posts.reaction as reaction,
                posts.comment as comment,
                channels.name as channel_name,
                channels.slug as channel_slug,
                channels.logo as channel_logo,
                topics.name as topic_name,
                topics.slug as topic_slug
            ")
            ->where('posts.status', 'active')
            ->leftJoin('channels', function ($join) {
                $join->on('posts.channel_id', '=', 'channels.id')
                    ->where('channels.status', 'active');
            })
            ->leftJoin('topics', function ($join) {
                $join->on('posts.topic_id', '=', 'topics.id')
                    ->where('topics.status', 'active');
            });

            if ($subscribedLanguageIds->isNotEmpty()) {
                $postBuilder->whereIn('posts.news_language_id', $subscribedLanguageIds);
            }

            if ($searchQuery) {
                $postBuilder->where(function ($subQuery) use ($searchQuery) {
                    $subQuery->where('posts.title', 'LIKE', "%$searchQuery%")
                        ->orWhere('posts.slug', 'LIKE', "%$searchQuery%")
                        ->orWhere('channels.name', 'LIKE', "%$searchQuery%")
                        ->orWhere('topics.name', 'LIKE', "%$searchQuery%");
                });
            }

            if (! empty($channels)) {
                $postBuilder->whereIn('channels.slug', $channels);
            }

            if (! empty($topics)) {
                $postBuilder->whereIn('topics.slug', $topics);
            }

            if (! empty($selectedTypes)) {
                $postTypes = [];
                if (in_array('post', $selectedTypes)) $postTypes[] = 'post';
                if (in_array('video', $selectedTypes)) $postTypes[] = 'video';
                if (in_array('youtube', $selectedTypes)) $postTypes[] = 'youtube';
                if (in_array('audio', $selectedTypes)) $postTypes[] = 'audio';
                $postBuilder->whereIn('posts.type', $postTypes);
            }

            $builders[] = $postBuilder;
        }

        // 2. Story Builder (Web Stories)
        $includeStories = empty($selectedTypes) || in_array('story', $selectedTypes);
        if ($includeStories && empty($channels)) { // Stories don't have channels
            $storyBuilder = Topic::selectRaw("
                stories.id as id,
                stories.title as title,
                stories.slug as slug,
                NULL as image,
                'story' as type,
                stories.created_at as publish_date,
                stories.story_count as view_count,
                0 as reaction,
                0 as comment,
                NULL as channel_name,
                NULL as channel_slug,
                NULL as channel_logo,
                topics.name as topic_name,
                topics.slug as topic_slug
            ")
            ->join('stories', 'stories.topic_id', '=', 'topics.id')
            ->whereExists(function ($q) {
                $q->select(\Illuminate\Support\Facades\DB::raw(1))
                  ->from('story_slides')
                  ->whereColumn('story_slides.story_id', 'stories.id');
            });

            if ($subscribedLanguageIds->isNotEmpty()) {
                $storyBuilder->whereIn('stories.news_language_id', $subscribedLanguageIds);
            }

            if ($searchQuery) {
                $storyBuilder->where(function ($subQuery) use ($searchQuery) {
                    $subQuery->where('stories.title', 'LIKE', "%$searchQuery%")
                        ->orWhere('stories.slug', 'LIKE', "%$searchQuery%")
                        ->orWhere('topics.name', 'LIKE', "%$searchQuery%");
                });
            }

            if (! empty($topics)) {
                $storyBuilder->whereIn('topics.slug', $topics);
            }

            $builders[] = $storyBuilder;
        }

        // 3. ENewspaper Builder (Newspapers, Magazines)
        $includeENewspapers = empty($selectedTypes) || array_intersect(['paper', 'magazine'], $selectedTypes);
        if ($includeENewspapers) {
            $enewspaperBuilder = ENewspaper::selectRaw("
                e_newspapers.id as id,
                channels.name as title,
                NULL as slug,
                e_newspapers.thumbnail as image,
                e_newspapers.type as type,
                e_newspapers.date as publish_date,
                0 as view_count,
                0 as reaction,
                0 as comment,
                channels.name as channel_name,
                channels.slug as channel_slug,
                channels.logo as channel_logo,
                topics.name as topic_name,
                topics.slug as topic_slug
            ")
            ->leftJoin('channels', function ($join) {
                $join->on('e_newspapers.channel_id', '=', 'channels.id')
                    ->where('channels.status', 'active');
            })
            ->leftJoin('topics', function ($join) {
                $join->on('e_newspapers.topic_id', '=', 'topics.id')
                    ->where('topics.status', 'active');
            });

            if ($subscribedLanguageIds->isNotEmpty()) {
                $enewspaperBuilder->whereIn('e_newspapers.news_language_id', $subscribedLanguageIds);
            }

            if ($searchQuery) {
                $enewspaperBuilder->where(function ($subQuery) use ($searchQuery) {
                    $subQuery->where('channels.name', 'LIKE', "%$searchQuery%")
                        ->orWhere('topics.name', 'LIKE', "%$searchQuery%");
                });
            }

            if (! empty($channels)) {
                $enewspaperBuilder->whereIn('channels.slug', $channels);
            }

            if (! empty($topics)) {
                $enewspaperBuilder->whereIn('topics.slug', $topics);
            }

            if (! empty($selectedTypes)) {
                $enewspaperTypes = [];
                if (in_array('paper', $selectedTypes)) $enewspaperTypes[] = 'paper';
                if (in_array('magazine', $selectedTypes)) $enewspaperTypes[] = 'magazine';
                $enewspaperBuilder->whereIn('e_newspapers.type', $enewspaperTypes);
            }

            $builders[] = $enewspaperBuilder;
        }

        if (empty($builders)) {
            $getPosts = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15);
        } else {
            $firstBuilder = array_shift($builders);
            foreach ($builders as $b) {
                $firstBuilder->unionAll($b);
            }

            $sortField = 'publish_date';
            $sortOrder = 'DESC';

            if ($filter == "most-read") {
                $sortField = 'view_count';
            } elseif ($filter == "most-liked") {
                $sortField = 'reaction';
            } elseif ($filter == "most-recent") {
                $sortField = 'publish_date';
            } elseif ($filter == "oldest") {
                $sortField = 'publish_date';
                $sortOrder = 'ASC';
            } elseif ($filter == "most-commented") {
                $sortField = 'comment';
            }

            $getPosts = \Illuminate\Support\Facades\DB::table(\Illuminate\Support\Facades\DB::raw("({$firstBuilder->toSql()}) as unified"))
                ->mergeBindings($firstBuilder->getQuery())
                ->orderBy($sortField, $sortOrder)
                ->paginate(15)
                ->withQueryString();
        }

        $channels     = Channel::select('id', 'name', 'slug')->where('status', 'active')->get();
        $topics       = Topic::select('id', 'name', 'slug')->where('status', 'active')->get();
        $post_label   = Setting::get()->where('name', 'news_label_place_holder')->first();
        $defaultImage = Setting::get()->where('name', 'default_image')->first();
        $defaultImageUrl = $defaultImage->value ?? asset('front_end/classic/images/default/post-placeholder.jpg');

        $followedChannels = collect();
        $followedTopics = collect();
        if (Auth::check()) {
            $followedChannelIds = ChannelSubscriber::where('user_id', Auth::id())->pluck('channel_id')->toArray();
            $followedTopicIds   = TopicFollower::where('user_id', Auth::id())->pluck('topic_id')->toArray();

            $followedChannels = Channel::select('id', 'name', 'slug')
                ->where('status', 'active')
                ->whereIn('id', $followedChannelIds)
                ->get();

            $followedTopics = Topic::select('id', 'name', 'slug')
                ->where('status', 'active')
                ->whereIn('id', $followedTopicIds)
                ->get();
        }

        foreach ($getPosts as $post) {
            // Resolve images and URLs based on type
            if ($post->type === 'story') {
                $firstSlide = \Illuminate\Support\Facades\DB::table('story_slides')
                    ->where('story_id', $post->id)
                    ->orderBy('order', 'asc')
                    ->first();
                $post->image = $firstSlide && $firstSlide->image ? asset('storage/' . $firstSlide->image) : $defaultImageUrl;
                $post->url = url('webstories/' . ($post->topic_slug ?? 'general') . '/' . $post->slug);
            } elseif ($post->type === 'paper') {
                $post->image = $post->image ? asset('storage/' . $post->image) : $defaultImageUrl;
                $post->url = route('e-newspaper.pdf', $post->id);
            } elseif ($post->type === 'magazine') {
                $post->image = $post->image ? asset('storage/' . $post->image) : $defaultImageUrl;
                $post->url = route('e-magazine.pdf', $post->id);
            } else {
                $post->url = url('posts/' . $post->slug);
                $post->image = $post->image ?? $defaultImageUrl;
                if ($post->image === url('storage')) {
                    $post->image = $defaultImageUrl;
                }
                if ($post->type === 'video' || $post->type === 'youtube') {
                    $post->video_thumb = $post->video_thumb ?? $post->image;
                }
            }

            if ($post->publish_date) {
                $post->publish_date_news = Carbon::parse($post->publish_date)->format(self::TIME_FORMATE);
                $post->publish_date      = Carbon::parse($post->publish_date)->diffForHumans();
                $post->pubdate           = $post->publish_date;
            } else {
                $post->pubdate = '';
                $post->publish_date_news = '';
                $post->publish_date = '';
            }
        }

        $title = $searchQuery ?? (isset($post_label->value) ? $post_label->value : __('frontend-labels.posts.all_posts'));
        $theme = getTheme();

        if ($request->ajax()) {
            return response()->json([
                'html' => view('front_end/' . $theme . '/pages/partials/search_result_posts', compact('getPosts', 'theme', 'searchQuery', 'post_label'))->render()
            ]);
        }

        return view('front_end/' . $theme . '/pages/search-result', compact('getPosts', 'title', 'searchQuery', 'post_label', 'channels', 'topics', 'theme', 'followedChannels', 'followedTopics'));
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
