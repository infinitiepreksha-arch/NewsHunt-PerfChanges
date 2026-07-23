<?php
namespace App\Http\Controllers;

use App\Models\Channel;
use App\Models\Favorite;
use App\Models\Language;
use App\Models\NewsLanguage;
use App\Models\NewsLanguageSubscriber;
use App\Models\Plan;
use App\Models\Post;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class FrontUserController extends Controller
{
    const PATH = 'front_end/';

    public function __construct()
    {
        $this->middleware('auth'); // Ensures user is authenticated
    }

    /**
     * Display user profile.
     */
    public function index()
    {
        if (! auth()->check()) {
            return redirect()->route('home');
        }
        $title = __('frontend-labels.myaccount.title');
        $theme = getTheme();
        return view(self::PATH . $theme . '/pages/my-account/user-profile', compact('title', 'theme'));
    }

    /**
     * Display followed channels.
     */
    public function followingsChannels()
    {
        if (! auth()->check()) {
            return redirect()->route('home');
        }
        $channelData = auth()->user()->subscriptions()
            ->select('channels.id', 'channels.name', 'channels.slug', 'channels.logo', 'channels.follow_count')
            ->paginate(8);
        $title       = __('frontend-labels.followings.title');
        $theme       = getTheme();
        return view(self::PATH . $theme . '/pages/my-account/following', compact('title', 'channelData', 'theme'));
    }

    /**
     * Display favorite posts.
     */
    public function favoritePosts(Request $request)
    {
        if (! auth()->check()) {
            return redirect()->route('home');
        }
        $userId = auth()->user()->id;
        if ($userId) {
            $subscribedLanguageIds = \Illuminate\Support\Facades\Cache::remember("user_subscribed_languages_{$userId}", 3600, function () use ($userId) {
                return NewsLanguageSubscriber::where('user_id', $userId)->pluck('news_language_id');
            });
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

        $query = Favorite::select(
            'posts.id', 'posts.title', 'posts.slug', 'posts.image', 'posts.type',
            'posts.video_thumb', 'posts.status', 'posts.publish_date', 'channels.name as channel_name',
            'channels.logo as channel_logo', 'channels.slug as channel_slug',
            'topics.name as topic_name', 'topics.slug as topic_slug', 'posts.pubdate', 'favorites.is_pinned'
        )
            ->leftJoin('posts', 'favorites.post_id', '=', 'posts.id')
            ->leftJoin('channels', 'posts.channel_id', '=', 'channels.id')
            ->leftJoin('topics', 'posts.topic_id', '=', 'topics.id')
            ->where('posts.status', 'active')
            ->orderByDesc('favorites.is_pinned')
            ->orderByDesc('favorites.id')
            ->where('favorites.user_id', $userId)
            ->when($subscribedLanguageIds->isNotEmpty(), function ($query) use ($subscribedLanguageIds) {
                $query->whereIn('posts.news_language_id', $subscribedLanguageIds);
            }, function ($query) {
                $selectedLanguageId = session('selected_news_language'); // For non-logged-in users
                if ($selectedLanguageId) {
                    $query->where('posts.news_language_id', $selectedLanguageId);
                }
            });

        // Add type filter
        $type = $request->get('type', 'all');
        if ($type !== 'all') {
            $query->where('posts.type', $type);
        }

        $favoritedPosts = $query->paginate(8)->withQueryString();

        $favoritedPosts->getCollection()->transform(function ($item) {
            if ($item->publish_date) {
                $item->publish_date = Carbon::parse($item->publish_date)->diffForHumans();
            } elseif ($item->pubdate) {
                $item->pubdate = Carbon::parse($item->pubdate)->diffForHumans();
            }
            $item->channel_logo = url('storage/images/' . $item->channel_logo);
            return $item;
        });

        $title = __('frontend-labels.favorite.title');
        $theme = getTheme();
        return view(self::PATH . $theme . '/pages/my-account/favorite', compact('title', 'favoritedPosts', 'theme', 'type'));
    }

    public function togglePin(Request $request)
    {
        $postId = $request->post_id;
        $userId = auth()->id();

        if (! $userId) {
            return response()->json(['success' => false, 'message' => 'User not authenticated.']);
        }

        $favorite = Favorite::where('user_id', $userId)
            ->where('post_id', $postId)
            ->first();

        if (! $favorite) {
            return response()->json(['success' => false, 'message' => 'Post not found in favorites.']);
        }

        // Toggle pin status
        $favorite->is_pinned = ! $favorite->is_pinned;
        $favorite->save();

        return response()->json([
            'success'   => true,
            'is_pinned' => (bool)$favorite->is_pinned,
            'message' => $favorite->is_pinned 
                ? __('frontend-labels.favorites.post_pinned_success') 
                : __('frontend-labels.favorites.post_unpinned_success'),
        ]);
    }

    /**
     * Display news languages.
     */

    public function toggleNewsLanguage(Request $request)
    {
        $languageId = $request->input('news_language_id');

        $checklanguageCode = NewsLanguage::find($languageId);
        $languageCode      = Language::where('code', $checklanguageCode->code)->first();

        if (! empty($languageCode)) {
            Session::put('web_locale', $languageCode->code);
            Session::put('web_language', (object) $languageCode->toArray());
            Session::save();
            app()->setLocale($languageCode->code);
        }

        // Validate language ID
        if ($languageId && ! NewsLanguage::where('id', $languageId)->exists()) {
            return response()->json(['status' => 'error', 'message' => 'Invalid language ID.'], 400);
        }

        if (Auth::check()) {
            $userId = Auth::id();

            // Clear previous selections
            NewsLanguageSubscriber::where('user_id', $userId)->delete();

            // If no language selected, fall back to default (is_active)
            if (! $languageId) {
                $defaultLang = NewsLanguage::where('is_active', 1)->first();
                $languageId  = $defaultLang ? $defaultLang->id : NewsLanguage::value('id');
            }

            if ($languageId) {
                NewsLanguageSubscriber::create([
                    'user_id'          => $userId,
                    'news_language_id' => $languageId,
                ]);
            }

            return response()->json(['status' => 'success', 'message' => __('frontend-labels.settings.language_updated_success')]);
        } else {
            // For non-logged-in users, use session to store selection
            if ($languageId) {
                session(['selected_news_language' => $languageId]);
            } else {
                // Fall back to default if no selection
                $defaultLang = NewsLanguage::where('is_active', 1)->first();
                $languageId  = $defaultLang ? $defaultLang->id : NewsLanguage::value('id');
                session(['selected_news_language' => $languageId]);
            }

            return response()->json(['status' => 'success', 'message' => __('frontend-labels.settings.language_updated_success')]);
        }
    }
    public function subscriptionDetails()
    {
        if (! auth()->check()) {
            return redirect()->route('home');
        }

        $user         = Auth::user();
        $subscription = $user->subscription()->with([
            'feature:id,number_of_articles,number_of_stories,number_of_e_papers_and_magazines',
            'plan:id,name'
        ])->first();

        $paymentSetting = \Illuminate\Support\Facades\Cache::rememberForever('active_payment_setting', function () {
            return \App\Models\PaymentSetting::where('status', true)->first();
        });
        $currency = $paymentSetting->currency_symbol ?? '$';

        $membership_data = collect();

        $title = __('frontend-labels.mysubscription.title');
        $theme = getTheme();

        // Prepare the data for the view
        $data = [
            'title'           => $title,
            'subscription'    => $subscription,
            'user'            => $user,
            'theme'           => $theme,
            'membership_data' => $membership_data,
            'currency'        => $currency,
        ];

        // Return the view with data
        return view(self::PATH . $theme . '/pages/my-account/subscription', $data);
    }

    /**
     * Display transaction details.
     */

    public function transactionDetails()
    {
        if (! auth()->check()) {
            return redirect()->route('home');
        }

        $user  = Auth::user();
        $title = __('frontend-labels.transaction_details.title');
        $theme = getTheme();

        // Fetch transactions for the logged-in user
        $transactions = Transaction::select('id', 'plan_details', 'transaction_id', 'amount', 'created_at', 'status', 'user_id')
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        return view(self::PATH . $theme . '/pages/my-account/transaction', compact('title', 'transactions', 'theme'));
    }
}
