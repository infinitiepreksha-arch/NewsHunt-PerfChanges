<?php
namespace App\Http\Controllers;

use App\Models\Channel;
use App\Models\Post;
use App\Models\Story;
use App\Models\Topic;
use App\Models\Video;
use App\Models\NewsLanguage;
use App\Models\NewsLanguageSubscriber;
use Illuminate\Support\Facades\Auth;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class SitemapController extends Controller
{
    public function index()
    {
        ob_clean();

        // ---------------------------------------------
        // 🟦 Get Language Preference Logic
        // ---------------------------------------------
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

        // If still empty, fallback (avoid empty sitemap)
        if ($subscribedLanguageIds->isEmpty()) {
            $subscribedLanguageIds = NewsLanguage::where('is_active', 1)
                ->pluck('id');
        }

        // ---------------------------------------------
        // 🟦 Create sitemap
        // ---------------------------------------------
        $sitemap = Sitemap::create();
        $now = now();

        // ---------------------------------------------
        // 🟦 Static Pages (Top)
        // ---------------------------------------------
        $staticPagesStart = [
            '/'           => 1.0,
            'channels'    => 0.9,
            'topics'      => 0.9,
            'posts'       => 0.9,
            'webstories'  => 0.9,
            'videos'      => 0.9,
            'membership'  => 0.7,
            'sponsor-ads' => 0.9,
        ];

        foreach ($staticPagesStart as $path => $priority) {
            $sitemap->add(
                Url::create(url($path))
                    ->setPriority($priority)
                    ->setLastModificationDate($now)
            );
        }

        // ---------------------------------------------
        // 🟦 Dynamic: Posts (Language Filter)
        // ---------------------------------------------
        $posts = Post::where('status', 'active')
            ->whereIn('news_language_id', $subscribedLanguageIds)
            ->latest('publish_date')
            ->take(15)
            ->get();

        foreach ($posts as $post) {
            $lastMod = $post->updated_at ?? $post->created_at ?? $now;

            $sitemap->add(
                Url::create(url("posts/{$post->slug}"))
                    ->setPriority(0.8)
                    ->setChangeFrequency('daily')
                    ->setLastModificationDate($lastMod)
            );
        }

        // ---------------------------------------------
        // 🟦 Dynamic: Channels (Language Filter)
        // ---------------------------------------------
        $channels = Channel::where('status', 'active')
            ->whereIn('news_language_id', $subscribedLanguageIds)
            ->latest()
            ->take(10)
            ->get();

        foreach ($channels as $channel) {
            $lastMod = $channel->updated_at ?? $channel->created_at ?? $now;

            $sitemap->add(
                Url::create(url("channels/{$channel->slug}"))
                    ->setPriority(0.8)
                    ->setChangeFrequency('weekly')
                    ->setLastModificationDate($lastMod)
            );
        }

        // ---------------------------------------------
        // 🟦 Dynamic: Topics (Language Filter)
        // ---------------------------------------------
        $topics = Topic::where('status', 'active')
            ->whereIn('news_language_id', $subscribedLanguageIds)
            ->latest()
            ->take(10)
            ->get();

        foreach ($topics as $topic) {
            $lastMod = $topic->updated_at ?? $topic->created_at ?? $now;

            $sitemap->add(
                Url::create(url("topics/{$topic->slug}"))
                    ->setPriority(0.8)
                    ->setChangeFrequency('weekly')
                    ->setLastModificationDate($lastMod)
            );
        }

        // ---------------------------------------------
        // 🟦 Dynamic: Webstories (Language Filter)
        // ---------------------------------------------
        $stories = Story::with(['story_slides', 'topic'])
            ->whereHas('topic', function ($q) use ($subscribedLanguageIds) {
                $q->whereIn('news_language_id', $subscribedLanguageIds);
            })
            ->whereHas('story_slides')
            ->latest()
            ->take(10)
            ->get();

        foreach ($stories as $story) {
            $lastMod = $story->updated_at ?? $story->created_at ?? $now;

            $sitemap->add(
                Url::create(url("webstories/{$story->topic->slug}/{$story->slug}"))
                    ->setPriority(0.8)
                    ->setChangeFrequency('weekly')
                    ->setLastModificationDate($lastMod)
            );
        }

        // ---------------------------------------------
        // 🟦 Dynamic: Videos (If exists)
        // ---------------------------------------------
        if (class_exists(Video::class)) {
            $videos = Video::where('status', 'active')
                ->whereIn('news_language_id', $subscribedLanguageIds)
                ->latest()
                ->take(10)
                ->get();

            foreach ($videos as $video) {
                $lastMod = $video->updated_at ?? $video->created_at ?? $now;

                $sitemap->add(
                    Url::create(url("videos/{$video->slug}"))
                        ->setPriority(0.7)
                        ->setChangeFrequency('weekly')
                        ->setLastModificationDate($lastMod)
                );
            }
        }

        // ---------------------------------------------
        // 🟦 Static Pages (Bottom)
        // ---------------------------------------------
        $staticPagesEnd = [
            'my-account'          => 0.5,
            'terms-and-condition' => 0.3,
            'privacy-policy'      => 0.3,
            'about-us'            => 0.6,
            'contact-us'          => 0.6,
        ];

        foreach ($staticPagesEnd as $path => $priority) {
            $sitemap->add(
                Url::create(url($path))
                    ->setPriority($priority)
                    ->setLastModificationDate($now)
            );
        }

        return response($sitemap->render(), 200)
            ->header('Content-Type', 'application/xml');
    }
}
