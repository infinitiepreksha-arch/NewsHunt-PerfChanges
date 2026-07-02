<?php
namespace App\Http\Controllers\Apis;

use App\Http\Controllers\Controller;
use App\Models\NewsLanguage;
use App\Models\NewsLanguageStatus;
use App\Models\Setting;
use App\Services\ResponseService;
use Illuminate\Http\Request;

class GetSettingController extends Controller
{
    public function getSystemSettings(Request $request)
    {
        try {
            $newsLanguageStatus    = NewsLanguageStatus::getCurrentStatus();
            $defaultNewsLanguageId = NewsLanguage::where('status', 'active')->where('is_active', 1)->first();
            $type                  = [
                'facebook_link', 'instagram_link', 'x_link', 'maintenance_mode',
                'default_language', 'android_version', 'ios_version', 'about_us',
                'privacy_policy', 'terms_conditions', 'maintenance_mode',
                'free_trial_post_limit', 'free_trial_story_limit', 'free_trial_e_papers_and_magazines_limit', 'open_ad_key', 'interstitial_ad_key', 'banner_ad_key', 'admob_app_id',
                'free_trial_status', 'app_theme_primary_colour',
                // ✅ Enable Ads Status
                'enable_custom_ads_status', 'app_font',

                // 🌐 Web Placement Positions
                'header_placement_status', 'header_price',
                'footer_placement_status', 'footer_price',
                'left_sidebar_placement_status', 'left_sidebar_price',
                'right_sidebar_placement_status', 'right_sidebar_price',
                'banner_slider_placement_status', 'banner_slider_price',
                'post_detail_page_placement_status', 'post_detail_page_price',
                'latest_placement_status', 'latest_price',
                'popular_placement_status', 'popular_price',
                'posts_placement_status', 'posts_price',
                'topic_posts_placement_status', 'topic_posts_price',
                'videos_placement_status', 'videos_price',

                // 📱 App Placement Positions
                'category_news_page_placement_status', 'category_news_page_price',
                'topics_page_placement_status', 'topics_page_price',
                'after_weather_section_placement_status', 'after_weather_section_price',
                'above_recommendations_section_placement_status', 'above_recommendations_section_price',
                'splash_screen_page_placement_status', 'splash_screen_page_price',
                'all_channels_placement_status', 'all_channels_price',
                'channels_page_floating_placement_status', 'channels_page_floating_price',
                'discover_page_floating_placement_status', 'discover_page_floating_price',
                'video_page_floating_placement_status', 'video_page_floating_price',
                'after_read_more_placement_status', 'after_read_more_price',
                'app_banner_slider_placement_status', 'app_banner_slider_price',

                // Payment & Currency
                'currency_code',
                'currency_symbol',
                'payment_deadline_hours',
                'payment_deadline_minutes',
                'admob_app_id', 'banner_ad_key', 'interstitial_ad_key', 'open_ad_key', 'weather_api_key',
                'android_admob_app_id',
                'android_banner_ad_key',
                'android_interstitial_ad_key',
                'android_open_ad_key',

                'ios_admob_app_id',
                'ios_banner_ad_key',
                'ios_interstitial_ad_key',
                'ios_open_ad_key',
            ];

            $settings = Setting::select(['name', 'value'])
                ->whereIn('name', $type)
                ->get()
                ->map(function ($item) {
                    $item->value = $item->value ?? "";
                    return $item;
                })
                ->keyBy('name');

            $enableCustomAds = $settings['enable_custom_ads_status']->value ?? "0";
            $webAdsMap       = [
                'header'        => ['header_placement_status', 'header_price', 'Header Placement'],
                'footer'        => ['footer_placement_status', 'footer_price', 'Footer Placement'],
                'left_sidebar'  => ['left_sidebar_placement_status', 'left_sidebar_price', 'Left Sidebar Placement'],
                'right_sidebar' => ['right_sidebar_placement_status', 'right_sidebar_price', 'Right Sidebar Placement'],
                'banner_slider' => ['banner_slider_placement_status', 'banner_slider_price', 'Banner Slider Placement'],
                'post_detail'   => ['post_detail_page_placement_status', 'post_detail_page_price', 'Post Detail Page Placement'],
                'latest'        => ['latest_placement_status', 'latest_price', 'Latest Section Placement'],
                'popular'       => ['popular_placement_status', 'popular_price', 'Popular Section Placement'],
                'posts'         => ['posts_placement_status', 'posts_price', 'Posts Section Placement'],
                'topic_posts'   => ['topic_posts_placement_status', 'topic_posts_price', 'Topic Posts Placement'],
                'videos'        => ['videos_placement_status', 'videos_price', 'Videos Section Placement'],
            ];

            $appAdsMap = [
                'app_category_news_page' => ['category_news_page_placement_status', 'category_news_page_price', 'Splash Screen Placement'],
                'topics_page'            => ['topics_page_placement_status', 'topics_page_price', 'Topics Page Placement'],
                'after_weather_card'     => ['after_weather_section_placement_status', 'after_weather_section_price', 'After Weather Section Placement'],
                'above_recommendations'  => ['above_recommendations_section_placement_status', 'above_recommendations_section_price', 'Above Recommendations Section Placement'],
                'splash_screen '         => ['splash_screen_page_placement_status', 'splash_screen_page_price', 'Search Page Floating Ad Placement'],
                'all_channels'           => ['all_channels_placement_status', 'all_channels_price', 'All Channels Page Placement'],
                'channels_floating'      => ['channels_page_floating_placement_status', 'channels_page_floating_price', 'Channels Page – Floating Ad'],
                'discover_floating'      => ['discover_page_floating_placement_status', 'discover_page_floating_price', 'Discover Page – Floating Ad'],
                'video_floating'         => ['video_page_floating_placement_status', 'video_page_floating_price', 'Video Page – Floating Ad'],
                'after_read_more'        => ['after_read_more_placement_status', 'after_read_more_price', 'After Read More Button in News Post'],
                'app_banner_slider'      => ['app_banner_slider_placement_status', 'app_banner_slider_price', 'App Banner Slider Placement'],
            ];

            // Format web ads
            $webAds = [];
            foreach ($webAdsMap as $position => [$statusKey, $priceKey, $displayName]) {
                if (isset($settings[$statusKey]) && isset($settings[$priceKey])) {
                    $webAds[] = [
                        'position'     => $position,
                        'display_name' => $displayName,
                        'status'       => (string) $settings[$statusKey]->value,
                        'price'        => (string) $settings[$priceKey]->value,
                    ];
                }
            }

            // Format app ads
            $appAds = [];
            foreach ($appAdsMap as $position => [$statusKey, $priceKey, $displayName]) {
                if (isset($settings[$statusKey]) && isset($settings[$priceKey])) {
                    $appAds[] = [
                        'position'     => $position,
                        'display_name' => $displayName,
                        'status'       => (string) $settings[$statusKey]->value,
                        'price'        => (string) $settings[$priceKey]->value,
                    ];
                }
            }

            $responseData = [];

            // Enable Custom Ads Status
            $responseData[] = [
                'name'  => 'enable_custom_ads_status',
                'value' => $enableCustomAds,
            ];

            // Web Ads
            $responseData[] = [
                'name'  => 'web_ads',
                'value' => ($enableCustomAds === "1") ? $webAds : [],
            ];

            // App Ads
            $responseData[] = [
                'name'  => 'app_ads',
                'value' => ($enableCustomAds === "1") ? $appAds : [],
            ];

            // Add news language status
            $responseData[] = [
                'name'  => 'news_language_status',
                'value' => $newsLanguageStatus === 'active',
            ];

            $responseData[] = [
                'name'  => 'default_news_language_id',
                'value' => $defaultNewsLanguageId->id,
            ];
            $responseData[] = [
                'name'  => 'default_news_language_code',
                'value' => $defaultNewsLanguageId->code,
            ];

            // Add remaining general settings
            foreach ($settings as $key => $item) {
                if (! in_array($item->name, array_merge(
                    ['enable_custom_ads_status'],
                    array_merge(...array_values($webAdsMap)),
                    array_merge(...array_values($appAdsMap))
                ))) {
                    $responseData[] = [
                        'name'  => $item->name,
                        'value' => $item->value,
                    ];
                }
            }

            // Format payment deadlines as [hh-MM]
            $paymentDeadlineMinutes = isset($settings['payment_deadline_minutes']) ? (int) $settings['payment_deadline_minutes']->value : 0;
            $paymentDeadlineHours   = isset($settings['payment_deadline_hours']) ? (int) $settings['payment_deadline_hours']->value : 0;
            $totalMinutes           = ($paymentDeadlineHours * 60) + $paymentDeadlineMinutes;
            $combinedHours          = floor($totalMinutes / 60);
            $combinedMinutes        = $totalMinutes % 60;
            $combinedTime           = sprintf('%02d:%02d', $combinedHours, $combinedMinutes);
            $responseData[]         = [
                'name'  => 'payment_deadline_combined',
                'value' => $combinedTime,
            ];
            return response()->json([
                'error'   => false,
                'message' => 'Get Setting successfully.',
                'data'    => $responseData,
            ]);

        } catch (\Exception $e) {
            ResponseService::logErrorResponse($e, 'Failed to fetch system settings: ' . $e->getMessage());
            return response()->json([
                'error' => 'Unable to fetch posts at this time. Please try again later.',
            ], 500);
        }
    }
}
