<?php
namespace App\Http\Controllers;

use App\Models\NewsLanguage;
use App\Models\NewsLanguageSubscriber;
use App\Models\Topic;
use Illuminate\Support\Facades\Auth;

class TopicFrontController extends Controller
{
    public function index()
    {
        $perPage = 16;

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
        $front_topics = Topic::where('status', 'active')
            ->whereHas('posts', function ($query) use ($subscribedLanguageIds) {
                if ($subscribedLanguageIds->isNotEmpty()) {
                    $query->whereIn('news_language_id', $subscribedLanguageIds);
                }
            })
            ->orderBy('categorie_order', 'asc')
            ->paginate($perPage);

        $title = __('frontend-labels.topics.title');
        $theme = getTheme();
        $data  = compact('title', 'theme', 'front_topics');
        return view('front_end/' . $theme . '/pages/topics', $data);
    }
}
