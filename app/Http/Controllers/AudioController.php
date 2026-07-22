<?php
namespace App\Http\Controllers;

use App\Models\NewsLanguage;
use App\Models\NewsLanguageSubscriber;
use App\Models\Post;
use App\Models\Topic;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AudioController extends Controller
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

    public function allAudios(Request $request)
    {
        $settingsCache = $this->getSettingsCache($request);
        $theme  = getTheme();
        $title  = __('frontend-labels.newsaudios.title');
        $userId = auth()->check() ? auth()->id() : null;

        // Determine subscribed language IDs
        $subscribedLanguageIds = $this->getSubscribedLanguageIds($userId, $request);

        $topics_for_filter = Topic::select('id', 'name', 'slug')
            ->whereHas('posts', function ($q) use ($subscribedLanguageIds) {
                $q->where('status', 'active')
                  ->where('type', 'audio')
                  ->when($subscribedLanguageIds->isNotEmpty(), function ($query) use ($subscribedLanguageIds) {
                      $query->whereIn('news_language_id', $subscribedLanguageIds);
                  });
            })->get();

        $typeFilter = $request->query('type', 'all');
        // Build video query with filters
        $query = Post::select('id', 'title', 'slug', 'image', 'comment', 'view_count', 'publish_date', 'pubdate', 'channel_id', 'topic_id')
            ->with([
                'topic' => fn($q) => $q->select('id', 'name', 'slug'),
                'channel' => fn($q) => $q->select('id', 'name', 'slug', 'logo')
            ])
            ->where('posts.status', 'active');

        if ($typeFilter !== 'all') {
            $query->where('type', $typeFilter);
        } else {
            $query->where('type', 'audio');
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

        // Paginate audios
        $audios = $query->paginate(18);

        // Humanize date fields
        $audios->getCollection()->transform(function ($post) {
            $post->publish_date_news = Carbon::parse($post->pubdate)->format(self::TIME_FORMATE);
            if ($post->publish_date) {
                $post->publish_date = Carbon::parse($post->publish_date)->diffForHumans();
            } elseif ($post->pubdate) {
                $post->pubdate = Carbon::parse($post->pubdate)->diffForHumans();
            }
            return $post;
        });

        $data              = [
            'audios'            => $audios,
            'theme'             => $theme,
            'title'             => $title,
            'current_sort'      => $sortBy, // Pass current sort to view
            'current_type'      => $typeFilter,
            'topics_for_filter' => $topics_for_filter,

        ];
        return view("front_end.$theme.pages.audios", $data);
    }
}
