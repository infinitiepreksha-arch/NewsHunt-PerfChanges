<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ENewspaper;
use App\Models\NewsLanguage;
use App\Models\NewsLanguageSubscriber;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ENewspaperFrontController extends Controller
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

    public function getENewspaper(Request $request)
    {
        $theme  = getTheme();
        $title  = __('frontend-labels.enewspapers.title');
        $userId = auth()->user()->id ?? "0";

        $settingsCache = $this->getSettingsCache($request);
        $subscribedLanguageIds = $this->getSubscribedLanguageIds($userId, $request);

        // Check limits
        $dailyLimitReached        = false;
        $subscriptionLimitReached = false;

        $user         = auth()->user();
        $subscription = $user ? $user->subscription : null;

        $freeTrialLimit = (int) ($settingsCache->get('free_trial_e_papers_and_magazines_limit')->value ?? 5);
        $isDailyLimitEligible = false;

        if ($subscription) {
            if ($subscription->hasReachedEPaperLimits()) {
                $subscriptionLimitReached = true;
                $isDailyLimitEligible = true;
            }
        } else {
            $isDailyLimitEligible = true;
        }

        // Build query with filters
        $query = ENewspaper::with(['channel', 'newsLanguage', 'topic'])
            ->whereIn('news_language_id', $subscribedLanguageIds)
            ->where('type', 'paper');

        // Apply filters
        if ($request->filled('topic')) {
            $query->whereHas('topic', function ($q) use ($request) {
                $q->where('slug', $request->topic);
            });
        }

        if ($request->filled('channel')) {
            $query->whereHas('channel', function ($q) use ($request) {
                $q->where('slug', $request->channel);
            });
        }

        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }

        $e_newspapers = $query->orderBy('date', 'desc')->paginate(12);

        // Get unique channels and topics targeted by subquery
        $epaperChannels = \App\Models\Channel::select('id', 'name', 'slug', 'logo')
            ->whereHas('eNewspapers', function ($q) use ($subscribedLanguageIds) {
                $q->whereIn('news_language_id', $subscribedLanguageIds)->where('type', 'paper');
            })->orderBy('name', 'asc')->get();

        $epapertopics = \App\Models\Topic::select('id', 'name', 'slug')
            ->whereHas('eNewspapers', function ($q) use ($subscribedLanguageIds) {
                $q->whereIn('news_language_id', $subscribedLanguageIds)->where('type', 'paper');
            })->orderBy('name', 'asc')->get();

        // Handle AJAX requests
        if ($request->ajax() || $request->wantsJson()) {
            $newspapers = $e_newspapers->map(function ($newspaper) {
                return [
                    'id'            => $newspaper->id,
                    'title'         => $newspaper->title ?? '',
                    'date'          => $newspaper->date,
                    'thumbnail_url' => asset('storage/' . $newspaper->thumbnail),
                    'pdf_url'       => route('e-newspaper.pdf', $newspaper->id),
                    'topic_name'    => $newspaper->topic->name ?? '',
                    'topic_url'     => url('topics/' . ($newspaper->topic->slug ?? '')),
                    'channel_name'  => $newspaper->channel->name ?? '',
                    'channel_url'   => url('channels/' . ($newspaper->channel->slug ?? '')),
                    'channel_logo'  => url('storage/images/' . ($newspaper->channel->logo ?? '')),
                ];
            });

            return response()->json([
                'success'                  => true,
                'newspapers'               => $newspapers,
                'total'                    => $e_newspapers->total(),
                'current_page'             => $e_newspapers->currentPage(),
                'last_page'                => $e_newspapers->lastPage(),
                'dailyLimitReached'        => $dailyLimitReached,
                'subscriptionLimitReached' => $subscriptionLimitReached,
                'freeTrialLimit'           => $freeTrialLimit,
                'isDailyLimitEligible'     => $isDailyLimitEligible,
            ]);
        }

        // Regular page load
        $socialsettings = $settingsCache->map(fn($item) => $item->value);
        $epapersetting  = [
            'enewspaper'      => $settingsCache->get('enews_paper_image')->value ?? asset('public/front_end/classic/images/default/newspaper-advertising-service-500x500-1.png'),
            'enewspapertitle' => $settingsCache->get('enews_paper_title')->value ?? 'Newshunt',
        ];

        $data = [
            'title'                    => $title,
            'e_newspapers'             => $e_newspapers,
            'theme'                    => $theme,
            'dailyLimitReached'        => $dailyLimitReached,
            'subscriptionLimitReached' => $subscriptionLimitReached,
            'socialsettings'           => $socialsettings,
            'epapersetting'            => $epapersetting,
            'epaperChannels'           => $epaperChannels,
            'epapertopics'             => $epapertopics,
            'freeTrialLimit'           => $freeTrialLimit,
            'isDailyLimitEligible'     => $isDailyLimitEligible,
        ];

        return view('front_end.' . $theme . '.pages.e-news-paper', $data);
    }

    private function getENewsletterSettings(?Request $request = null)
    {
        $settingsCache = $this->getSettingsCache($request);

        return [
            'enewspaper'      => $settingsCache->get('enews_paper_image')->value ?? asset('public/front_end/classic/images/default/newspaper-advertising-service-500x500-1.png'),
            'enewspapertitle' => $settingsCache->get('enews_paper_title')->value ?? 'Newshunt',
        ];
    }

    public function getMagazine(Request $request)
    {
        $theme  = getTheme();
        $title  = __('frontend-labels.magazines.title');
        $userId = auth()->user()->id ?? "0";

        $settingsCache = $this->getSettingsCache($request);
        $subscribedLanguageIds = $this->getSubscribedLanguageIds($userId, $request);

        // Check limits
        $dailyLimitReached        = false;
        $subscriptionLimitReached = false;

        $user         = auth()->user();
        $subscription = $user ? $user->subscription : null;

        $freeTrialLimit = (int) ($settingsCache->get('free_trial_e_papers_and_magazines_limit')->value ?? 5);
        $isDailyLimitEligible = false;

        if ($subscription) {
            if ($subscription->hasReachedEPaperLimits()) {
                $subscriptionLimitReached = true;
                $isDailyLimitEligible = true;
            }
        } else {
            $isDailyLimitEligible = true;
        }

        // Build query with filters
        $query = ENewspaper::with(['channel', 'newsLanguage', 'topic'])
            ->whereIn('news_language_id', $subscribedLanguageIds)
            ->where('type', 'magazine');

        // Apply filters
        if ($request->filled('topic')) {
            $query->whereHas('topic', function ($q) use ($request) {
                $q->where('slug', $request->topic);
            });
        }

        if ($request->filled('channel')) {
            $query->whereHas('channel', function ($q) use ($request) {
                $q->where('slug', $request->channel);
            });
        }

        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }

        $e_newspapers = $query->orderBy('date', 'desc')->paginate(12);

        $epaperChannels = \App\Models\Channel::select('id', 'name', 'slug', 'logo')
            ->whereHas('eNewspapers', function ($q) use ($subscribedLanguageIds) {
                $q->whereIn('news_language_id', $subscribedLanguageIds)->where('type', 'magazine');
            })->orderBy('name', 'asc')->get();

        $epapertopics = \App\Models\Topic::select('id', 'name', 'slug')
            ->whereHas('eNewspapers', function ($q) use ($subscribedLanguageIds) {
                $q->whereIn('news_language_id', $subscribedLanguageIds)->where('type', 'magazine');
            })->orderBy('name', 'asc')->get();

        // Handle AJAX requests
        if ($request->ajax() || $request->wantsJson()) {
            $newspapers = $e_newspapers->map(function ($newspaper) {
                return [
                    'id'            => $newspaper->id,
                    'title'         => $newspaper->title ?? '',
                    'date'          => $newspaper->date,
                    'thumbnail_url' => asset('storage/' . $newspaper->thumbnail),
                    'pdf_url'       => route('e-newspaper.pdf', $newspaper->id),
                    'topic_name'    => $newspaper->topic->name ?? '',
                    'topic_url'     => url('topics/' . ($newspaper->topic->slug ?? '')),
                    'channel_name'  => $newspaper->channel->name ?? '',
                    'channel_url'   => url('channels/' . ($newspaper->channel->slug ?? '')),
                    'channel_logo'  => url('storage/images/' . ($newspaper->channel->logo ?? '')),
                ];
            });

            return response()->json([
                'success'                  => true,
                'newspapers'               => $newspapers,
                'total'                    => $e_newspapers->total(),
                'current_page'             => $e_newspapers->currentPage(),
                'last_page'                => $e_newspapers->lastPage(),
                'dailyLimitReached'        => $dailyLimitReached,
                'subscriptionLimitReached' => $subscriptionLimitReached,
                'freeTrialLimit'           => $freeTrialLimit,
                'isDailyLimitEligible'     => $isDailyLimitEligible,
            ]);
        }

        // Regular page load
        $socialsettings = $settingsCache->map(fn($item) => $item->value);
        $epapersetting  = [
            'enewspaper'      => $settingsCache->get('enews_paper_image')->value ?? asset('public/front_end/classic/images/default/newspaper-advertising-service-500x500-1.png'),
            'enewspapertitle' => $settingsCache->get('enews_paper_title')->value ?? 'Newshunt',
        ];

        $data = [
            'title'                    => $title,
            'e_magazines'              => $e_newspapers,
            'theme'                    => $theme,
            'dailyLimitReached'        => $dailyLimitReached,
            'subscriptionLimitReached' => $subscriptionLimitReached,
            'socialsettings'           => $socialsettings,
            'epapersetting'            => $epapersetting,
            'epaperChannels'           => $epaperChannels,
            'epapertopics'             => $epapertopics,
            'freeTrialLimit'           => $freeTrialLimit,
            'isDailyLimitEligible'     => $isDailyLimitEligible,
        ];

        return view('front_end.' . $theme . '.pages.e-news-magazine', $data);
    }

    public function accessPdf(Request $request, $id)
    {
        $userId       = auth()->check() ? auth()->id() : null;
        $user         = auth()->user();
        $subscription = $user ? $user->subscription : null;

        $settingsCache = $this->getSettingsCache($request);
                $eNewspaper = ENewspaper::findOrFail($id);

        $freeTrialLimit = (int) ($settingsCache->get('free_trial_e_papers_and_magazines_limit')->value ?? 5);
        $isDailyLimitEligible = false;
        $dailyLimitReached = false;
        $subscriptionLimitReached = false;

        if ($subscription) {
            if ($subscription->hasReachedEPaperLimits()) {
                $subscriptionLimitReached = true;
                $isDailyLimitEligible = true;
            } else {
                $subscription->incrementEPaperCountWithValidation(1);
            }
        } else {
            $isDailyLimitEligible = true;
        }

        return redirect(asset('storage/' . $eNewspaper->pdf_path));
    }

    public function showPdf($id)
    {
        $request = request();
        $settingsCache = $this->getSettingsCache($request);
        $userId       = auth()->check() ? auth()->id() : null;
        $user         = auth()->user();
        $subscription = $user ? $user->subscription : null;
        $subscribedLanguageIds = $this->getSubscribedLanguageIds($userId, $request);

        $e_newspaper = ENewspaper::with('channel')
            ->findOrFail($id);

        $freeTrialLimit = (int) ($settingsCache->get('free_trial_e_papers_and_magazines_limit')->value ?? 5);
        $isDailyLimitEligible = false;
        $dailyLimitReached = false;
        $subscriptionLimitReached = false;

        if ($subscription) {
            if ($subscription->hasReachedEPaperLimits()) {
                $subscriptionLimitReached = true;
                $isDailyLimitEligible = true;
            } else {
                $subscription->incrementEPaperCountWithValidation(1);
            }
        } else {
            $isDailyLimitEligible = true;
        }

        if ($dailyLimitReached) {
            $route = $e_newspaper->type === 'paper' ? 'e-newspaper.index' : 'e-magazine.index';
            return redirect()->route($route)
                ->with('daily_limit_reached', true)
                ->with('subscription_limit_reached', $subscriptionLimitReached);
        }

        $appName = $settingsCache->get('app_name')->value ?? 'News Portal';

        $title = $e_newspaper->channel->name ?? 'E-Paper';

        $flipbookAssets = [
            'whiteBookCss'      => asset('front_end/classic/css/epaper-css/white-book-view.css'),
            'shortWhiteBookCss' => asset('front_end/classic/css/epaper-css/short-white-book-view.css'),
            'shortBlackBookCss' => asset('front_end/classic/css/epaper-css/short-black-book-view.css'),
            'blackBookCss'      => asset('front_end/classic/css/epaper-css/black-book-view.css'),
            'fontAwesomeCss'    => 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css',
            'jquery'            => asset('front_end/classic/js/libs/jquery.min.js'),
            'three'             => asset('front_end/classic/js/custom/dist/three.min.js'),
            'pdf'               => asset('front_end/classic/js/custom/dist/pdf.min.js'),
            'pdfWorker'         => asset('front_end/classic/js/custom/dist/pdf.worker.js'),
            'html2canvas'       => asset('front_end/classic/js/custom/dist/html2canvas.min.js'),
            'flipBook'          => asset('front_end/classic/js/custom/dist/flip-book.js'),
            'defaultView'       => asset('front_end/classic/js/custom/dist/default-book-view.js'),
            'templateHtml'      => asset('front_end/classic/pages/templates/default-book-view.html'),
            'startSound'        => asset('front_end/sounds/start-flip.mp3'),
            'endSound'          => asset('front_end/sounds/end-flip.mp3'),
            'pdfCMapUrl'        => 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/cmaps/',
        ];
        $pdfUrl =
        isset($e_newspaper) && $e_newspaper->pdf_path
            ? asset('storage/' . ltrim($e_newspaper->pdf_path, '/'))
            : null;

        $theme = getTheme();
        return view('front_end.' . $theme . '.pages.pdf-viewer', compact('e_newspaper', 'theme', 'title', 'appName', 'flipbookAssets', 'pdfUrl', 'freeTrialLimit', 'isDailyLimitEligible', 'dailyLimitReached', 'subscriptionLimitReached'
        ));
    }
}
