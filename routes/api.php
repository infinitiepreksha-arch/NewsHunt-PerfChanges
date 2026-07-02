<?php

use App\Http\Controllers\Apis\ActiveUserCountController;
use App\Http\Controllers\Apis\AudioPostApiController;
use App\Http\Controllers\Apis\BookmarkController;
use App\Http\Controllers\Apis\ChannelController;
use App\Http\Controllers\Apis\ClientForApiController;
use App\Http\Controllers\Apis\ContactUsController;
use App\Http\Controllers\Apis\CreditPackApiController;
use App\Http\Controllers\Apis\CustomAdsApiController;
use App\Http\Controllers\Apis\ENewspaperApiController;
use App\Http\Controllers\Apis\FavoriteController;
use App\Http\Controllers\Apis\FetchRssFeedController;
use App\Http\Controllers\Apis\FirebaseController;
use App\Http\Controllers\Apis\GetSettingController;
use App\Http\Controllers\Apis\MembershipApiController;
use App\Http\Controllers\Apis\NewsLanguageApiController;
use App\Http\Controllers\Apis\NotificationController;
use App\Http\Controllers\Apis\ReactionsController;
use App\Http\Controllers\Apis\ReportReasonTypeApiController;
use App\Http\Controllers\Apis\SplashAndWeatherAdsController;
use App\Http\Controllers\Apis\StoryController;
use App\Http\Controllers\Apis\SubscriberApiController;
use App\Http\Controllers\Apis\SuggestionPostController;
use App\Http\Controllers\Apis\UserCommentController;
use App\Http\Controllers\Apis\UserLoginController;
use App\Http\Controllers\Apis\VideoApiController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SearchPostController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*======================================V1=APIs================================================ */

