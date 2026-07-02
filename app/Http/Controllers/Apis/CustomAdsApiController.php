<?php
namespace App\Http\Controllers\Apis;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\SmartAd;
use App\Models\SmartAdPlacement;
use App\Models\SmartAdsDetail;
use App\Models\SmartAdTracking;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class CustomAdsApiController extends Controller
{
    /**
     * Handle API requests for custom ads (GET to retrieve, POST to create)
     */

    public function handleAds(Request $request)
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Unauthorized: User not authenticated.',
            ], 401);
        }
        $hour   = Setting::where('name', 'payment_deadline_hours')->first();
        $minute = Setting::where('name', 'payment_deadline_minutes')->first();

        $deadlineHours   = (int) ($hour->value ?? 0);
        $deadlineMinutes = (int) ($minute->value ?? 0);

        $totalDeadlineMinutes = ($deadlineHours * 60) + $deadlineMinutes;

        if ($totalDeadlineMinutes > 0) {
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
                try {
                    Mail::to($ad->contact_email)->queue(new SmartAdStatusMail($ad, 'expired'));
                } catch (\Exception $e) {
                    Log::error('Failed to send expiry mail', [
                        'ad_id'   => $ad->id,
                        'message' => $e->getMessage(),
                    ]);
                }

                // Delete detail
                $ad->delete();

                // If no more details exist for parent ad, delete parent SmartAd
                $remainingDetails = SmartAdsDetail::where('smart_ad_id', $ad->smart_ad_id)->count();
                if ($remainingDetails === 0) {
                    SmartAd::where('id', $ad->smart_ad_id)->delete();
                }
            }
        }

        if ($request->isMethod('post')) {
            try {
                // Validation
                $request->validate([
                    'name'             => 'required|string|max:255',
                    'contact_name'     => 'required|string|max:255',
                    'contact_email'    => 'required|email|max:255',
                    'start_date'       => 'required|date|after_or_equal:today',
                    'end_date'         => 'required|date|after_or_equal:start_date',
                    'total_price'      => 'required|numeric|min:0',
                    'daily_price'      => 'required|numeric|min:0',
                    'total_days'       => 'required|integer|min:1',
                    'vertical_image'   => 'required|image|mimes:jpeg,jpg,png,gif,webp|max:10240',
                    'horizontal_image' => 'required|image|mimes:jpeg,jpg,png,gif,webp|max:10240',
                ]);

                $appPlacements = $request->input('app_ads_placement', []);
                $webPlacements = $request->input('web_ads_placement', []);

                if (empty($appPlacements) && empty($webPlacements)) {
                    return response()->json([
                        'status'  => 'error',
                        'message' => 'Please select at least one placement option.',
                    ], 422);
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

                // DB Transaction
                DB::beginTransaction();

                try {
                    $customAdsSettings = $this->customAdsSettings();
                    $priceSummary      = $this->buildPriceSummary($appPlacements, $webPlacements, $customAdsSettings);

                    // Create SmartAd
                    $smartAd = SmartAd::create([
                        'name'             => $request->input('name'),
                        'slug'             => Str::slug($request->input('name')) . '-' . time(),
                        'body'             => $request->input('description'),
                        'adType'           => $request->input('ad_type', 'image'),
                        'vertical_image'   => $verticalImagePath,
                        'horizontal_image' => $horizontalImagePath,
                        'imageUrl'         => $request->input('url'),
                        'imageAlt'         => $request->input('image_alt') ?? 'Sponsored Ads',
                        'views'            => 0,
                        'clicks'           => 0,
                        'enabled'          => false,
                        'placements'       => array_merge($appPlacements, $webPlacements),
                    ]);

                    // Create SmartAdsDetail
                    $smartAdsDetail = SmartAdsDetail::create([
                        'user_id'           => $user->id,
                        'smart_ad_id'       => $smartAd->id,
                        'contact_name'      => $request->input('contact_name'),
                        'contact_email'     => $request->input('contact_email'),
                        'contact_phone'     => $request->input('phone'),
                        'total_price'       => (float) $request->input('total_price'),
                        'daily_price'       => (float) $request->input('daily_price'),
                        'total_days'        => (int) $request->input('total_days'),
                        'price_summary'     => $priceSummary,
                        'web_ads_placement' => ! empty($webPlacements) ? $webPlacements : null,
                        'app_ads_placement' => ! empty($appPlacements) ? $appPlacements : null,
                        'ad_publish_status' => 'pending',
                        'payment_status'    => 'pending',
                        'start_date'        => $request->input('start_date'),
                        'end_date'          => $request->input('end_date'),
                    ]);

                    // Create Tracking (using smart_ads_tracking table)
                    SmartAdTracking::create([
                        'smart_ad_id' => $smartAd->id,
                        'ad_clicks'   => [],
                        'totalClicks' => 0,
                    ]);

                    $allPlacements = array_merge($appPlacements, $webPlacements);

                    foreach ($allPlacements as $placementKey) {
                        SmartAdPlacement::create([
                            'smart_ad_id'   => $smartAd->id,
                            'user_id'       => $user->id,
                            'placement_key' => $placementKey,
                            'start_date'    => $request->input('start_date'),
                            'end_date'      => $request->input('end_date'),
                        ]);
                    }

                    DB::commit();

                    return response()->json([
                        'status'  => 'success',
                        'message' => 'Advertisement request submitted successfully! Ad ID: #' . $smartAd->id,
                        'data'    => [
                            'id'                => $smartAd->id,
                            'user_id'           => $smartAdsDetail->user_id,
                            'name'              => $smartAd->name,
                            'slug'              => $smartAd->slug,
                            'body'              => $smartAd->body,
                            'ad_type'           => $smartAd->adType,
                            'image'             => $smartAd->vertical_image,
                            'horizontal_image'  => $smartAd->horizontal_image,
                            'image_url'         => $smartAd->imageUrl,
                            'image_alt'         => $smartAd->imageAlt ?? 'Sponsored Ads',
                            'contact_name'      => $smartAdsDetail->contact_name,
                            'contact_email'     => $smartAdsDetail->contact_email,
                            'contact_phone'     => $smartAdsDetail->contact_phone,
                            'total_price'       => $smartAdsDetail->total_price,
                            'daily_price'       => $smartAdsDetail->daily_price,
                            'total_days'        => $smartAdsDetail->total_days,
                            'price_summary'     => $smartAdsDetail->price_summary,
                            'web_ads_placement' => $smartAdsDetail->web_ads_placement,
                            'app_ads_placement' => $smartAdsDetail->app_ads_placement,
                            'ad_publish_status' => $smartAdsDetail->ad_publish_status,
                            'payment_status'    => $smartAdsDetail->payment_status,
                            'start_date'        => $smartAdsDetail->start_date,
                            'end_date'          => $smartAdsDetail->end_date,
                            'created_at'        => $smartAd->created_at,
                            'updated_at'        => $smartAd->updated_at,
                        ],
                    ], 201);

                } catch (\Throwable $e) {
                    DB::rollBack();

                    if ($verticalImagePath && Storage::disk('public')->exists($verticalImagePath)) {
                        Storage::disk('public')->delete($verticalImagePath);
                    }
                    if ($horizontalImagePath && Storage::disk('public')->exists($horizontalImagePath)) {
                        Storage::disk('public')->delete($horizontalImagePath);
                    }

                    Log::error('Error submitting smart ad', [
                        'message' => $e->getMessage(),
                        'file'    => $e->getFile(),
                        'line'    => $e->getLine(),
                        'trace'   => $e->getTraceAsString(),
                    ]);

                    return response()->json([
                        'status'  => 'error',
                        'message' => 'An error occurred: ' . $e->getMessage(),
                    ], 500);
                }

            } catch (\Illuminate\Validation\ValidationException $e) {
                Log::error('Validation Error in handleAds', ['errors' => $e->errors()]);
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Validation failed',
                    'errors'  => $e->errors(),
                ], 422);
            }
        }

        // GET method - fetch user's ads
        $ads = SmartAdsDetail::where('user_id', $user->id)
            ->with('smartAd')
            ->latest()
            ->get()
            ->map(function ($detail) {
                return [
                    'id'                => $detail->smart_ad_id,
                    'ad_details_id'     => $detail->id,
                    'user_id'           => $detail->user_id,
                    'name'              => $detail->smartAd->name,
                    'slug'              => $detail->smartAd->slug,
                    'body'              => $detail->smartAd->body,
                    'ad_type'           => $detail->smartAd->adType,
                    'image'             => url('public/storage/' . $detail->smartAd->vertical_image),
                    'horizontal_image'  => url('public/storage/' . $detail->smartAd->horizontal_image),
                    'image_url'         => $detail->smartAd->imageUrl,
                    'image_alt'         => $detail->smartAd->imageAlt ?? 'Sponsored Ads',
                    'contact_name'      => $detail->contact_name,
                    'contact_email'     => $detail->contact_email,
                    'contact_phone'     => $detail->contact_phone,
                    'total_price'       => $detail->total_price,
                    'daily_price'       => $detail->daily_price,
                    'total_days'        => $detail->total_days,
                    'price_summary'     => $detail->price_summary,
                    'web_ads_placement' => $detail->web_ads_placement,
                    'app_ads_placement' => $detail->app_ads_placement,
                    'ad_publish_status' => $detail->ad_publish_status,
                    'payment_status'    => $detail->payment_status,
                    'start_date'        => $detail->start_date,
                    'end_date'          => $detail->end_date,
                    'created_at'        => $detail->created_at,
                    'updated_at'        => $detail->updated_at,
                ];
            });

        return response()->json([
            'status' => 'success',
            'data'   => $ads,
        ]);
    }

    public function trackClick(Request $request)
    {
        $request->validate([
            'smart_ad_id' => 'required|integer|exists:smart_ads,id',
        ]);

        $adId = $request->input('smart_ad_id');
        $ad   = SmartAd::find($adId);

        // Increment clicks in smart_ads table
        $ad->increment('clicks');

        // Update smart_ads_tracking table
        $tracking = SmartAdTracking::firstOrCreate(
            ['smart_ad_id' => $ad->id],
            ['ad_clicks' => [], 'totalClicks' => 0]
        );

        // Ensure it's always an array
        $clickLog   = is_array($tracking->ad_clicks) ? $tracking->ad_clicks : [];
        $clickLog[] = [
            'ip'        => $request->ip(),
            'timestamp' => now()->toDateTimeString(),
        ];

        $tracking->update([
            'ad_clicks'   => $clickLog,
            'totalClicks' => $tracking->totalClicks + 1,
        ]);

        return response()->json([
            'error'   => false,
            'message' => 'Click tracked successfully',
            'data'    => [

                'ad_id'        => $ad->id,
                'clicks'       => $ad->clicks,
                'total_clicks' => $tracking->totalClicks,
            ],
        ], 200);
    }

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
}
