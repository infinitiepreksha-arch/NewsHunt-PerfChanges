<?php

use App\Http\Controllers\AboutUsController;
use App\Http\Controllers\AdminControllers\AdminUserController;
use App\Http\Controllers\AdminControllers\AudioPostAdminController;
use App\Http\Controllers\AdminControllers\BlockedCommentController;
use App\Http\Controllers\AdminControllers\ChannelController;
use App\Http\Controllers\AdminControllers\CommentController;
use App\Http\Controllers\AdminControllers\ContactUsAdminController;
use App\Http\Controllers\AdminControllers\CountryController;
use App\Http\Controllers\AdminControllers\CreditPackController;
use App\Http\Controllers\AdminControllers\CustomAdsRequestController;
use App\Http\Controllers\AdminControllers\DashboardController;
use App\Http\Controllers\AdminControllers\EmailTemplateAdminController;
use App\Http\Controllers\AdminControllers\EmailTemplateForSponsorAds;
use App\Http\Controllers\AdminControllers\ENewspaperController;
use App\Http\Controllers\AdminControllers\InstallerController;
use App\Http\Controllers\AdminControllers\LanguageController;
use App\Http\Controllers\AdminControllers\LogoSettingController;
use App\Http\Controllers\AdminControllers\NewsHuntSubscriberController;
use App\Http\Controllers\AdminControllers\NewsLanguageController;
use App\Http\Controllers\AdminControllers\NotificationController;
use App\Http\Controllers\AdminControllers\PaymentGatewaySettingsController;
use App\Http\Controllers\AdminControllers\PermissionController;
use App\Http\Controllers\AdminControllers\PostController;
use App\Http\Controllers\AdminControllers\PricingPlanController;
use App\Http\Controllers\AdminControllers\ReportCommentController;
use App\Http\Controllers\AdminControllers\RoleController;
use App\Http\Controllers\AdminControllers\RssFeedController;
use App\Http\Controllers\AdminControllers\SettingController;
use App\Http\Controllers\AdminControllers\StoryController;
use App\Http\Controllers\AdminControllers\SubscriptionController;
use App\Http\Controllers\AdminControllers\SubscriptionModelController;
use App\Http\Controllers\AdminControllers\SystemUpdateController;
use App\Http\Controllers\AdminControllers\TopicController;
use App\Http\Controllers\AdminControllers\TransactionController;
use App\Http\Controllers\AdminControllers\UsersController;
use App\Http\Controllers\AdminControllers\VideoAdminController;
use App\Http\Controllers\AdminControllers\WebThemeController;
use App\Http\Controllers\Apis\ForgetPassword;
use App\Http\Controllers\AudioController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ChannelFrontController;
use App\Http\Controllers\ContactUsController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ENewspaperFrontController;
use App\Http\Controllers\FirebaseAuthController;
use App\Http\Controllers\FooterController;
use App\Http\Controllers\FrontUserController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PostDetailController;
use App\Http\Controllers\ReactionController;
use App\Http\Controllers\SearchPostController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\SmartAdManagerController;
use App\Http\Controllers\TopicFrontController;
use App\Http\Controllers\UserCommentController;
use App\Http\Controllers\UserLoginController;
use App\Http\Controllers\UserRegisterController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\WebStory;
use App\Services\CachingService;
use dacoto\LaravelWizardInstaller\Controllers\InstallDatabaseController;
use dacoto\LaravelWizardInstaller\Controllers\InstallFinishController;
use dacoto\LaravelWizardInstaller\Controllers\InstallFolderController;
use dacoto\LaravelWizardInstaller\Controllers\InstallIndexController;
use dacoto\LaravelWizardInstaller\Controllers\InstallKeysController;
use dacoto\LaravelWizardInstaller\Controllers\InstallMigrationsController;
use dacoto\LaravelWizardInstaller\Controllers\InstallServerController;
use dacoto\LaravelWizardInstaller\Controllers\InstallSetDatabaseController;
use dacoto\LaravelWizardInstaller\Controllers\InstallSetKeysController;
use dacoto\LaravelWizardInstaller\Controllers\InstallSetMigrationsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Rap2hpoutre\LaravelLogViewer\LogViewerController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();

Route::get('/', function () {
    if (! (new InstallServerController())->check() || ! (new InstallFolderController())->check()) {
        return redirect()->route('LaravelWizardInstaller::install.folders');
    } else {
        if (Auth::check()) {
            Route::middleware(['auth'])->group(function () {
                Route::get('/user/dashboard', function () {
                    return view('user.dashboard');
                })->name('user.dashboard');
                Route::get('/admin/dashboard', function () {
                    return view('/admin/login');
                })->name('home')->middleware('admin');
            });
        }
    }
    return view('auth.login');
});

