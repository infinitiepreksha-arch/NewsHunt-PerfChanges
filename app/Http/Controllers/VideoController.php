<?php
namespace App\Http\Controllers;

use App\Models\NewsLanguage;
use App\Models\NewsLanguageSubscriber;
use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    const TIME_FORMATE = 'Y-m-d H:i';
    private function getSettingsCache(?Request $request = null)
    {
        $request = $request ?? request();
        if ($request->attributes->has('settings_cache')) {
            return $request->attributes->get('settings_cache');
        }

        $settingsList = \Illuminate\Support\Facades\DB::table('settings')->select('name', 'value', 'type')->get();
        $settingsCache = $settingsList->keyBy('name');
        $request->attributes->set('settings_cache', $settingsCache);

        return $settingsCache;
    }

    private function getSubscribedLanguageIds($userId, ?Request $request = null)
    {
        $request = $request ?? request();
        if ($request->attributes->has('subscribed_language_ids')) {
            return $request->attributes->get('subscribed_language_ids');
        }

        if ($userId) {
            $subscribedLanguageIds = NewsLanguageSubscriber::where('user_id', $userId)->pluck('news_language_id');

            // If no subscription found, assign default
            if ($subscribedLanguageIds->isEmpty()) {
                $defaultLanguage = NewsLanguage::where('is_active', 1)->first();
                if ($defaultLanguage) {
                    NewsLanguageSubscriber::create([
                        'user_id'          => $userId,
                        'news_language_id' => $defaultLanguage->id,
                    ]);
                    $subscribedLanguageIds = collect([$defaultLanguage->id]);
                }
            }
        } else {
            $sessionLanguageId     = session('selected_news_language');
            $subscribedLanguageIds = $sessionLanguageId ? collect([$sessionLanguageId]) : collect();
        }

        $request->attributes->set('subscribed_language_ids', $subscribedLanguageIds);
        return $subscribedLanguageIds;
    }

    public function allVideos(Request $request)
    {
        $settingsCache = $this->getSettingsCache($request);
        $theme  = getTheme();
        $title  = __('frontend-labels.news_videos.title');
        $userId = auth()->check() ? auth()->id() : null;

        // Determine subscribed language IDs
        $subscribedLanguageIds = $this->getSubscribedLanguageIds($userId, $request);

        $typeFilter = $request->query('type', 'all');
        // Build video query with filters
        $query = Post::select('id', 'title', 'slug', 'video_thumb', 'comment', 'view_count', 'publish_date', 'pubdate', 'channel_id')
            ->with(['channel' => fn($q) => $q->select('id', 'name', 'slug', 'logo')])
            ->where('posts.status', 'active');

        if ($typeFilter !== 'all') {
            $query->where('type', $typeFilter);
        } else {
            $query->whereIn('type', ['video', 'youtube']);
        }
        // Apply news language filter
        if ($subscribedLanguageIds->isNotEmpty()) {
            $query->whereIn('news_language_id', $subscribedLanguageIds);
        }

        $sortBy = $request->query('sort', 'newest'); // default to newest

        switch ($sortBy) {
            case 'oldest':
                $query->oldest('publish_date');
                break;
            case 'newest':
            default:
                $query->latest('publish_date');
                break;
        }

        // Paginate videos
        $videos = $query->paginate(9);

        // Humanize date fields
        $videos->getCollection()->transform(function ($post) {
            $post->publish_date_news = Carbon::parse($post->pubdate)->format(self::TIME_FORMATE);
            if ($post->publish_date) {
                $post->publish_date = Carbon::parse($post->publish_date)->diffForHumans();
            } elseif ($post->pubdate) {
                $post->pubdate = Carbon::parse($post->pubdate)->diffForHumans();
            }
            return $post;
        });

        $data = [
            'videos'       => $videos,
            'theme'        => $theme,
            'title'        => $title,
            'current_sort' => $sortBy, // Pass current sort to view
            'current_type' => $typeFilter,

        ];
        return view("front_end.$theme.pages.videos", $data);
    }
}
