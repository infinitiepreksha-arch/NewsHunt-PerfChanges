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
    public function allAudios(Request $request)
    {
        $theme  = getTheme();
        $title  = __('frontend-labels.newsaudios.title');
        $userId = auth()->check() ? auth()->id() : null;

        // Determine subscribed language IDs
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

        $topicIds = Post::where('type', 'audio')
            ->whereNotNull('topic_id')
            ->when($subscribedLanguageIds->isNotEmpty(), function ($query) use ($subscribedLanguageIds) {
                $query->whereIn('news_language_id', $subscribedLanguageIds);
            })
            ->pluck('topic_id')
            ->unique()
            ->filter()
            ->toArray();

        $typeFilter = $request->query('type', 'all');
        // Build video query with filters
        $query = Post::with(['topic', 'channel'])
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

        $topics_for_filter = Topic::whereIn('id', $topicIds)->get();
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