Route::group([
    'prefix'    => 'install',
    'namespace' => 'dacoto\LaravelWizardInstaller\Controllers',
], static function () {
    Route::get('/', ['as' => 'install.index', 'uses' => InstallIndexController::class]);
    Route::get('/server', ['as' => 'install.server', 'uses' => InstallServerController::class]);
    Route::get('/folders', ['as' => 'install.folders', 'uses' => InstallFolderController::class]);
    Route::get('/database', ['as' => 'install.database', 'uses' => InstallDatabaseController::class]);
    Route::post('/database', ['uses' => InstallSetDatabaseController::class]);
    Route::get('/migrations', ['as' => 'install.migrations', 'uses' => InstallMigrationsController::class]);
    Route::post('/migrations', ['uses' => InstallSetMigrationsController::class]);
    Route::get('/keys', ['as' => 'install.keys', 'uses' => InstallKeysController::class]);
    Route::post('/keys', ['uses' => InstallSetKeysController::class]);
    Route::get('/finish', ['as' => 'install.finish', 'uses' => InstallFinishController::class]);
});

/*****************************************Front End Routes*********************************************/
Route::get('/', function () {
    return redirect()->route('/');
});

/************* User Authentication Routes ************/
Route::middleware(['web', 'web.locale'])->group(function () {
    Route::get('login', [UserLoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [UserLoginController::class, 'login'])->name('user.login');
    Route::post('/google-auth', [UserLoginController::class, 'googleAuth'])->name('auth.google');
    Route::post('logout', [UserLoginController::class, 'logout'])->name('logout');
    Route::post('delete-account', [UserLoginController::class, 'deleteAccount'])->name('delete-user-account');
    Route::get('first-login', function (Request $request) {
        $request->session()->forget('first_login');
        return 'success';
    });

    Route::get('reset-password', [ForgetPassword::class, 'resetPasswordLoad']);
    Route::post('password/form', [ForgetPassword::class, 'resetPassword']);
    Route::post('profile-update', [UserLoginController::class, 'changeProfileUpdate'])->name('profile-update');

    Route::post('/auth/google/callback', [FirebaseAuthController::class, 'googleCallback']);
    Route::post('/auth/phone/callback', [FirebaseAuthController::class, 'phoneCallback']);

    /***** User Register Routes ******/
    Route::get('register', [UserRegisterController::class, 'index'])->name('register');
    Route::post('register', [UserRegisterController::class, 'register'])->name('user.register');

    Route::get('my-account', [FrontUserController::class, 'index']);
    Route::get('my-account/followings', [FrontUserController::class, 'followingsChannels']);
    Route::get('my-account/bookmarks', [FrontUserController::class, 'favoritePosts'])->name('my-account.bookmarks');
    Route::post('/follow-unfollow-language', [FrontUserController::class, 'toggleNewsLanguage'])->name('language.toggle');
    Route::get('my-account/subscription', [FrontUserController::class, 'subscriptionDetails'])->name('my-account.subscription');
    Route::get('my-account/transaction', [FrontUserController::class, 'transactionDetails'])->name('my-account.transaction');
    Route::post('my-account/favorites/toggle-pin', [FrontUserController::class, 'togglePin'])->name('favorites.togglePin');

    Route::get('/', [HomeController::class, 'index'])->middleware('track_user_visit');
    Route::get('home', [HomeController::class, 'index'])->name('home');
    Route::post('/ads/click', [HomeController::class, 'trackClick'])->middleware('auth');
    Route::post('/set-web-language', [HomeController::class, 'setWebLanguage'])->name('set.web.language');
    Route::get('/get-default-locale', function () {
        $locale = session('web_locale', config('app.locale'));
        return response()->json(['locale' => $locale]);
    });
    Route::get('/preview/about-us', [HomeController::class, 'previewAboutUs'])
        ->name('preview.aboutus');

    Route::get('/preview/privacy-policies', [HomeController::class, 'previewPrivacyPolicies'])
        ->name('preview.privacypolicies');

    Route::get('/preview/terms-conditions', [HomeController::class, 'previewTermsConditions'])
        ->name('preview.termsconditions');

    Route::get('/channel-posts/{channelId}', [HomeController::class, 'getChannelPosts']);

    Route::post('change-password-via-email', [HomeController::class, 'changeAuthPasswordViaEmail'])->name('change-password-via-email.update');
    Route::get('channels/{channel?}', [ChannelFrontController::class, 'index']);
    Route::get('follow/{channel}', [ChannelFrontController::class, 'channelFollow']);

    /* Topic Page */
    Route::get('topics', [TopicFrontController::class, 'index']);
    Route::get('topics/{topic?}', [CategoryController::class, 'index']);

    /* AJAX Search (must be before posts/{slug}) */
    Route::get('posts/ajax-search', [SearchPostController::class, 'ajaxSearch'])->name('posts.ajax-search');
    Route::get('posts/autocomplete', [SearchPostController::class, 'autocomplete'])->name('posts.autocomplete');

    /* Post Detail page */
    Route::get('posts/{slug}', [PostDetailController::class, 'index']);
    Route::post('posts/favorite', [PostDetailController::class, 'favorteToggle']);
    Route::post('/set-locale', [PostDetailController::class, 'setLocale'])->name('set.locale');
    Route::get('/ads/random', [HomeController::class, 'getRandomAdBanner']);

    Route::get('/ads/{placementKey}', [HomeController::class, 'getRandomAdByPlacement']);

    /* Searching result */
    Route::get('posts', [SearchPostController::class, 'search'])->name('posts.search');


    /* Reactions routes */
    // Route::post('/posts/{post}/react', [ReactionController::class, 'react'])->middleware('auth');
    // Route::get('/posts/{post}/reactions', [ReactionController::class, 'getReactions']);
    // Route::get('/posts/{post_id}/reactors', [ReactionController::class, 'getreactData']);
    Route::post('/posts/{post}/react', [ReactionController::class, 'react'])->middleware('auth')->name('posts.react');
    Route::get('/posts/{post}/reactions', [ReactionController::class, 'getReactions'])->name('posts.reactions');
    Route::get('/posts/{post_id}/reactors', [ReactionController::class, 'getreactData'])->name('posts.reactors');

    Route::get('get-channel-data/{id}', [SearchPostController::class, 'getchannel']);

    /* Privacy & Policy */
    Route::get('contact-us', [ContactUsController::class, 'index']);
    Route::post('contact-us/store', [ContactUsController::class, 'store'])->name('contact_us.store');
    Route::get('privacy-policies', [FooterController::class, 'privacyEndPolicy'])->name('frontend-privacy-policies');
    Route::get('terms-and-condition', [FooterController::class, 'termsAndCondition'])->name('frontend-terms-and-condition');
    Route::get('about-us', [AboutUsController::class, 'index']);

    /* User comments */
    Route::get('/posts/{post}/comments', [UserCommentController::class, 'show'])->name('comments.show');
    Route::post('/comments/store', [UserCommentController::class, 'store'])->name('comments.store');
    Route::post('/comments/update', [UserCommentController::class, 'update'])->name('comments.update');
    Route::delete('/comments/delete/{id}', [UserCommentController::class, 'destroy'])->name('comments.delete');

    Route::post('subscribe/store', [NewsHuntSubscriberController::class, 'store'])->name('subscribe.store');

    Route::get('sitemap.xml', [SitemapController::class, 'index']);

    Route::get('share', function () {
        return view('front_end.classic.pages.share');
    });

    Route::get('/webstories', [WebStory::class, 'index'])->name('webstories.index');
    Route::get('/webstories/{topic:slug}/{story:slug}', [WebStory::class, 'show'])->name('webstories.show');
    Route::get('/webstories/{topic:slug}', [WebStory::class, 'storyByTopic'])->name('webstories.by.topic');

    Route::get('/membership', [MembershipController::class, 'index'])->name('membership.index');

    Route::prefix('payment')->middleware(['auth'])->group(function () {
        Route::get('/', [PaymentController::class, 'showForm'])->name('payment.form');
        Route::post('/stripe', [PaymentController::class, 'createStripeSession'])->name('payment.stripe');
        Route::get('/success', [PaymentController::class, 'success'])->name('payment.success');
        Route::post('/success', [PaymentController::class, 'success']);
        Route::get('/cancel', [PaymentController::class, 'cancel'])->name('payment.cancel');
        Route::post('/razorpay', [PaymentController::class, 'razorpayProcess'])->name('razorpay.process');
        Route::post('/razorpay/callback', [PaymentController::class, 'razorpayCallback'])->name('razorpay.callback');
        Route::post('/create-razorpay-order', [PaymentController::class, 'createRazorpayOrder'])->name('razorpay.order.create');
    });

    Route::get('/videos', [VideoController::class, 'allVideos'])->name('videos.frontend.index');

    Route::get('/audios', [AudioController::class, 'allAudios'])->name('audios.frontend.index');

    Route::get('e-newspaper', [ENewspaperFrontController::class, 'getENewspaper'])->name('e-newspaper.index');
    Route::get('e-magazine', [ENewspaperFrontController::class, 'getMagazine'])->name('e-magazine.index');
    // Route::get('/e-newspaper/pdf/{id}', [ENewspaperFrontController::class, 'accessPdf'])->name('e-newspaper.pdf');

    Route::get('/e-newspaper/{id}/pdf', [ENewspaperFrontController::class, 'showPdf'])
        ->name('e-newspaper.pdf');

    Route::get('/e-magazine/{id}/pdf', [ENewspaperFrontController::class, 'showPdf'])
        ->name('e-magazine.pdf');

    Route::prefix('sponsor-ads')->middleware(['auth'])->group(function () {
        Route::get('/', [SmartAdManagerController::class, 'smartIndex'])->name('smart-ads-index-page');
    });

    Route::prefix('smart-ads')->middleware(['auth'])->group(function () {
        Route::get('/', [SmartAdManagerController::class, 'dashboard'])->name('smart-ads.dashboard');
        Route::get('/ads', [SmartAdManagerController::class, 'index'])->name('smart-ads.index');
        Route::get('/ads/transactions', [SmartAdManagerController::class, 'transactionHistory'])->name('smart-ads-transaction-page');
        Route::get('/ads/create', [SmartAdManagerController::class, 'create'])->name('smart-ads.create');
        Route::get('/ads/{smartAd}', [SmartAdManagerController::class, 'show'])->name('smart-ads.show');
        Route::post('/ads/store', [SmartAdManagerController::class, 'store'])->name('smart-ads.store');
        Route::get('/smart-banner-auto-placements', [SmartAdManagerController::class, 'autoAds']);
        Route::post('/smart-banner-update-clicks', [SmartAdManagerController::class, 'updateClicks']);
        Route::get('/change-password', [SmartAdManagerController::class, 'editPassword'])->name('smart-ads-edit-password');
        Route::post('/update-password', [SmartAdManagerController::class, 'updatePassword'])->name('smart-ads-update-password');
        Route::get('/edit-profile', [SmartAdManagerController::class, 'editProfile'])->name('smart-ads-edit-profile');
        Route::post('/update-profile', [SmartAdManagerController::class, 'updateProfile'])->name('smart-ads-update-profile');
    });
});

Route::fallback([HomeController::class, 'themeNotFound']);

/*****************************************Front End Routes End****************************************/

/*** Dashboard Module : START ***/
Route::prefix('admin')->middleware(['admin.locale', 'auth'])->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])
        ->name('admin.login')
        ->middleware('auth.redirect');
    Route::post('login', [LoginController::class, 'login']);
    Route::get('logout', [LoginController::class, 'logout'])->name('admin.logout');
    Route::post('logout', [LoginController::class, 'logout']);

    Route::post('forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');

    // Redirect route for /admin
    Route::get('/', function () {
        if (auth()->check()) {
            return redirect()->route('dashboard');
        }
        return redirect()->route('admin.login');
    });

    Route::middleware(['authcheck', 'admin.locale'])->group(function () {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::post('change-password', [DashboardController::class, 'changePasswordUpdate'])->name('change-password.update');
        Route::get('change-password', [DashboardController::class, 'changePassword'])->name('change-password');
        Route::post('change-profile', [DashboardController::class, 'changeProfileUpdate'])->name('change-profile.update');
        Route::get('profile', [DashboardController::class, 'changeProfile'])->name('change-profile');
        Route::get('/adsense/callback', [DashboardController::class, 'adsenseCallback'])->name('admin.adsense.callback');

        Route::get('permission-restriction', [DashboardController::class, 'permissionRestricted'])->name('permission-restriction');
        /***** Get Chart data *****/
        Route::get('chart/data', [DashboardController::class, 'getMonthYearData'])->name('admin.chart.data');

        /*** Customer Module : START ***/
        Route::resource('users', UsersController::class);
        Route::post('/users/{id}/recover', [UsersController::class, 'recover'])->name('users.recover');
        Route::post('/users/{id}/block', [UsersController::class, 'blockUser'])->name('users.block');
        Route::post('/users/{id}/unblock', [UsersController::class, 'unblockUser'])->name('users.unblock');

        Route::resource('admin-users', AdminUserController::class);
        Route::get('updateFCMID', [AdminUserController::class, 'updateFCMID']);

        /*** Channel Routes : START ***/
        Route::resource('channels', ChannelController::class);
        Route::post('channel/update/status', [ChannelController::class, 'updateStatus'])->name('channel.update.status');

        /*** Topics Routes : START ***/
        Route::resource('topics', TopicController::class);
        Route::get('topics-order', [TopicController::class, 'orderIndex'])->name('topics.order');
        Route::post('topics/update-order', [TopicController::class, 'updateOrder'])->name('topics.update-order');
        Route::get('get-topics-order-by-language', [TopicController::class, 'getTopicsByLanguage'])->name('topics.get-by-language');
        Route::post('topic/update/status', [TopicController::class, 'updateStatus'])->name('topic.update.status');

        /*** Rss Feed Routes : START ***/
        Route::resource('rss-feeds', RssFeedController::class)->middleware('permission:list-rssfeed');
        Route::post('rssfeed/update/status', [RssFeedController::class, 'updateStatus'])->name('rsfeed.update.status')->middleware('permission:update-status-rssfeed');
        Route::post('rssfeed/single-fetch', [RssFeedController::class, 'singelFeedFetch'])->name('rsfeed.single-fetch');

        Route::get('/post-format', [PostController::class, 'listPost'])->name('list-of-posts');
        Route::resource('posts', PostController::class);
        Route::post('posts/{post}/sendNotification', [PostController::class, 'sendNotification'])->name('posts.notify');
        Route::post('/posts/bulk-delete', [PostController::class, 'bulkDelete'])
            ->name('posts.bulk-delete');

        Route::get('get-channels-by-language', [Controller::class, 'getChannelsByLanguage']);
        Route::get('get-topics-by-language', [Controller::class, 'getTopicsByLanguage']);

        /*** Posts Comments Routes : START ***/
        Route::resource('user-comments', CommentController::class);
        Route::get('comments', [CommentController::class, 'index'])->name('comments.index');

        /* Reported commens rotue */
        Route::resource('report-comments', ReportCommentController::class);
        Route::delete('report-comments/reason-type/{id}', [ReportCommentController::class, 'destroyReasonType'])
            ->name('report-comments.destroy-reason-type');
        Route::post('report-comments/{id}/ignore', [ReportCommentController::class, 'ignore'])->name('report-comments.ignore');
        Route::post('report-comments/{id}/remove', [ReportCommentController::class, 'remove'])->name('report-comments.remove');

        /* Blocked comments route */
        Route::resource('blocked-comments', BlockedCommentController::class);

        /*** Permission */
        Route::resource('permission', PermissionController::class);

        /******* Start Contries Routes *******/
        Route::group(['prefix' => 'countries'], static function () {
            Route::get("/", [CountryController::class, 'countryIndex'])->name('countries.index');
            Route::get("/show", [CountryController::class, 'countryShow'])->name('countries.show');
            Route::post("/import", [CountryController::class, 'importCountry'])->name('countries.import');
            Route::delete("/{id}/delete", [CountryController::class, 'destroyCountry'])->name('countries.destroy');
        });

        /*** Roles Module : END ***/
        Route::get("/roles-show", [RoleController::class, 'list'])->name('roles.list');
        Route::resource('roles', RoleController::class);

        /*** Setting Module : START ***/
        Route::group(['prefix' => 'settings'], static function () {
            Route::get('/', [SettingController::class, 'index'])->name('settings.index');
            Route::post('/store', [SettingController::class, 'store'])->name('settings.store');
            Route::post('/about_us', [SettingController::class, 'aboutUs'])->name('settings.about_privacy_terms');
            Route::post('/terms_and_conditions', [SettingController::class, 'termsAndConditions'])->name('settings.terms_and_conditions');
            Route::post('/privacy_polices', [SettingController::class, 'privacyPolices'])->name('settings.privacy_polices');
            Route::post('/company_setup', [SettingController::class, 'company_setup'])->name('settings.company_setup');
            Route::post('/app-keys-settings', [SettingController::class, 'applicationKeysSetup'])->name('settings.app-keys-settings');
            Route::post('/custom_ad_setting', [SettingController::class, 'custom_ad_setting'])->name('settings.custom_ad_setting');
            Route::post('/googleAdsenseSettings', [SettingController::class, 'googleAdsenseSettings'])->name('settings.googleAdsenseSettings');
            Route::post('/store-news-language-status', [SettingController::class, 'storeNewsLanguageStatus'])->name('settings.storeNewsLanguageStatus');

            Route::get('/check-active-ads', [SettingController::class, 'checkActiveAds'])
                ->name('settings.check_active_ads');

            Route::get('/check-pending-payments', [SettingController::class, 'checkPendingPayments'])
                ->name('settings.check_pending_payments');

            Route::get('system', [SettingController::class, 'page'])->name('settings.system');
            Route::get('about-us', [SettingController::class, 'page'])->name('settings.about-us.index')->middleware('permission:about-us-settings');
            Route::get('subscription-model', [SettingController::class, 'page'])->name('settings.subscription-model')->middleware('permission:subscription-model-and-header/footer-script-settings');
            Route::get('google-adsense-configuration', [SettingController::class, 'page'])->name('settings.google-adsense-configuration')->middleware('permission:google-adsense-configuration');

            Route::get('newslanguage_section', [SettingController::class, 'page'])->name('settings.newslanguage_section')->middleware('permission:newslanguage-settings');
            Route::get('smtp_mail_configuration', [SettingController::class, 'page'])->name('settings.smtp_mail_configuration')->middleware('permission:smtp-mail-configuration-settings');
            Route::get('company_setup', [SettingController::class, 'page'])->name('settings.company_setup')->middleware('permission:basic-company-setup-settings');
            Route::get('popular-post-setting', [SettingController::class, 'page'])->name('settings.popular-post-setting')->middleware('permission:popular-post-settings');

            Route::get('app-keys-settings', [SettingController::class, 'page'])->name('settings.app-keys-settings')->middleware('permission:app-admob-and-weather-settings');
            Route::get('system-health-monitoring', [SettingController::class, 'page'])->name('system-health-monitoring')->middleware('permission:system-health-settings');

            Route::get('logo_management_and_web_settings', [SettingController::class, 'page'])->name('settings.logo_management_and_web_settings')->middleware('permission:logo-management-and-web-settings');

            Route::get('links_and_aws_setup', [SettingController::class, 'page'])->name('settings.links_and_aws_setup')->middleware('permission:social-link-and-other-settings');
            Route::get('custom_ads_settings', [SettingController::class, 'page'])->name('settings.custom_ads_settings')->middleware('permission:custom-advertising-settings');
            Route::get('privacy-policy', [SettingController::class, 'page'])->name('settings.privacy-policy.index')->middleware('permission:privacy-policy-settings');
            Route::get('terms-conditions', [SettingController::class, 'page'])->name('settings.terms-conditions.index')->middleware('permission:terms-conditions-settings');

            Route::get('firebase', [SettingController::class, 'page'])->name('settings.firebase.index')->middleware('permission:firebase-settings');
            Route::post('firebase/update', [SettingController::class, 'updateFirebaseSettings'])->name('settings.firebase.update');

            Route::get('notification-settings', [SettingController::class, 'page'])->name('settings.notification-settings')->middleware('permission:notification-settings');
            Route::post('notification-settings', [SettingController::class, 'notification_setting'])->name('settings.notification-settings.store')->middleware('permission:notification-settings');

            Route::get('error-logs', [LogViewerController::class, 'index'])->name('settings.error-logs.index')->middleware('permission:error-logs-view-settings');

            Route::get('system-update/index', [SystemUpdateController::class, 'index'])->name('system-update.index')->middleware('permission:system-update-settings');
            Route::post('system-update/update', [SystemUpdateController::class, 'update'])->name('system-update.update');

            Route::resource('web_theme', WebThemeController::class);
            Route::post('web_theme/update/status', [WebThemeController::class, 'updateStatus'])->name('web_theme.update.status');

            /* Cronjob info */
            Route::get('cronjob/info', function () {
                return view('admin.settings.cronjob-info');
            })->name('settings.cronjob.info')->middleware('permission:cronjob/info-in-settings');

            Route::get('/payment-gateway', [PaymentGatewaySettingsController::class, 'index'])->name('payment-gateway.index')->middleware('permission:payment-gateway-settings');
            Route::post('/payment-gateway', [PaymentGatewaySettingsController::class, 'store'])->name('payment-gateway.store');

            Route::post('logo-setting', [LogoSettingController::class, 'store'])->name('settings.logo');
            Route::post('subscription-model/store', [SubscriptionModelController::class, 'store'])->name('settings.subscription-store');

        });

        Route::get('/rollback-migrations', function () {
            // Only allow this in local environment
            if (app()->environment('local')) {
                Artisan::call('migrate:rollback', ['--step' => '1']);
                return 'Rolled back 6 migration steps.';
            } else {
                abort(403, 'Unauthorized access');
            }
        });
        /* View Privacy & Policy */
        Route::get('page/privacy-policy', static function () {
            $privacy_policy = CachingService::getSystemSettings('privacy_policy');
            echo htmlspecialchars_decode($privacy_policy);
        })->name('public.privacy-policy');

        /* View Terms & Codition */
        Route::get('page/terms-conditions', static function () {
            $terms_conditions = CachingService::getSystemSettings('terms_conditions');
            echo htmlspecialchars_decode($terms_conditions);
        })->name('public.terms-conditions');
        /*** Setting Module : END ***/

        Route::group(['middleware' => ['auth', 'language']], static function () {

            /*** Language Module : START ***/
            Route::group(['prefix' => 'language'], static function () {
                Route::get('set-language/{lang}', [LanguageController::class, 'setLanguage'])->name('language.set-current');
                Route::post('storelanguage', [LanguageController::class, 'store_language'])->name('language.store_language');
                Route::get('language/{id}/translations', [LanguageController::class, 'getTranslations'])->name('language.translations');
                Route::get('language/{id}/translation', [LanguageController::class, 'editTranslations'])->name('language.translations.edit');
                Route::post('upload-file', [LanguageController::class, 'uploadFile'])->name('language.upload-file');
                Route::get('download-file/{id}', [LanguageController::class, 'downloadFile'])->name('language.download-file');
                Route::get('delete-file/{id}', [LanguageController::class, 'deleteFile'])->name('language.delete-file');
                Route::get('download-sample/{type}', [LanguageController::class, 'downloadSample'])->name('language.download-sample');
            });

            Route::resource('language', LanguageController::class)->middleware('permission:language-translation-settings');
            /*** Language Module : END ***/

            /*** Notification Module : START ***/
            Route::group(['prefix' => 'notification'], static function () {
                Route::delete('/batch-delete', [NotificationController::class, 'batchDelete'])->name('notification.batch.delete');
            });
            Route::get('/userList', [NotificationController::class, 'userListNofification'])->name('userList');
            Route::resource('notification', NotificationController::class);
            /*** Notification Module : END ***/

            /* Subscriber */
            Route::get('subscriber', [NewsHuntSubscriberController::class, 'index'])->name('subscriber.index');
            Route::get('subscriber/show', [NewsHuntSubscriberController::class, 'show'])->name('subscriber.show');

            // stories admin
            Route::resource('stories', StoryController::class);
            Route::get('/stories', [StoryController::class, 'publicIndex'])->name('stories.publicIndex');
            Route::get('create', [StoryController::class, 'create_story'])->name('create.story');
            Route::get('stories/{story}/edit', [StoryController::class, 'edit'])->name('stories.edit');
            Route::put('stories/{story}', [StoryController::class, 'update'])->name('stories.update');
            Route::post('/stories/{story}/reorder', [StoryController::class, 'updateOrder'])->name('stories.updateOrder');

            // news languages admin
            // Route::resource('news_languages', NewsLanguageController::class);
            Route::get('/news_languages', [NewsLanguageController::class, 'index'])->name('news-languages.index');
            Route::post('news_languages', [NewsLanguageController::class, 'store'])->name('admin.news-languages.store');
            Route::put('news_languages/{id}', [NewsLanguageController::class, 'update'])->name('news_languages.update');
            Route::delete('news_languages/{id}', [NewsLanguageController::class, 'destroy'])->name('admin.news-languages.destroy');
            Route::post('news-languages/reorder', [NewsLanguageController::class, 'reorder'])->name('news_languages.reorder');
            Route::post('/news_languages/{id}/update-status', [NewsLanguageController::class, 'updateStatus'])->name('news-languages.update-status');

            // Pricing Plans
            Route::get('pricing-plans', [PricingPlanController::class, 'index'])->name('pricing-plans.index');
            Route::get('pricing-plans-create', [PricingPlanController::class, 'create'])->name('pricing-plans.create');
            Route::post('pricing-plans', [PricingPlanController::class, 'store'])->name('pricing-plans.store');
            Route::get('pricing-plans/{id}/edit', [PricingPlanController::class, 'edit'])->name('pricing-plans.edit');
            Route::put('pricing-plans/{id}', [PricingPlanController::class, 'update'])->name('pricing-plans.update');
            Route::delete('pricing-plans/{id}', [PricingPlanController::class, 'destroy'])->name('pricing-plans.destroy');
            Route::put('pricing-plans/{id}/toggle-status', [PricingPlanController::class, 'toggleStatus'])->name('pricing-plans.toggleStatus');

            Route::get('subscription', [SubscriptionController::class, 'index'])->name('subscription.index');
            Route::post('/subscriptions/store', [SubscriptionController::class, 'store'])->name('subscriptions.store');
            Route::get('/subscriptions/list', [SubscriptionController::class, 'getSubscriptions'])->name('subscriptions.list');
            Route::post('subscriptions/update-status', [SubscriptionController::class, 'updateStatus'])->name('subscription.update.status');
            Route::delete('subscriptions/{id}', [SubscriptionController::class, 'destroy'])->name('subscriptions.destroy');

            Route::get('/transactions', [TransactionController::class, 'index'])->name('admin.transactions.index');
            Route::get('/transactions/{id}', [TransactionController::class, 'show'])->name('admin.transactions.show');

            /* Contact us */
            Route::get('contact-us', [ContactUsAdminController::class, 'view'])->name('contact-us.index');
            Route::get('contact-us/show_data', [ContactUsAdminController::class, 'show'])->name('contact-us.show');
            Route::delete('contact-us/{id}', [ContactUsAdminController::class, 'destroy'])->name('admin.contact-us.destroy');

            /* E-Newspaper using resources */
            Route::group(['prefix' => 'e-newspapers'], static function () {
                Route::get('/', [ENewspaperController::class, 'index'])->name('e-newspapers.index');
                Route::get('create', [ENewspaperController::class, 'create'])->name('e-newspapers.create');
                Route::post('/', [ENewspaperController::class, 'store'])->name('e-newspapers.store');
                Route::get('{id}/edit', [ENewspaperController::class, 'edit'])->name('e-newspapers.edit');
                Route::put('{id}', [ENewspaperController::class, 'update'])->name('e-newspapers.update');
                Route::delete('{id}', [ENewspaperController::class, 'destroy'])->name('e-newspapers.destroy');
            });

            Route::group(['prefix' => 'email-template-sponsor-ads'], static function () {
                Route::get('/', [EmailTemplateForSponsorAds::class, 'index'])->name('email-Sponsor-Ads.index');
                Route::get('create', [EmailTemplateForSponsorAds::class, 'create'])->name('email-Sponsor-Ads.create');
                Route::post('/', [EmailTemplateForSponsorAds::class, 'store'])->name('email-Sponsor-Ads.store');
                Route::get('datatable', [EmailTemplateForSponsorAds::class, 'show'])->name('email-Sponsor-Ads.datatable');
                Route::post('update-status', [EmailTemplateForSponsorAds::class, 'updateStatus'])->name('email-Sponsor-Ads.update-status');
                Route::delete('{id}', [EmailTemplateForSponsorAds::class, 'destroy'])->name('email-Sponsor-Ads.destroy');
            });

            Route::group(['prefix' => 'email-template'], static function () {
                Route::get('/', [EmailTemplateAdminController::class, 'index'])->name('email-template.index');
                Route::get('create', [EmailTemplateAdminController::class, 'create'])->name('email-template.create');
                Route::post('/', [EmailTemplateAdminController::class, 'store'])->name('email-template.store');
                Route::get('datatable', [EmailTemplateAdminController::class, 'show'])->name('email-template.datatable');
                Route::delete('{id}', [EmailTemplateAdminController::class, 'destroy'])->name('email-template.destroy');
                Route::post('update-status', [EmailTemplateAdminController::class, 'updateStatus'])->name('email-template.update-status');
            });
            Route::group(['prefix' => 'videos'], static function () {
                Route::get('/', [VideoAdminController::class, 'index'])->name('videos.index');
                Route::post('/{post}/sendNotification', [PostController::class, 'sendNotification'])->name('posts.notify');
                Route::get('/create/custom', [VideoAdminController::class, 'createCustom'])->name('videos.create.custom');
                Route::get('/create/youtube', [VideoAdminController::class, 'createYoutube'])->name('videos.create.youtube');

                Route::post('/', [VideoAdminController::class, 'store'])->name('videos.store');
                Route::get('/{video}', [VideoAdminController::class, 'show'])->name('videos.show');

                Route::get('/{video}/custom', [VideoAdminController::class, 'edit'])->name('videos.edit.custom');
                Route::put('/{video}/custom', [VideoAdminController::class, 'update'])->name('videos.update');

                Route::get('/{video}/youtube', [VideoAdminController::class, 'edit_youtube'])->name('videos.edit.youtube');
                Route::put('/{video}/youtube', [VideoAdminController::class, 'update_youtube'])->name('videos.update_youtube');

                Route::delete('/{video}', [VideoAdminController::class, 'destroy'])->name('videos.destroy');
                Route::post('/bulk-delete', [PostController::class, 'bulkDelete'])
                    ->name('videos.bulk-delete');
            });

            Route::group(['prefix' => 'audios'], static function () {
                Route::get('/', [AudioPostAdminController::class, 'index'])->name('audios.index');
                Route::post('/{post}/sendNotification', [PostController::class, 'sendNotification'])->name('posts.notify');
                Route::get('create', [AudioPostAdminController::class, 'create'])->name('audios.create');
                Route::post('/', [AudioPostAdminController::class, 'store'])->name('audios.store');
                Route::get('/{audios}/edit', [AudioPostAdminController::class, 'edit'])->name('audios.edit');
                Route::put('/{audios}/edit', [AudioPostAdminController::class, 'update'])->name('audios.update');
                Route::get('/{audios}', [AudioPostAdminController::class, 'show'])->name('audios.show');
                Route::delete('/{audios}', [AudioPostAdminController::class, 'destroy'])->name('audios.destroy');
                Route::post('/bulk-delete', [PostController::class, 'bulkDelete'])
                    ->name('audios.bulk-delete');
            });

            Route::resource('credit-packs', CreditPackController::class)->middleware('permission:credit-packs-settings');

            Route::group(['prefix' => 'custom-ads-requests'], static function () {
                Route::get('/', [CustomAdsRequestController::class, 'index'])->name('custom-ads-request.index');
                Route::post('{id}/update-status', [CustomAdsRequestController::class, 'updateStatus'])->name('custom-ads.update-status');

            });
        });
    });

    /************ Starts Commands  **************/
    // Route::get('clear', static function () {
    //     Artisan::call('config:clear');
    //     Artisan::call('view:clear');
    //     Artisan::call('cache:clear');
    //     Artisan::call('optimize:clear');
    //     Artisan::call('debugbar:clear');
    //     return redirect()->back();
    // });
    Route::get('/linkstorage', function () {
        Artisan::call('storage:link');
    });

    Route::get('/rollback-migration', function () {
        try {
            Artisan::call('migrate:rollback', ['--step' => 1]);
            return 'Rollback successful: ' . Artisan::output();
        } catch (\Exception $e) {
            return 'Error: ' . $e->getMessage();
        }
    });
    /* To start the queue porcess */
    Route::get('/run-queue', static function () {
        Artisan::call('rss:fetch');
        return response()->json(['error' => false, 'message' => "All Feeds fetched successfully"]);
    })->name('admin.run-queue');

    /* Migration Database */
    Route::get('/migrate', static function () {
        Artisan::call('migrate');
        echo "Done";
    });

    /* Rollback Migration */
    Route::get('/migrate-rollback', static function () {
        Artisan::call('migrate:rollback', [
            '--step' => 1,
        ]);
        return redirect()->back();
    });

    /* Reset the migration */
    Route::get('/reset-migrate', function () {
        Artisan::call('migrate:fresh');
        return 'Database tables have been deleted and re-migrated.';
    });

    /* Run the seeder */
    Route::get('/seeder', static function () {
        Artisan::call('db:seed --class=DatabaseSeeder');
        return redirect()->back();
    });

    /************ Ends Commands  **************/
    return redirect('/');
});

Route::get('clear_cache', static function () {
    Artisan::call('config:clear');
    Artisan::call('view:clear');
    Artisan::call('cache:clear');
    Artisan::call('optimize:clear');
    Artisan::call('debugbar:clear');
    return redirect()->back();
});

Route::get('/run-scheduler', function () {
    // Optional: Add a secret key for security
    if (request()->get('key') !== env('CRON_SECRET_KEY')) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    Artisan::call('schedule:run');

    return response()->json([
        'success' => true,
        'output'  => Artisan::output(),
    ]);
});
/************************************************************************************************************************************************/
/**********************************************************Unused Routes*************************************************************************/
/************************************************************************************************************************************************/

Route::group(['prefix' => 'install'], static function () {
    Route::get('purchase-code', [InstallerController::class, 'purchaseCodeIndex'])->name('install.purchase-code');
    Route::post('purchase-code', [InstallerController::class, 'checkPurchaseCode'])->name('install.purchase-code.post');
});

