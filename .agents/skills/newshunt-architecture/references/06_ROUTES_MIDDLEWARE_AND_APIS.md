# 06 - Routes, Middleware & API Specifications

## 1. Route Map Overview
Routing is separated into two primary route configuration files:
* [routes/web.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/routes/web.php) (712 lines) — Handles Installer, Admin Panel CMS, and Customer Web Frontend.
* [routes/api.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/routes/api.php) (212 lines) — Handles Mobile Application REST APIs (`/api/v1/`).

---

## 2. Middleware Stacks Matrix

| Middleware Name | Class / Registration | Scope & Purpose |
|---|---|---|
| `web.locale` | `App\Http\Middleware\WebLocale` | Applies session-selected language locale (`web_locale`) to client web pages. |
| `admin.locale` | `App\Http\Middleware\AdminLocale` | Applies language locale settings to Admin panel routes. |
| `auth` | `Illuminate\Auth\Middleware\Authenticate` | Enforces user login session authentication. |
| `auth:sanctum` | `Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful` | Mobile API Bearer Token authentication. |
| `permission:*` | `Spatie\Permission\Middleware\PermissionMiddleware` | Spatie RBAC permission gate (e.g. `permission:list-rssfeed`). |
| `track_user_visit` | `App\Http\Middleware\TrackUserVisit` | Logs unique page hit statistics. |

---

## 3. Customer Web Frontend Routes ([routes/web.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/routes/web.php))

### A. Homepage & Feeds
* `GET /` → `HomeController@index` (Middleware: `track_user_visit`)
* `GET /home` → `HomeController@index` (Name: `home`)
* `POST /set-web-language` → `HomeController@setWebLanguage`

### B. AJAX Deferred Slider Routes
* `GET /channel-posts/{channelId}` → `HomeController@getChannelPosts`
* `GET /topic-posts/{topicId}` → `HomeController@getTopicPosts`
* `GET /most-read-remaining` → `HomeController@getRemainingMostRead`
* `GET /web-stories-remaining` → `HomeController@getRemainingStories`
* `GET /top-posts-remaining` → `HomeController@getRemainingTopPosts`
* `GET /followed-channels-remaining` → `HomeController@getRemainingFollowedChannels`

### C. Search & Autocomplete
* `GET /posts` → `SearchPostController@search` (Name: `posts.search`)
* `GET /posts/ajax-search` → `SearchPostController@ajaxSearch` (Name: `posts.ajax-search`)
* `GET /posts/autocomplete` → `SearchPostController@autocomplete` (Name: `posts.autocomplete`)

### D. Single Content Pages
* `GET /posts/{slug}` → `PostDetailController@index`
* `POST /posts/{post}/react` → `ReactionController@react` (Name: `posts.react`)
* `GET /webstories` → `WebStory@index` (Name: `webstories.index`)
* `GET /webstories/{topic:slug}/{story:slug}` → `WebStory@show` (Name: `webstories.show`)
* `GET /e-newspaper` → `ENewspaperFrontController@getENewspaper`
* `GET /e-newspaper/{id}/pdf` → `ENewspaperFrontController@showPdf`

---

## 4. Administrative Panel Routes (`/admin`)

* `GET /admin/dashboard` → `DashboardController@index` (Name: `dashboard`)
* `RESOURCE /admin/users` → `UsersController`
* `RESOURCE /admin/channels` → `ChannelController`
* `RESOURCE /admin/topics` → `TopicController`
* `RESOURCE /admin/posts` → `PostController`
* `RESOURCE /admin/stories` → `StoryController`
* `RESOURCE /admin/e-newspapers` → `ENewspaperController`
* `RESOURCE /admin/rss-feeds` → `RssFeedController` (Middleware: `permission:list-rssfeed`)
* `RESOURCE /admin/pricing-plans` → `PricingPlanController`
* `RESOURCE /admin/subscriptions` → `SubscriptionController`
* `GET /admin/settings` → `SettingController@index` (System configs, SMTP, AWS, Admob, Firebase)

---

## 5. Mobile Application REST APIs (`/api/v1/`)

All responses return standard JSON envelopes (`success`, `message`, `data`).

### A. Authentication & Settings (Public)
* `POST /api/v1/register` → `UserLoginController@register`
* `POST /api/v1/login` → `UserLoginController@login`
* `POST /api/v1/firebaseauth` → `FirebaseController@firebaseTokenverify`
* `GET /api/v1/get-system-settings` → `GetSettingController@getSystemSettings`

### B. Feed Retrieval & User Actions (`auth:sanctum` Protected)
* `GET /api/v1/fetch-feeds/recommended` → `FetchRssFeedController@fetchPosts`
* `GET /api/v1/fetch-feeds/followed-channels-post` → `FetchRssFeedController@followedChannelsPosts`
* `GET /api/v1/fetch-post/descriptions/{slug}/{device_id?}/{fcm_id?}` → `FetchRssFeedController@postDescription`
* `GET /api/v1/favorites/posts` → `FavoriteController@getPosts`
* `POST /api/v1/favorites/add` → `FavoriteController@addToggleFavorite`
* `POST /api/v1/commets` → `UserCommentController@store`
* `GET /api/v1/membership_plan` → `MembershipApiController@membership_plan`
* `POST /api/v1/create-stripe-session` → `MembershipApiController@createStripeSession`
* `POST /api/v1/razorpay/verify-payment` → `MembershipApiController@verifyRazorpayPayment`
