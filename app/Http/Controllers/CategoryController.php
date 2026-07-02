<?php
namespace App\Http\Controllers;

use App\Models\NewsLanguage;
use App\Models\NewsLanguageSubscriber;
use App\Models\Post;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($topic = null)
    {
        $perPage = 15;

        $userId = Auth::user()->id ?? 0;

        $defaultImage = Setting::where('name', 'default_image')->first()->value ?? null;
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

        $getPosts = Post::select(
            'posts.id', 'posts.slug', 'posts.image', 'posts.type', 'posts.video_thumb', 'posts.comment', 'posts.view_count',
            'channels.name as channel_name', 'channels.logo as channel_logo', 'channels.slug as channel_slug',
            'topics.name as topic_name', 'topics.slug as topic_slug', 'posts.title',
            'posts.favorite', 'posts.description', 'posts.status', 'posts.publish_date', 'posts.pubdate', 'posts.reaction'
        )
            ->join('channels', 'posts.channel_id', '=', 'channels.id')
            ->join('topics', 'posts.topic_id', '=', 'topics.id')
            ->where('channels.status', 'active')
            ->where('topics.status', 'active')
            ->where('posts.status', 'active')
            ->orderBy('posts.publish_date', 'Desc');

        if (! empty($topic)) {
            $getPosts->where('topics.slug', $topic);
        }

        if ($subscribedLanguageIds->isNotEmpty()) {
            $getPosts->whereIn('posts.news_language_id', $subscribedLanguageIds);
        }

        $getPosts = $getPosts->paginate($perPage);

        foreach ($getPosts as $post) {

            // Set default image if still empty
            $post->image = $post->image ?? $defaultImage;

            // Set default values for video fields
            $post->video_thumb = $post->video_thumb ?? $defaultImage;
            $post->video       = $post->video ?? $defaultImage;

            if ($post->publish_date) {
                $post->publish_date = Carbon::parse($post->publish_date)->diffForHumans();
            } elseif ($post->pubdate) {
                $post->pubdate = Carbon::parse($post->pubdate)->diffForHumans();
            }
        }

        $post_lable = Setting::get()->where('name', 'news_lable_place_holder')->first();
        $title      = $getPosts->first()->topic_name ?? 'Posts';
        $theme      = getTheme();
        $data       = compact('title', 'getPosts', 'post_lable', 'theme', 'defaultImage');
        return view('front_end/' . $theme . '/pages/topic-posts', $data);
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
    public function show(Request $request)
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
}
