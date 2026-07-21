<?php
namespace App\Http\Controllers;

use App\Models\NewsLanguage;
use App\Models\NewsLanguageSubscriber;
use App\Models\Setting;
use App\Models\Story;
use App\Models\Topic;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class WebStory extends Controller
{
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
        } else {
            $sessionLanguageId = session('selected_news_language');
            if ($sessionLanguageId) {
                $subscribedLanguageIds = collect([$sessionLanguageId]);
            } else {
                $defaultActiveLanguage = \App\Providers\AppServiceProvider::$activeLanguageCache ?? NewsLanguage::where('is_active', 1)->first();
                $subscribedLanguageIds = $defaultActiveLanguage ? collect([$defaultActiveLanguage->id]) : collect();
            }
        }
        $request->attributes->set('subscribed_language_ids', $subscribedLanguageIds);
        return $subscribedLanguageIds;
    }

    public function index(Request $request)
    {
        $title           = __('frontend-labels.web_stories.title');
        $theme           = getTheme();
        $selectedTopicId = $request->query('topic');
        $userId          = Auth::user()->id ?? 0;

        $settingsCache = $this->getSettingsCache($request);
        $subscribedLanguageIds = $this->getSubscribedLanguageIds($userId, $request);

        $stories = Story::select('id', 'title', 'slug', 'topic_id', 'news_language_id', 'story_count', 'image_size_type', 'created_at')
            ->with([
                'story_slides',
                'topic' => function ($query) {
                    $query->select('id', 'name', 'slug');
                }
            ])
            ->whereHas('story_slides')
            ->when($selectedTopicId, function (Builder $query) use ($selectedTopicId) {
                return $query->where('topic_id', $selectedTopicId);
            })
            ->when($subscribedLanguageIds->isNotEmpty(), function (Builder $query) use ($subscribedLanguageIds) {
                return $query->whereIn('news_language_id', $subscribedLanguageIds);
            })
            ->get();

        $filteredTopics = $stories->pluck('topic')->filter()->unique('id')->values();

        $dailyLimitReached        = false;
        $subscriptionLimitReached = false;

        $user = auth()->user();

        $freeTrialLimit = (int) ($settingsCache->get('free_trial_story_limit')->value ?? 10);
        $isDailyLimitEligible = false;

        // Listing page: do NOT increment story count here.
        // Only check subscription status so the JS knows about limits.
        if ($user && $user->subscription) {
            $subscription = $user->subscription;
            if ($subscription->hasReachedStoryLimits()) {
                $subscriptionLimitReached = true;
                $isDailyLimitEligible = true;
            }
            // No increment on listing page
        } else {
            $isDailyLimitEligible = true;
        }

        return view("front_end/{$theme}/pages/webstory", compact(
            'title',
            'filteredTopics',
            'theme',
            'stories',
            'selectedTopicId',
            'freeTrialLimit',
            'isDailyLimitEligible',
            'dailyLimitReached',
            'subscriptionLimitReached'
        ));
    }

    public function show(Topic $topic, Story $story)
    {
        $request = request();
        $settingsCache = $this->getSettingsCache($request);
        $socialsettings = $settingsCache->map(fn($item) => $item->value);

        if ($story->topic_id !== $topic->id) {
            abort(404);
        }
        $userId = auth()->user()->id ?? 0;
        $subscribedLanguageIds = $this->getSubscribedLanguageIds($userId, $request);

        if ($subscribedLanguageIds->isNotEmpty() && ! $subscribedLanguageIds->contains($story->news_language_id)) {
            abort(404); // or redirect to webstories.index with message
        }

        $user                     = auth()->user();
        $dailyLimitReached        = false;
        $subscriptionLimitReached = false;

        $freeTrialLimit = (int) ($settingsCache->get('free_trial_story_limit')->value ?? 10);
        $isDailyLimitEligible = false;

        if ($user && $user->subscription) {
            $subscription = $user->subscription;
            if ($subscription->hasReachedStoryLimits()) {
                $isDailyLimitEligible = true;
                $subscriptionLimitReached = true;
            } else {
                try {
                    $subscription->incrementStoryCountWithValidation(1);
                } catch (\Throwable $e) {
                    report($e);
                    $isDailyLimitEligible = true;
                    $subscriptionLimitReached = true;
                }
            }
        } else {
            $isDailyLimitEligible = true;
        }

        $this->storyCount($story);

        $nextStoryQuery = Story::select('id', 'title', 'slug', 'topic_id', 'news_language_id', 'image_size_type')
            ->with([
                'story_slides',
                'topic' => function ($query) {
                    $query->select('id', 'name', 'slug');
                }
            ])
            ->where('topic_id', $topic->id)
            ->where('id', '>', $story->id)
            ->whereHas('story_slides');

        if ($subscribedLanguageIds->isNotEmpty()) {
            $nextStoryQuery->whereIn('news_language_id', $subscribedLanguageIds);
        }

        $nextStory = $nextStoryQuery->first();

        if (! $nextStory) {
            $fallbackQuery = Story::select('id', 'title', 'slug', 'topic_id', 'news_language_id', 'image_size_type')
                ->with([
                    'story_slides',
                    'topic' => function ($query) {
                        $query->select('id', 'name', 'slug');
                    }
                ])
                ->whereHas('story_slides')
                ->where('id', '!=', $story->id);
            if ($subscribedLanguageIds->isNotEmpty()) {
                $fallbackQuery->whereIn('news_language_id', $subscribedLanguageIds);
            }
            $nextStory = $fallbackQuery->first();
        }

        $animations = [];
        foreach ($story->story_slides as $slide) {
            $animations[$slide->id] = $slide->animation_details;
        }

        $theme = getTheme();

        return view("front_end/{$theme}/pages/webstory_slide", compact(
            'story',
            'theme',
            'nextStory',
            'animations',
            'socialsettings',
            'freeTrialLimit',
            'isDailyLimitEligible',
            'dailyLimitReached',
            'subscriptionLimitReached'
        ));
    }

    public function storyByTopic(Topic $topic)
    {
        $title  = __('frontend-labels.web_stories.title');
        $theme  = getTheme();
        $request = request();
        $settingsCache = $this->getSettingsCache($request);
        $userId = Auth::user()->id ?? 0;
        $subscribedLanguageIds = $this->getSubscribedLanguageIds($userId, $request);

        $stories = Story::select('id', 'title', 'slug', 'topic_id', 'news_language_id', 'story_count', 'image_size_type', 'created_at')
            ->with([
                'story_slides',
                'topic' => function ($query) {
                    $query->select('id', 'name', 'slug');
                }
            ])
            ->where('topic_id', $topic->id)
            ->whereHas('story_slides')
            ->when($subscribedLanguageIds->isNotEmpty(), function (Builder $query) use ($subscribedLanguageIds) {
                return $query->whereIn('news_language_id', $subscribedLanguageIds);
            })
            ->latest()
            ->paginate(12);

        $totalStories = $stories->total();

        // Check limits — listing page, do NOT increment story count here.
        $dailyLimitReached        = false;
        $subscriptionLimitReached = false;

        $user = auth()->user();
        $freeTrialLimit = (int) ($settingsCache->get('free_trial_story_limit')->value ?? 10);
        $isDailyLimitEligible = false;

        // Only check subscription status so the JS knows about limits.
        if ($user && $user->subscription) {
            $subscription = $user->subscription;
            if ($subscription->hasReachedStoryLimits()) {
                $subscriptionLimitReached = true;
                $isDailyLimitEligible     = true;
            }
            // No increment on listing page
        } else {
            $isDailyLimitEligible = true;
        }

        return view("front_end/{$theme}/pages/webstory_by_topic", compact(
            'stories',
            'topic',
            'theme',
            'totalStories',
            'freeTrialLimit',
            'isDailyLimitEligible',
            'dailyLimitReached',
            'subscriptionLimitReached'
        ));
    }

    protected function storyCount($story)
    {
        $user_id    = Auth::user()->id ?? null;
        $cookieName = 'viewed_story_' . $story->id;

        // Check if the story has been viewed by checking the cookie
        if (! Cookie::has($cookieName)) {
            // Set a cookie to mark this story as viewed (expires in 15 days = 21600 minutes)
            Cookie::queue($cookieName, true, 21600);
            // Increment the story_count column
            $story->increment('story_count');
        }

        return $story;
    }
}
