<?php
namespace App\Http\Controllers;

use App\Mail\SmartAdStatusMail;
use App\Models\Setting;
use App\Models\SmartAd;
use App\Models\SmartAdPlacement;
use App\Models\SmartAdsDetail;
use App\Models\SmartAdsPayment;
use App\Models\SmartAdTracking;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Throwable;

class SmartAdManagerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); // Ensures user is authenticated
    }

    public function dashboard()
    {
        if (! Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Please login to access your dashboard.');
        }
        $title = __('frontend-labels.sponsor_ads.dashboard');
        $data  = [
            'title' => $title,
        ];
        return view('front_end.smart-ad-manager.dashboard', $data);
    }

    public function smartIndex()
    {
        $theme = getTheme();
        $user  = Auth::user();

        $hasCreatedAd = SmartAdsDetail::where('user_id', Auth::id())
            ->whereDate('end_date', '>=', Carbon::today())
            ->exists();

        $smartAds = SmartAd::select('smart_ads.*')
            ->join('smart_ads_details', 'smart_ads_details.smart_ad_id', '=', 'smart_ads.id')
            ->where('smart_ads_details.user_id', $user->id ?? 0)
            ->orderBy('smart_ads_details.id', 'DESC')
            ->orderBy('smart_ads.enabled', 'DESC')
            ->orderBy('smart_ads.name')
            ->paginate(10);

        $smartAdsDetails = SmartAdsDetail::where('user_id', $user->id ?? "")->orderBy('id', 'DESC')->get();

        if ($smartAds->count() > 0) {
            $createdAtFormatted = $smartAds->first()->created_at->format('l, F j, Y');
        } else {
            $createdAtFormatted = 'Date not available';
        }

        $totalClicks = SmartAd::sum('clicks');

        // ------------------ Deadline Logic -------------------
        $hour   = Setting::where('name', 'payment_deadline_hours')->first();
        $minute = Setting::where('name', 'payment_deadline_minutes')->first();

        // If admin leaves blank, treat as 0
        $deadlineHours   = (int) ($hour->value ?? 0);
        $deadlineMinutes = (int) ($minute->value ?? 0);

        // Total deadline in minutes (convert hours + minutes into minutes)
        $totalDeadlineMinutes = ($deadlineHours * 60) + $deadlineMinutes;

        // 1. Expire pending ads
        $expiredAds = SmartAdsDetail::where('ad_publish_status', 'approved')
            ->where('payment_status', 'pending')
            ->where('updated_at', '<', now()->subMinutes($totalDeadlineMinutes))
            ->get();

        foreach ($expiredAds as $ad) {
            // Update status
            $ad->update([
                'ad_publish_status' => 'rejected',
                'payment_status'    => 'failed',
            ]);

            // Send Expired Notification Email
            Mail::to($ad->contact_email)->queue(new SmartAdStatusMail($ad, 'expired'));

            // ✅ Remove user ad data after notification
            $ad->delete();

            // If you also want to remove related parent SmartAd if no other details exist
            $smartAd = SmartAd::find($ad->smart_ad_id);
            $smartAd->delete();
        }

        // 2. Calculate remaining time for each pending ad
        foreach ($smartAdsDetails as $detail) {
            if (
                $detail->ad_publish_status === 'approved' &&
                $detail->payment_status === 'pending' &&
                $detail->total_price > 0
            ) {
                $deadline = $detail->updated_at->copy()->addMinutes($totalDeadlineMinutes);

                $detail->remaining_time = now()->diffInSeconds($deadline, false); // negative = expired
            } else {
                $detail->remaining_time = null;
            }
        }

        $title = __('frontend-labels.sponsor_ads.title');

        $data = [
            'theme'              => $theme,
            'smartAds'           => $smartAds,
            'totalClicks'        => $totalClicks,
            'hasCreatedAd'       => $hasCreatedAd,
            'createdAtFormatted' => $createdAtFormatted,
            'title'              => $title,
            'smartAdsDetails'    => $smartAdsDetails,
        ];
        return view('front_end.classic.pages.smart_ads_request', $data);
    }

    public function index()
    {
        if (! Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Please login to access your dashboard.');
        }
        $user     = Auth::user();
        $smartAds = SmartAd::select('smart_ads.*')
            ->join('smart_ads_details', 'smart_ads_details.smart_ad_id', '=', 'smart_ads.id')
            ->where('smart_ads_details.user_id', $user->id)
            ->orderBy('smart_ads.enabled', 'DESC')
            ->orderBy('smart_ads.name')
            ->paginate(10);

        $totalClicks = SmartAd::sum('clicks');
        $title       = __('frontend-labels.sponsor_ads.sponsor_ads_details');
        $data        = [
            'smartAds'    => $smartAds,
            'totalClicks' => $totalClicks,
            'title'       => $title,
        ];
        return view('front_end.smart-ad-manager.index', $data);
    }

    public function transactionHistory()
    {
        if (! Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Please login to access your dashboard.');
        }
        $user = Auth::user();

        $ads_transactions = SmartAdsPayment::with([
            'smartAd.smartAdsDetail',
        ])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $title = __('frontend-labels.sponsor_ads.transaction_details');
        $data  = [
            'ads_transactions' => $ads_transactions,
            'title'            => $title,
        ];

        return view('front_end.smart-ad-manager.transaction_history', $data);
    }

    public function show(SmartAd $smartAd)
    {
        if (! Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Please login to access your dashboard.');
        }

        $data = [
            'smartAd' => $smartAd,
        ];
        return view('front_end.smart-ad-manager.show', $data);
    }

    public function create()
    {
        if (! Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Please login to access your dashboard.');
        }
        $title = __('frontend-labels.sponsor_ads.create_news_ad');
        $user  = Auth::user();

        $hasCreatedAd = SmartAdsDetail::where('user_id', Auth::id())
            ->whereDate('end_date', '>=', Carbon::today())
            ->exists();

        $user              = Auth::user();
        $customAdsSettings = Setting::pluck('value', 'name');

        $orderByColumn  = 'created_at'; // or 'id'
        $orderDirection = 'DESC';

        $smartAdsDetail = SmartAdsDetail::where('user_id', Auth::id())
            ->orderBy($orderByColumn, $orderDirection)
            ->first();

        $smartAds = SmartAd::select('smart_ads.*')
            ->join('smart_ads_details', 'smart_ads_details.smart_ad_id', '=', 'smart_ads.id')
            ->where('smart_ads_details.user_id', $user->id ?? 0)
            ->orderBy('smart_ads_details.created_at', 'DESC')
            ->orderBy('smart_ads.enabled', 'DESC')
            ->orderBy('smart_ads.name')
            ->paginate(10);

        if ($smartAds->count() > 0) {
            $createdAtFormatted = $smartAds->first()->created_at->format('l, F j, Y');
        } else {
            $createdAtFormatted = 'Date not available';
        }

        $data = [
            'customAdsSettings'  => $customAdsSettings,
            'smartAds'           => $smartAds,
            'createdAtFormatted' => $createdAtFormatted,
            'hasCreatedAd'       => $hasCreatedAd,
            'smartAdsDetail'     => $smartAdsDetail,
            'title'              => $title,
        ];
        return view('front_end.smart-ad-manager.create', $data);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name'             => 'required|string|max:255',
                'contact_name'     => 'required|string|max:255',
                'contact_email'    => 'required|email|max:255',
                'start_date'       => 'required|date',
                'end_date'         => 'required|date|after_or_equal:start_date',
                'total_price'      => 'required|numeric|min:0',
                'daily_price'      => 'required|numeric|min:0',
                'total_days'       => 'required|integer|min:1',
                'horizontal_image' => 'required|image|mimes:jpeg,jpg,png,gif,webp|max:10240', // 10MB max
                'vertical_image'   => 'required|image|mimes:jpeg,jpg,png,gif,webp|max:10240', // 10MB max
            ]);

            $appPlacements = $request->input('app_ads_placement', []);
            $webPlacements = $request->input('web_ads_placement', []);

            if (empty($appPlacements) && empty($webPlacements)) {
                return back()->withErrors(['placement' => 'Please select at least one placement option.'])->withInput();
            }

            // Handle image upload
            $verticalImagePath   = null;
            $horizontalImagePath = null;

            try {
                if ($request->hasFile('vertical_image')) {
                    $vertical     = $request->file('vertical_image');
                    $verticalName = time() . '_v_' . Str::random(8) . '.' . $vertical->getClientOriginalExtension();
                    if (! Storage::disk('public')->exists('ads/images')) {
                        Storage::disk('public')->makeDirectory('ads/images');
                    }
                    $verticalImagePath = $vertical->storeAs('ads/images', $verticalName, 'public');
                }

                if ($request->hasFile('horizontal_image')) {
                    $horizontal     = $request->file('horizontal_image');
                    $horizontalName = time() . '_h_' . Str::random(8) . '.' . $horizontal->getClientOriginalExtension();
                    if (! Storage::disk('public')->exists('ads/images')) {
                        Storage::disk('public')->makeDirectory('ads/images');
                    }
                    $horizontalImagePath = $horizontal->storeAs('ads/images', $horizontalName, 'public');
                }
            } catch (\Exception $e) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Failed to upload images. Please try again.',
                ], 422);
            }

            DB::beginTransaction();

            try {
                $customAdsSettings = $this->customAdsSettings();
                $priceSummary      = $this->buildPriceSummary($appPlacements, $webPlacements, $customAdsSettings);
                $slug              = Str::slug($request->name);
                if (empty($slug)) {
                    $slug = 'advertisement-' . uniqid();
                }
                $smartAd = SmartAd::create([
                    'name'             => $request->name,
                    'slug'             => $slug,
                    'body'             => $request->body,
                    'adType'           => $request->adType ?? 'image',
                    'vertical_image'   => $verticalImagePath,
                    'horizontal_image' => $horizontalImagePath,
                    'imageUrl'         => $request->imageUrl,
                    'imageAlt'         => $request->imageAlt,
                    'views'            => 0,
                    'clicks'           => 0,
                    'enabled'          => false,
                    'placements'       => array_merge($appPlacements, $webPlacements),
                ]);

                $smartAdsDetailData = [
                    'user_id'           => Auth::user()->id,
                    'smart_ad_id'       => $smartAd->id,
                    'contact_name'      => $request->contact_name,
                    'contact_email'     => $request->contact_email,
                    'contact_phone'     => $request->mobile_number,
                    'total_price'       => (float) $request->total_price,
                    'daily_price'       => (float) $request->daily_price,
                    'total_days'        => (int) $request->total_days,
                    'price_summary'     => $priceSummary,
                    'web_ads_placement' => ! empty($webPlacements) ? $webPlacements : null,
                    'app_ads_placement' => ! empty($appPlacements) ? $appPlacements : null,
                    'ad_publish_status' => 'pending',
                    'payment_status'    => 'pending',
                    'start_date'        => $request->start_date,
                    'end_date'          => $request->end_date,
                ];

                $smartAdsDetail = SmartAdsDetail::create($smartAdsDetailData);

                SmartAdTracking::create([
                    'smart_ad_id' => $smartAd->id,
                    'ad_clicks'   => [],
                    'totalClicks' => 0,
                ]);

                // After creating $smartAdsDetail
                if (! empty($appPlacements) || ! empty($webPlacements)) {
                    $allPlacements = array_merge($appPlacements, $webPlacements);

                    foreach ($allPlacements as $placementKey) {
                        SmartAdPlacement::create([
                            'smart_ad_id'   => $smartAd->id,
                            'user_id'       => Auth::id(),
                            'placement_key' => $placementKey,
                            'start_date'    => $request->start_date,
                            'end_date'      => $request->end_date,
                        ]);
                    }
                }

                DB::commit();
                return redirect()->route('smart-ads.index')->with('success',
                    'Your advertisement has been submitted successfully! Ad ID: #' . $smartAd->id
                );

            } catch (\Throwable $e) {
                DB::rollback();

                if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                    Storage::disk('public')->delete($imagePath);
                }
                throw $e;
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation Error in Smart Ads', ['errors' => $e->errors()]);
            return back()->withErrors($e->errors())->withInput();

        } catch (\Throwable $e) {
            Log::error('Error submitting smart ad', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return back()->withErrors(['error' => 'An error occurred: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Build price summary array from selected placements
     */
    protected function buildPriceSummary($appPlacements, $webPlacements, $customAdsSettings)
    {
        $priceSummary = [];

        // Process app placements
        if (! empty($appPlacements)) {
            foreach ($appPlacements as $placementGroup) {
                $placements = is_string($placementGroup)
                    ? array_map('trim', explode(',', $placementGroup))
                    : [$placementGroup];

                foreach ($placements as $placement) {
                    $priceKey = $this->getAppPlacementPriceKey($placement);
                    if ($priceKey) {
                        $price          = isset($customAdsSettings[$priceKey]) ? (float) $customAdsSettings[$priceKey] : 0;
                        $priceSummary[] = [
                            'placement'    => $placement,
                            'type'         => 'app',
                            'display_name' => $this->getPlacementDisplayName($placement),
                            'daily_price'  => $price,
                        ];
                    }
                }
            }
        }

        // Process web placements
        if (! empty($webPlacements)) {
            foreach ($webPlacements as $placementGroup) {
                $placements = is_string($placementGroup)
                    ? array_map('trim', explode(',', $placementGroup))
                    : [$placementGroup];

                foreach ($placements as $placement) {
                    $priceKey = $this->getWebPlacementPriceKey($placement);
                    if ($priceKey) {
                        $price          = isset($customAdsSettings[$priceKey]) ? (float) $customAdsSettings[$priceKey] : 0;
                        $priceSummary[] = [
                            'placement'    => $placement,
                            'type'         => 'web',
                            'display_name' => $this->getPlacementDisplayName($placement),
                            'daily_price'  => $price,
                        ];
                    }
                }
            }
        }

        return $priceSummary;
    }

    /**
     * Get price key for app placements
     */
    protected function getAppPlacementPriceKey($placement)
    {
        $mapping = [
            'app_category_news_page' => 'category_news_page_price',
            'topics_page'            => 'topics_page_price',
            'after_weather_card'     => 'after_weather_section_price',
            'above_recommendations'  => 'above_recommendations_section_price',
            'all_channels'           => 'all_channels_price',
            'app_banner_slider'      => 'app_banner_slider_price',
            'splash_screen '         => 'splash_screen_page_price',
            'channels_floating'      => 'channels_page_floating_price',
            'discover_floating'      => 'discover_page_floating_price',
            'video_floating'         => 'video_page_floating_price',
            'after_read_more'        => 'after_read_more_price',
        ];

        return $mapping[$placement] ?? null;
    }

    protected function getAppPlacementStatusKey($placement)
    {
        $mapping = [
            'app_category_news_page' => 'category_news_page_placement_status',
            'topics_page'            => 'topics_page_placement_status',
            'after_weather_card'     => 'after_weather_section_placement_status',
            'above_recommendations'  => 'above_recommendations_section_placement_status',
            'all_channels'           => 'all_channels_placement_status',
            'app_banner_slider'      => 'app_banner_slider_placement_status',
            'splash_screen '         => 'splash_screen_page_placement_status',
            'channels_floating'      => 'channels_page_floating_placement_status',
            'discover_floating'      => 'discover_page_floating_placement_status',
            'video_floating'         => 'video_page_floating_placement_status',
            'after_read_more'        => 'after_read_more_placement_status',
        ];

        return $mapping[$placement] ?? null;
    }

    protected function getWebPlacementPriceKey($placement)
    {
        $mapping = [
            'header'        => 'header_price',
            'left_sidebar'  => 'left_sidebar_price',
            'footer'        => 'footer_price',
            'right_sidebar' => 'right_sidebar_price',
            'banner_slider' => 'banner_slider_price',
            'post_detail'   => 'post_detail_page_price',
            'latest'        => 'latest_price',
            'popular'       => 'popular_price',
            'posts'         => 'posts_price',
            'topic_posts'   => 'topic_posts_price',
            'videos'        => 'videos_price',
        ];

        return $mapping[$placement] ?? null;
    }

    protected function getWebPlacementStatusKey($placement)
    {
        $mapping = [
            'header'        => 'header_placement_status',
            'left_sidebar'  => 'left_sidebar_placement_status',
            'footer'        => 'footer_placement_status',
            'right_sidebar' => 'right_sidebar_placement_status',
            'banner_slider' => 'banner_slider_placement_status',
            'post_detail'   => 'post_detail_page_placement_status',
            'latest'        => 'latest_placement_status',
            'popular'       => 'popular_placement_status',
            'posts'         => 'posts_placement_status',
            'topic_posts'   => 'topic_posts_placement_status',
            'videos'        => 'videos_placement_status',
        ];

        return $mapping[$placement] ?? null;
    }

    protected function getPlacementDisplayName($placement)
    {
        $displayNames = [
            'app_category_news_page' => 'Splash Screen Placement',
            'topics_page'            => 'Topics Page Placement',
            'after_weather_card'     => 'After Weather Section Placement',
            'above_recommendations'  => 'Above Recommendations Section Placement',
            'splash_screen '         => 'Search Page Floating Ad Placement',
            'all_channels'           => 'All Channels Page Placement',
            'channels_floating'      => 'Channels Page – Floating Ad',
            'discover_floating'      => 'Discover Page – Floating Ad',
            'video_floating'         => 'Video Page – Floating Ad',
            'after_read_more'        => 'After Read More Button in News Post',
            'app_banner_slider'      => 'App Banner Slider Placement',

            'header'                 => 'Header Placement',
            'footer'                 => 'Footer Placement',
            'left_sidebar'           => 'Left Sidebar Placement',
            'right_sidebar'          => 'Right Sidebar Placement',
            'banner_slider'          => 'Banner Slider Placement',
            'post_detail'            => 'Post Detail Page Placement',
            'latest'                 => 'Latest Section Placement',
            'popular'                => 'Popular Section Placement',
            'posts'                  => 'Posts Section Placement',
            'topic_posts'            => 'Topic Posts Placement',
            'videos'                 => 'Videos Section Placement',
        ];

        return $displayNames[$placement] ?? ucwords(str_replace('_', ' ', $placement));
    }

    protected function customAdsSettings()
    {
        try {
            $customAdsSettings = Setting::whereIn('name', [
                'enable_custom_ads_status',
                'category_news_page_price',
                'topics_page_price',
                'after_weather_section_price',
                'above_recommendations_section_price',
                'all_channels_price',
                'splash_screen_page_price',
                'channels_page_floating_price',
                'discover_page_floating_price',
                'video_page_floating_price',
                'after_read_more_price',
                'app_banner_slider_price',

                'header_price',
                'left_sidebar_price',
                'footer_price',
                'right_sidebar_price',
                'banner_slider_price',
                'post_detail_page_price',
                'latest_price',
                'popular_price',
                'posts_price',
                'topic_posts_price',
                'videos_price',
            ])->pluck('value', 'name');

            return [
                'enable_custom_ads_status'            => $customAdsSettings['enable_custom_ads_status'] ?? '0',
                'category_news_page_price'            => $customAdsSettings['category_news_page_price'] ?? '25',
                'topics_page_price'                   => $customAdsSettings['topics_page_price'] ?? '25',
                'after_weather_section_price'         => $customAdsSettings['after_weather_section_price'] ?? '25',
                'above_recommendations_section_price' => $customAdsSettings['above_recommendations_section_price'] ?? '50',
                'all_channels_price'                  => $customAdsSettings['all_channels_price'] ?? '25',
                'splash_screen_page_price'            => $customAdsSettings['splash_screen_page_price'] ?? '50',
                'channels_page_floating_price'        => $customAdsSettings['channels_page_floating_price'] ?? '50',
                'discover_page_floating_price'        => $customAdsSettings['discover_page_floating_price'] ?? '60',
                'video_page_floating_price'           => $customAdsSettings['video_page_floating_price'] ?? '60',
                'after_read_more_price'               => $customAdsSettings['after_read_more_price'] ?? '25',
                'app_banner_slider_price'             => $customAdsSettings['app_banner_slider_price'] ?? '0',
                'header_price'                        => $customAdsSettings['header_price'] ?? '25',
                'left_sidebar_price'                  => $customAdsSettings['left_sidebar_price'] ?? '30',
                'footer_price'                        => $customAdsSettings['footer_price'] ?? '25',
                'right_sidebar_price'                 => $customAdsSettings['right_sidebar_price'] ?? '25',
                'banner_slider_price'                 => $customAdsSettings['banner_slider_price'] ?? '35',
                'post_detail_page_price'              => $customAdsSettings['post_detail_page_price'] ?? '40',
                'latest_price'                        => $customAdsSettings['latest_price'] ?? '45',
                'popular_price'                       => $customAdsSettings['popular_price'] ?? '50',
                'posts_price'                         => $customAdsSettings['posts_price'] ?? '20',
                'topic_posts_price'                   => $customAdsSettings['topic_posts_price'] ?? '20',
                'videos_price'                        => $customAdsSettings['videos_price'] ?? '25',
            ];
        } catch (Throwable $e) {
            Log::error('Error getting custom ads settings: ' . $e->getMessage());
            return [
                'enable_custom_ads_status'            => '0',
                'category_news_page_price'            => '25',
                'topics_page_price'                   => '25',
                'after_weather_section_price'         => '25',
                'above_recommendations_section_price' => '50',
                'all_channels_price'                  => '25',
                'splash_screen_page_price'            => '50',
                'channels_page_floating_price'        => '50',
                'discover_page_floating_price'        => '60',
                'video_page_floating_price'           => '60',
                'after_read_more_price'               => '25',
                'app_banner_slider_price'             => '0',
                'app_latest_price'                    => '0',
                'app_popular_price'                   => '0',
                'app_posts_price'                     => '0',
                'app_topic_posts_price'               => '0',
                'app_banner_price'                    => '0',
                'header_price'                        => '25',
                'left_sidebar_price'                  => '30',
                'footer_price'                        => '25',
                'right_sidebar_price'                 => '25',
                'banner_slider_price'                 => '35',
                'post_detail_page_price'              => '40',
                'latest_price'                        => '45',
                'popular_price'                       => '50',
                'posts_price'                         => '20',
                'topic_posts_price'                   => '20',
                'videos_price'                        => '25',
            ];
        }
    }
    protected function slug($data)
    {
        $ex = explode(' ', $data);
        return implode('-', $ex);
    }

    public function autoAds()
    {
        $ads = SmartAd::whereNotNull('placements')->get();
        return $ads;
    }

    /**
     * Adds click count to the add
     */
    public function updateClicks(Request $request)
    {
        $slug = $request->get('slug');
        LaravelSmartAdsFacade::updateClicks($slug);
    }

    public function editPassword()
    {
        $title = __('frontend-labels.sponsor_ads.change_password');
        if (! Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Please login to access your dashboard.');
        }

        $data =
            [
            'title' => $title,
        ];
        return view('front_end.smart-ad-manager.edit-password', $data);
    }

    public function updatePassword(Request $request)
    {

        if (! Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Please login to access your dashboard.');
        }
        $validator = Validator::make($request->all(), [
            'old_password'     => 'required|string',
            'new_password'     => 'required|string|min:8',
            'confirm_password' => 'required|string|same:new_password',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();

        // Check if current password is correct
        if (! Hash::check($request->old_password, $user->password)) {
            return response()->json([
                'errors' => [
                    'old_password' => ['Current password is incorrect.'],
                ],
            ], 422);
        }

        try {
            // Update password
            $user->password = Hash::make($request->new_password);
            $user->save();

            // Logout user after password change
            Auth::logout();

            // Invalidate the session
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return response()->json([
                'success'  => 'Password updated successfully. Please login with your new password.',
                'redirect' => route('home'), // or route('admin.login') - whatever your login route is
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'errors' => [
                    'general' => ['Something went wrong. Please try again later.'],
                ],
            ], 500);
        }
    }

    public function editProfile()
    {
        $title = __('frontend-labels.sponsor_ads.update_profile');

        $data =
            [
            'title' => $title,
        ];
        return view('front_end.smart-ad-manager.edit-profile', $data);
    }

    public function updateProfile(Request $request)
    {
        if (! Auth::check()) {
            return response()->json([
                'errors' => [
                    'general' => ['Please login to access your dashboard.'],
                ],
            ], 401);
        }

        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'name'    => 'required|string|max:255',
            'profile' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if ($validator->fails()) {return response()->json(['errors' => $validator->errors()], 422);}
        // Prevent email change
        if ($request->email !== $user->email) {
            return response()->json([
                'errors' => [
                    'email' => ['Email cannot be changed.'],
                ],
            ], 422);
        }
        try {
            $user->name = $request->name;

            // Profile image
            if ($request->hasFile('profile')) {
                if ($user->profile && Storage::exists('public/' . $user->profile)) {
                    Storage::delete('public/' . $user->profile);
                }

                $path          = $request->file('profile')->store('profile_images', 'public');
                $user->profile = $path;
            }

            $user->save();

            return response()->json([
                'success'  => 'Profile updated successfully.',
                'redirect' => url('/smart-ads'),
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'errors' => [
                    'general' => ['Something went wrong. Please try again later.'],
                ],
            ], 500);
        }
    }

}
