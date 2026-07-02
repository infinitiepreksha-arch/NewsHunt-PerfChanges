<?php
namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\NewsLanguage;
use App\Models\NewsLanguageStatus;
use App\Models\Setting;
use App\Models\SmartAdsDetail;
use App\Models\Subscription;
use App\Services\CachingService;
use App\Services\FileService;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Throwable;

// Add this import

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    private string $uploadFolder;
    protected $helperService;

    public function __construct()
    {
        $this->uploadFolder = 'settings';
    }

    public function index()
    {
        ResponseService::noAnyPermissionThenRedirect(['payment-gateway-settings', 'custom-advertising-settings', 'credit-packs-settings', 'about-us-settings', 'terms-conditions-settings', 'newslanguage-settings', 'basic-company-setup-settings', 'logo-management-and-web-settings', 'subscription-model-and-header/footer-script-settings', 'social-link-and-other-settings', 'smtp-mail-configuration-settings', 'privacy-policy-settings', 'language-translation-settings', 'error-logs-view-settings', 'system-update-settings', 'firebase-settings',
            'cronjob/info-in-settings', 'app-admob-and-weather-settings', 'system-health-settings', 'google-adsense-configuration']);

        $languages = Language::all();

        $data = [
            'languages' => $languages,
        ];
        return view('admin.settings.index', $data);
    }

    public function page(Request $request)
    {
        $type                          = last(request()->segments());
        $settings                      = CachingService::getSystemSettings()->toArray();
        $settings['free_trial_status'] = $settings['free_trial_status'] ?? 0;

        if (! empty($settings['place_api_key']) && config('app.demo_mode')) {
            $settings['place_api_key'] = "**************************";
        }
        $stripe_currencies = ["USD", "AED", "AFN", "ALL", "AMD", "ANG", "AOA", "ARS", "AUD", "AWG", "AZN", "BAM", "BBD", "BDT", "BGN", "BIF", "BMD", "BND", "BOB", "BRL", "BSD", "BWP", "BYN", "BZD", "CAD", "CDF", "CHF", "CLP", "CNY", "COP", "CRC", "CVE", "CZK", "DJF", "DKK", "DOP", "DZD", "EGP", "ETB", "EUR", "FJD", "FKP", "GBP", "GEL", "GIP", "GMD", "GNF", "GTQ", "GYD", "HKD", "HNL", "HTG", "HUF", "IDR", "ILS", "INR", "ISK", "JMD", "JPY", "KES", "KGS", "KHR", "KMF", "KRW", "KYD", "KZT", "LAK", "LBP", "LKR", "LRD", "LSL", "MAD", "MDL", "MGA", "MKD", "MMK", "MNT", "MOP", "MRO", "MUR", "MVR", "MWK", "MXN", "MYR", "MZN", "NAD", "NGN", "NIO", "NOK", "NPR", "NZD", "PAB", "PEN", "PGK", "PHP", "PKR", "PLN", "PYG", "QAR", "RON", "RSD", "RUB", "RWF", "SAR", "SBD", "SCR", "SEK", "SGD", "SHP", "SLE", "SOS", "SRD", "STD", "SZL", "THB", "TJS", "TOP", "TTD", "TWD", "TZS", "UAH", "UGX", "UYU", "UZS", "VND", "VUV", "WST", "XAF", "XCD", "XOF", "XPF", "YER", "ZAR", "ZMW"];
        $languages         = CachingService::getLanguages();

        $hasActiveSubscription = Subscription::currentActive()->exists();
        if ($hasActiveSubscription && $request->input('free_trial_status') == 1) {
            return redirect()->back()->with('error', 'Cannot enable free trial mode while active subscriptions exist.');
        }
        $data = getSystemHealth();
        // Fetch news languages from the news_languages table
        $newsLanguages      = NewsLanguage::all();
        $activeNewsLanguage = $newsLanguages->where('is_active', 1)->first();

        // Add default values for notification settings if they don't exist
        $settings['automatic_notifications']  = $settings['automatic_notifications'] ?? 1;
        $settings['daily_notification_limit'] = $settings['daily_notification_limit'] ?? 100;

        return view('admin.settings.' . $type, compact('settings', 'type', 'languages', 'stripe_currencies', 'activeNewsLanguage', 'newsLanguages', 'hasActiveSubscription', 'data'));
    }

    public function store(Request $request)
    {
        ResponseService::noPermissionThenSendJson('update-settings');
        try {
            $inputs = $request->input();
            unset($inputs['_token']);
            $data = [];

            // Handle string inputs
            foreach ($inputs as $key => $input) {
                $data[] = [
                    'name'  => $key,
                    'value' => $input,
                    'type'  => 'string',
                ];
            }

            // Handle file uploads
            $oldSettingFiles = Setting::whereIn('name', collect($request->files)->keys())->get();
            foreach ($request->files as $key => $file) {
                $data[] = [
                    'name'  => $key,
                    'value' => $request->file($key)->store($this->uploadFolder, 'public'),
                    'type'  => 'file',
                ];
                $oldFile = $oldSettingFiles->first(function ($old) use ($key) {
                    return $old->name == $key;
                });
                if (! empty($oldFile)) {
                    FileService::delete($oldFile->getRawOriginal('value'));
                }
            }

            // Upsert settings
            Setting::upsert($data, 'name', ['value']);
            CachingService::removeCache(config('constants.CACHE.SETTINGS'));
            return redirect()->back()->with('success', 'Settings Updated Successfully!!');
        } catch (Throwable $th) {
            ResponseService::logErrorResponse($th, "Setting Controller -> store");
            ResponseService::errorResponse('Something Went Wrong: ' . $th->getMessage());
        }
    }

    public function aboutUs(Request $request)
    {
        try {
            $inputs = $request->input();
            unset($inputs['_token']);
            $data = [];

            // Handle string inputs
            foreach ($inputs as $key => $input) {
                $data[] = [
                    'name'  => $key,
                    'value' => $input,
                    'type'  => 'string',
                ];
            }

            Setting::upsert($data, 'name', ['value']);
            CachingService::removeCache(config('constants.CACHE.SETTINGS'));

            ResponseService::successResponse('About Us Updated Successfully');
        } catch (Throwable $th) {
            ResponseService::logErrorResponse($th, "Setting Controller -> store");
            ResponseService::errorResponse('Something Went Wrong: ' . $th->getMessage());
        }
    }

    public function privacyPolices(Request $request)
    {
        try {
            $inputs = $request->input();
            unset($inputs['_token']);
            $data = [];

            // Handle string inputs
            foreach ($inputs as $key => $input) {
                $data[] = [
                    'name'  => $key,
                    'value' => $input,
                    'type'  => 'string',
                ];
            }

            Setting::upsert($data, 'name', ['value']);
            CachingService::removeCache(config('constants.CACHE.SETTINGS'));

            ResponseService::successResponse('Privacy Policy Updated Successfully');
        } catch (Throwable $th) {
            ResponseService::logErrorResponse($th, "Setting Controller -> store");
            ResponseService::errorResponse('Something Went Wrong: ' . $th->getMessage());
        }
    }

    public function termsAndConditions(Request $request)
    {
        try {
            $inputs = $request->input();
            unset($inputs['_token']);
            $data = [];

            // Handle string inputs
            foreach ($inputs as $key => $input) {
                $data[] = [
                    'name'  => $key,
                    'value' => $input,
                    'type'  => 'string',
                ];
            }

            Setting::upsert($data, 'name', ['value']);
            CachingService::removeCache(config('constants.CACHE.SETTINGS'));

            ResponseService::successResponse('Terms & Conditions Updated Successfully');
        } catch (Throwable $th) {
            ResponseService::logErrorResponse($th, "Setting Controller -> store");
            ResponseService::errorResponse('Something Went Wrong: ' . $th->getMessage());
        }
    }

    public function company_setup(Request $request)
    {

        ResponseService::noPermissionThenSendJson('basic-company-setup-settings');
        $validator = Validator::make($request->all(), [
            'company_email'    => 'required|email',
            'company_name'     => 'required|string|max:255',
            'company_tel1'     => 'required|regex:/^[0-9]{8,15}$/',
            'company_tel2'     => 'nullable|regex:/^[0-9]{8,15}$/',
            'company_address'  => 'required|string|max:500',
            'seo_title'        => 'required|string|min:10|max:60',
            'meta_description' => 'required|string|min:50|max:160',
            'meta_keywords'    => 'required|string|max:255',
        ], [
            'company_tel1.required' => 'The company telephone 1 field is required.',
            'company_tel1.regex'    => 'The company telephone 1 must contain only numbers.',

            'company_tel2.regex'    => 'The company telephone 2 must contain only numbers.',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $inputs = $request->except('_token');
        $data   = [];

        foreach ($inputs as $key => $input) {
            $data[] = [
                'name'  => $key,
                'value' => $input,
                'type'  => 'string',
            ];
        }

        Setting::upsert($data, 'name', ['value']);
        CachingService::removeCache(config('constants.CACHE.SETTINGS'));

        return response()->json([
            'status'   => true,
            'message'  => 'Settings Updated Successfully',
            'redirect' => route('settings.company_setup'),
        ]);
    }

    public function applicationKeysSetup(Request $request)
    {
        ResponseService::noPermissionThenSendJson('app-admob-and-weather-settings');

        $validator = Validator::make($request->all(), [

            // ANDROID
            'android_admob_app_id'        => 'required|string|max:255',
            'android_banner_ad_key'       => 'required|string|max:255',
            'android_interstitial_ad_key' => 'required|string|max:255',
            'android_open_ad_key'         => 'required|string|max:255',

            // IOS
            'ios_admob_app_id'            => 'required|string|max:255',
            'ios_banner_ad_key'           => 'required|string|max:255',
            'ios_interstitial_ad_key'     => 'required|string|max:255',
            'ios_open_ad_key'             => 'required|string|max:255',
            'weather_api_key'             => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $inputs = $request->except('_token');
        $data   = [];

        foreach ($inputs as $key => $input) {
            $data[] = [
                'name'  => $key,
                'value' => $input,
                'type'  => 'string',
            ];
        }

        Setting::upsert($data, ['name'], ['value']);
        CachingService::removeCache(config('constants.CACHE.SETTINGS'));

        return response()->json([
            'status'   => true,
            'message'  => 'AdMob Keys Updated Successfully.',
            'redirect' => route('settings.app-keys-settings'),
        ]);
    }

    public function googleAdsenseSettings(Request $request)
    {
        // Check permission (same as your original)
        ResponseService::noPermissionThenSendJson('basic-company-setup-settings');

        // Validate the inputs
        $validator = Validator::make($request->all(), [
            'adsense_client_id'     => 'required|string|max:255',
            'adsense_client_secret' => 'required|string|max:255',
            'adsense_redirect_uri'  => 'required|url|max:255',
            // Optional: you can allow token input if needed
            //'adsense_token'         => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        // Prepare data for upsert
        $inputs = $request->only(['adsense_client_id', 'adsense_client_secret', 'adsense_redirect_uri']);
        $data   = [];

        foreach ($inputs as $key => $value) {
            $data[] = [
                'name'  => $key,
                'value' => $value,
                'type'  => 'string',
            ];
        }

        // Upsert into settings table
        Setting::upsert($data, ['name'], ['value']);

        // Clear cached settings
        CachingService::removeCache(config('constants.CACHE.SETTINGS'));

        return response()->json([
            'status'   => true,
            'message'  => 'Google AdSense Configuration Updated Successfully.',
            'redirect' => route('settings.google-adsense-configuration'), // update with your settings page route
        ]);
    }

    public function custom_ad_setting(Request $request)
    {
        ResponseService::noPermissionThenSendJson('custom-advertising-settings');
        $validator = Validator::make($request->all(), [
            'currency_code'                       => 'required|string|max:10',
            'currency_symbol'                     => 'required|string|max:10',
            'approval_limit_for_admin'            => 'required|integer|min:5|max:100',
            'payment_deadline_hours'              => 'required|integer|min:0|max:72',
            'payment_deadline_minutes'            => 'required|integer|min:0|max:59',
            'header_price'                        => 'required|numeric|min:0',
            'left_sidebar_price'                  => 'required|numeric|min:0',
            'banner_slider_price'                 => 'required|numeric|min:0',
            'post_detail_page_price'              => 'required|numeric|min:0',
            'latest_price'                        => 'required|numeric|min:0',
            'popular_price'                       => 'required|numeric|min:0',
            'posts_price'                         => 'required|numeric|min:0',
            'topic_posts_price'                   => 'required|numeric|min:0',
            'videos_price'                        => 'required|numeric|min:0',
            'right_sidebar_price'                 => 'required|numeric|min:0',
            'footer_price'                        => 'required|numeric|min:0',
            'category_news_page_price'            => 'required|numeric|min:0',
            'topics_page_price'                   => 'required|numeric|min:0',
            'after_weather_section_price'         => 'required|numeric|min:0',
            'above_recommendations_section_price' => 'required|numeric|min:0',
            'channels_page_floating_price'        => 'required|numeric|min:0',
            'splash_screen_page_price'            => 'required|numeric|min:0',
            'all_channels_price'                  => 'required|numeric|min:0',
            'discover_page_floating_price'        => 'required|numeric|min:0',
            'video_page_floating_price'           => 'required|numeric|min:0',
            'after_read_more_price'               => 'required|numeric|min:0',
            'app_banner_slider_price'             => 'required|numeric|min:0',
            'sponsor_ad_rotation_time'            => 'required|integer|min:15|max:120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $inputs = $request->except('_token');
        $data   = [];

        foreach ($inputs as $key => $input) {
            $data[] = [
                'name'  => $key,
                'value' => $input,
                'type'  => 'string',
            ];
        }

        Setting::upsert($data, 'name', ['value']);
        CachingService::removeCache(config('constants.CACHE.SETTINGS'));

        return response()->json([
            'status'   => true,
            'message'  => 'Settings Updated Successfully',
            'redirect' => route('settings.custom_ads_settings'),
        ]);
    }

    public function checkActiveAds()
    {
        $activeAdsCount = 0;

        // Check for pending payment ads (approved but not paid)
        $pendingAds = SmartAdsDetail::where('ad_publish_status', 'approved')
            ->where('payment_status', 'pending')
            ->exists();

        // Check for currently running ads (paid and within date range)
        $runningAds = SmartAdsDetail::where('ad_publish_status', 'approved')
            ->where('payment_status', 'success')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->exists();

        if ($pendingAds || $runningAds) {
            // Count total active ads (both pending and running)
            $activeAdsCount = SmartAdsDetail::where('ad_publish_status', 'approved')
                ->where(function ($query) {
                    $query->where('payment_status', 'pending')
                        ->orWhere(function ($subQuery) {
                            $subQuery->where('payment_status', 'success')
                                ->where('start_date', '<=', now())
                                ->where('end_date', '>=', now());
                        });
                })
                ->count();
        }

        return response()->json([
            'has_active_ads' => $activeAdsCount > 0,
            'active_count'   => $activeAdsCount,
            'status'         => true,
        ]);
    }

    public function checkPendingPayments()
    {
        $pendingPaymentsCount = SmartAdsDetail::where('ad_publish_status', 'approved')
            ->where('payment_status', 'pending')
            ->count();

        return response()->json([
            'has_pending_payments' => $pendingPaymentsCount > 0,
            'pending_count'        => $pendingPaymentsCount,
            'status'               => true,
        ]);
    }
    public function updateFirebaseSettings(Request $request)
    {
        ResponseService::noPermissionThenSendJson('update-settings');

        $validator = Validator::make($request->all(), [
            'apiKey'            => 'required',
            'authDomain'        => 'required',
            'projectId'         => 'required',
            'storageBucket'     => 'required',
            'messagingSenderId' => 'required',
            'appId'             => 'required',
            'measurementId'     => 'required',
            'service_file'      => 'required|file|mimes:json',
        ]);

        if ($validator->fails()) {
            ResponseService::validationError($validator->errors()->first());
        }

        try {
            $inputs = $request->except(['_token', 'service_file']);

            // Save string values to settings
            $data = [];
            foreach ($inputs as $key => $input) {
                $data[] = [
                    'name'  => $key,
                    'value' => $input,
                    'type'  => 'string',
                ];
            }
            Setting::upsert($data, 'name', ['value']);

            // Handle service account JSON file
            if ($request->hasFile('service_file')) {
                $firebaseJson = $request->file('service_file');

                // Generate random filename
                $fileName = Str::random(40) . '.json';
                $filePath = 'settings/' . $fileName;

                // Store file in storage/app/public/settings/
                $firebaseJson->storeAs('settings', $fileName, 'public');

                // Delete old service file if exists
                $oldFile = Setting::where('name', 'service_file')->value('value');
                if ($oldFile && Storage::disk('public')->exists($oldFile)) {
                    Storage::disk('public')->delete($oldFile);
                }

                // Save new file path in DB (public/storage/...)
                Setting::updateOrCreate(
                    ['name' => 'service_file'],
                    [
                        'value' => $filePath, // e.g. settings/abcd123.json
                        'type'  => 'file',
                    ]
                );
            }

            // Update firebase-messaging-sw.js
            File::copy(
                public_path('assets/dummy-firebase-messaging-sw.js'),
                public_path('firebase-messaging-sw.js')
            );
            $serviceWorkerFile = file_get_contents(public_path('firebase-messaging-sw.js'));

            $updateFileStrings = [
                "apiKeyValue"            => '"' . $request->apiKey . '"',
                "authDomainValue"        => '"' . $request->authDomain . '"',
                "projectIdValue"         => '"' . $request->projectId . '"',
                "storageBucketValue"     => '"' . $request->storageBucket . '"',
                "messagingSenderIdValue" => '"' . $request->messagingSenderId . '"',
                "appIdValue"             => '"' . $request->appId . '"',
                "measurementIdValue"     => '"' . $request->measurementId . '"',
            ];

            $serviceWorkerFile = str_replace(array_keys($updateFileStrings), $updateFileStrings, $serviceWorkerFile);
            file_put_contents(public_path('firebase-messaging-sw.js'), $serviceWorkerFile);

            // Clear cache
            CachingService::removeCache(config('constants.CACHE.SETTINGS'));

            ResponseService::successResponse('Settings Updated Successfully');
        } catch (Throwable $th) {
            ResponseService::logErrorResponse($th, "Settings Controller -> updateFirebaseSettings");
            ResponseService::errorResponse();
        }
    }

    public function notification_setting(Request $request)
    {
        ResponseService::noPermissionThenSendJson('update-settings');
        $validator = Validator::make($request->all(), [
            'automatic_notifications'  => 'required|boolean',
            'daily_notification_limit' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $inputs = $request->except('_token');
        $data   = [];

        foreach ($inputs as $key => $input) {
            $data[] = [
                'name'  => $key,
                'value' => $input,
                'type'  => 'string',
            ];
        }

        Setting::upsert($data, ['name'], ['value']);
        CachingService::removeCache(config('constants.CACHE.SETTINGS'));

        return response()->json([
            'status'   => true,
            'message'  => 'Notification Settings Updated Successfully',
            'redirect' => route('settings.notification-settings'),
        ]);
    }

    public function storeNewsLanguageStatus(Request $request)
    {
        ResponseService::noPermissionThenSendJson('newslanguage-settings');
        try {
            // Handle news_languages_toggle
            $newsLanguagesToggle = $request->has('news_languages_toggle') ? 1 : 0;
            $data = [];
            $data[] = [
                'name'  => 'news_languages_toggle',
                'value' => $newsLanguagesToggle,
                'type'  => 'boolean',
            ];

            $status = $newsLanguagesToggle ? 'active' : 'inactive';

            NewsLanguageStatus::truncate();

            NewsLanguageStatus::updateStatus($status);

            // Handle selected news language
            if ($request->has('news_language')) {
                $selectedLanguageId = $request->input('news_language');

                // Deactivate all first
                NewsLanguage::where('is_active', 1)->update(['is_active' => 0]);

                // Activate selected language
                NewsLanguage::where('id', $selectedLanguageId)->update(['is_active' => 1]);
            }

            // Upsert settings
            Setting::upsert($data, 'name', ['value']);
            CachingService::removeCache(config('constants.CACHE.SETTINGS'));
            
            return redirect()->back()->with('success', 'News Language Settings Updated Successfully!!');
        } catch (Throwable $th) {
            ResponseService::logErrorResponse($th, "Setting Controller -> storeNewsLanguageStatus");
            ResponseService::errorResponse('Something Went Wrong: ' . $th->getMessage());
        }
    }
}