Route::group(['prefix' => 'v1'], static function () {
    /***** User Authentication Routes *****/
    Route::post('register', [UserLoginController::class, 'register']);
    Route::post('login', [UserLoginController::class, 'login']);
    Route::post('firebaseauth', [FirebaseController::class, 'firebaseTokenverify']);

    /* Get Setting APi */
    Route::get('get-system-settings', [GetSettingController::class, 'getSystemSettings']);

    /***** Home page api Starts******/
    Route::group(['prefix' => 'fetch-feeds', 'middleware' => 'auth:sanctum'], static function () {
        Route::get('banner', [FetchRssFeedController::class, 'fetchBannerPosts']);
    });

    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::group(['prefix' => 'fetch-feeds'], function () {

            Route::get('recommended', [FetchRssFeedController::class, 'fetchPosts']);
            Route::get('followerd-channels-post', [FetchRssFeedController::class, 'followedChannelsPosts']);
            Route::get('followerd-and-recommended-post', [FetchRssFeedController::class, 'fetchFollowedAndRecommendedPosts']);

            // Route::get('populer-home', [FetchRssFeedController::class, 'fetchPopularHome']);
            Route::get('populer/{page?}', [FetchRssFeedController::class, 'fetchPopularPosts']);

            /***** Fetch By Topic *****/
            Route::get('topic/{topic}', [FetchRssFeedController::class, 'fetchPostsByTopic']);
        });

        /******* Post Description api********/
        Route::get('fetch-post/descriptions/{slug}/{device_id?}/{fcm_id?}', [FetchRssFeedController::class, 'postDescription']);

        /*****Channel API & Subscribe Channel API*****/
        Route::get('fetch-feeds/channels', [ChannelController::class, 'index']);
        // Route::post('subscribe-channel/{slug}', [ChannelController::class, 'subscribeChannel']);

        Route::get('fetch-feeds/channels/{slug}', [ChannelController::class, 'getProfileData']);
        Route::get('fetch-feeds/channel/posts/{slug}', [ChannelController::class, 'getProfilePosts']);

        /***** Manage user Favorite post *****/
        Route::get('favorites/posts', [FavoriteController::class, 'getPosts']);

        /* Bookmark API */
        Route::post('favorites/add', [FavoriteController::class, 'addToggleFavorite']);
        Route::post('favorites/remove', [FavoriteController::class, 'removeToggleFavorite']);
        Route::post('favorites/pin-toggle', [FavoriteController::class, 'pinToggle']);

        /******** Search & Get Suggestions *********/
        Route::get('search/suggestion', [SuggestionPostController::class, 'getsuggestion']);
        Route::get('search/result', [SuggestionPostController::class, 'search']);

        /* User Profile */
        Route::get('get-profile', [UserLoginController::class, 'getProfile']);
        Route::post('profile-update', [UserLoginController::class, 'updateProfile']);
        Route::get('user-channel-list', [UserLoginController::class, 'getChannelList']);

        /* Comments api */
        Route::post('commets', [UserCommentController::class, 'store']);
        Route::post('commets/update', [UserCommentController::class, 'update']);
        Route::delete('delete-comment/{id}', [UserCommentController::class, 'destroy']);

        /* Report Comments */
        Route::post('commets/report', [UserCommentController::class, 'reportComment']);
        Route::post('comments/web/report', [UserCommentController::class, 'reportsWebComment']);
        Route::get('commets/check-report', [UserCommentController::class, 'checkCommentReport']);

        /************ Discover API ****************/
        Route::get('discover/posts/{page?}', [BookmarkController::class, 'discoverPosts']);
        Route::get('posts/autocomplete', [SearchPostController::class, 'autocomplete'])->name('posts.autocomplete');

        /* Notificaitons api */
        Route::get('notifications', [NotificationController::class, 'getNotificationList']);
    });
    Route::get('fetch-feeds/topics', [ChannelController::class, 'fetchTopics']);

    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::get('commets/{id}', [UserCommentController::class, 'show']);
        Route::get('commets/replies/{postId}/{parentId}', [UserCommentController::class, 'replayShow']);
    });
    /******Forget Password*********/
    Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail']);

    Route::group(['middleware' => 'auth:sanctum'], function () {

        /* Channel Subscribe */
        Route::post('channel/subscribe/{slug}', [ChannelController::class, 'subscribeChannelNew']);
        Route::post('channel/unsubscribe/{slug}', [ChannelController::class, 'unSubscribeChannel']);

        Route::post('user-profile-update', [UserLoginController::class, 'updateProfileNew']);

        /* Delete user account */
        Route::delete('remove-account', [UserLoginController::class, 'deleteUser']);

        /* User react */
        Route::get('react/{type}/{slug}', [ReactionsController::class, 'react']);
    });
    Route::get('get-reactions', [ReactionsController::class, 'getReaction']);
    Route::get('get-reactors/{type}/{slug}', [ReactionsController::class, 'getReactors']);

    Route::post('store-fcm', [NotificationController::class, 'storeOnlyFcm']);
    Route::post('notification-read', [NotificationController::class, 'handleNotificationRead']);

    // Route::post('/contact-us', [App\Http\Controllers\Apis\ContactUsController::class, 'contactUs']);
    Route::get('/contacts', [ContactUsController::class, 'getAllContacts']);
    Route::post('/contacts', [ContactUsController::class, 'createContactUs']);
    Route::get('/contacts/{id}', [ContactUsController::class, 'getContact']);

    /****** Story API*********/
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('stories/{type}/{topic?}', [StoryController::class, 'index']);
    });

    // create news languages api
    Route::post('/get-posts-by-language', [NewsLanguageApiController::class, 'getPostsByNewsLanguage']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/news-languages', [NewsLanguageApiController::class, 'getNewsLanguages']);
        // Route::post('/get-posts-by-language', [NewsLanguageApiController::class, 'getPostsByNewsLanguage']);
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/membership_plan', [MembershipApiController::class, 'membership_plan']);
        Route::post('/create-stripe-session', [MembershipApiController::class, 'createStripeSession']);
        Route::get('/payment-settings', [MembershipApiController::class, 'getPaymentSettings']);
        Route::get('transaction-history', [MembershipApiController::class, 'transaction_history']);
        Route::post('story-epaper-limit-count', [MembershipApiController::class, 'storyEpaperLimitCount']);
        Route::post('/verify-apple-receipt', [MembershipApiController::class, 'verifyAppleReceipt']);
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/razorpay/generate-signature', [MembershipApiController::class, 'generateRazorpaySignature'])->name('razorpay.generate_signature');
        Route::post('/razorpay/verify-payment', [MembershipApiController::class, 'verifyRazorpayPayment'])->name('razorpay.verify_payment');
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/custom-ads', [CustomAdsApiController::class, 'handleAds'])->name('api.ads.handle');
        Route::post('/custom-ads', [CustomAdsApiController::class, 'handleAds'])->name('api.ads.create');
        Route::get('videos', [VideoApiController::class, 'getVideos']);
    });

    Route::post('/active-users/store', [ActiveUserCountController::class, 'store']);
    Route::get('/active-users', [ActiveUserCountController::class, 'index']);
    Route::post('custom-ads/click', [CustomAdsApiController::class, 'trackClick']);

    Route::get('/ads/splash-screen', [SplashAndWeatherAdsController::class, 'splashScreenAd']);
    Route::get('/ads/after-weather-card', [SplashAndWeatherAdsController::class, 'afterWeatherCardAd']);

    Route::get('/e-newspapers', [ENewspaperApiController::class, 'getenewspaper']);

    Route::post('/subscribe', [SubscriberApiController::class, 'store']);

    Route::get('credit-packs', [CreditPackApiController::class, 'index']);

    Route::get('/report-reason-types', [ReportReasonTypeApiController::class, 'index']);
    Route::get('/report-reason-web-types', [ReportReasonTypeApiController::class, 'getWebType']);

    Route::get('/audio-posts', [AudioPostApiController::class, 'getAudio']);

    Route::post('/client-form', [ClientForApiController::class, 'store']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('verify-credits', [CreditPackApiController::class, 'verifyCreditPacksApplePurchase']);
        Route::get('user-credits', [CreditPackApiController::class, 'user_credits']);
    });
});

// stripe webhook
Route::post('/stripe/webhook', [PaymentController::class, 'stripeWebhook'])->name('stripe.webhook');
