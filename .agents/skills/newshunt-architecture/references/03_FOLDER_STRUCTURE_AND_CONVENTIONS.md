# 03 - Folder Structure & Architectural Conventions

## 1. Complete Application Directory Map

```
app/
├── Console/                       # Artisan Console Commands & Scheduler
│   ├── Commands/
│   │   ├── FetchRssFeeds.php      # Main RSS parser command (rss:fetch)
│   │   ├── CompressExistingImages.php # Bulk WebP migration utility (images:compress)
│   │   ├── ExpireSmartAds.php     # Expire ad campaigns command (ads:expire)
│   │   ├── PlanExpiry.php         # Check user membership expirations (expired:plan)
│   │   └── SendRecentPostsEmail.php # Send scheduled email updates
│   └── Kernel.php                 # Scheduler schedule() configuration
│
├── Constants/
│   └── DatabaseFields.php         # Standardized SELECT query column definitions
│
├── Helpers/
│   └── helper.php                 # Global custom helpers (versioned_asset, getTheme, optimize_youtube_thumb)
│
├── Http/
│   ├── Controllers/
│   │   ├── AdminControllers/      # CMS Dashboard CRUD & Configurations (35+ Controllers)
│   │   ├── Apis/                  # Mobile App REST API Controllers (26 Controllers)
│   │   ├── Auth/                  # User & Admin Login/Register/Password Reset Controllers
│   │   ├── HomeController.php     # Homepage Feed Builder, Shuffling & De-duplication
│   │   ├── PostDetailController.php # Article Single View, Reactions, Bookmarks & Guest Checks
│   │   ├── SearchPostController.php # Union Multi-Table Search & Autocomplete
│   │   ├── WebStory.php           # Web Stories Slider View & View Counter Cookie Guard
│   │   └── PaymentController.php  # Web Checkout Controllers (Stripe & Razorpay)
│   │
│   ├── Middleware/
│   │   ├── AdminLocale.php        # Set Admin language locale
│   │   ├── WebLocale.php          # Set Web Customer language locale
│   │   ├── TrackUserVisit.php     # Log visitor hits
│   │   └── CheckAdminRole.php     # Admin panel access guard
│   │
│   └── Requests/                  # Form Validation Request Classes
│
├── Models/                        # 60+ Eloquent Model Entities (User, Post, Story, Subscription, etc.)
├── Providers/
│   ├── AppServiceProvider.php     # Bootstrapper for View Composer & Cache Invalidation Listeners
│   ├── AuthServiceProvider.php    # Passport & Sanctum authorization policies
│   ├── EventServiceProvider.php   # Event-Listener bindings
│   └── RouteServiceProvider.php   # Route files mapping & API rate limiting
│
├── Services/
│   ├── CachingService.php         # Server-side persistent key-value configuration cache layer
│   └── FileService.php            # File upload processing, WebP encoding & aspect compression
│
└── Traits/
    └── SelectsFields.php          # Query projection helper trait for optimized SELECT arrays
```

---

## 2. Architectural Design Conventions

### A. Request-Scoped Attributes Pattern
To prevent memory leaks and stale state issues in persistent runner environments (FrankenPHP, Swoole, RoadRunner):
* **Convention:** Never store request-specific variables in PHP class `static` properties.
* **Implementation:** Store temporary variables in Laravel's request attributes (`request()->attributes`).
```php
$request = request();
if ($request->attributes->has('subscribed_language_ids')) {
    $languageIds = $request->attributes->get('subscribed_language_ids');
} else {
    $languageIds = NewsLanguageSubscriber::where('user_id', $userId)->pluck('news_language_id');
    $request->attributes->set('subscribed_language_ids', $languageIds);
}
```

### B. Query Projection Trait (`SelectsFields`)
* **Convention:** Avoid `SELECT *` in heavy database queries.
* **Implementation:** Controllers use `SelectsFields` ([SelectsFields.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Traits/SelectsFields.php)) to retrieve pre-configured column arrays (e.g. `selectPostDescriptionFields()`), keeping database transfer payloads small.

### C. Services & Helper Isolation
* **`FileService::resizeAndCompressUpload()`:** All administrative image upload forms pass uploaded files through this utility to enforce aspect ratio constraints (max width 800px) and convert assets to WebP format.
* **`CachingService::getSystemSettings()`:** Replaces direct `Setting::where(...)` database queries with `Cache::rememberForever()` lookups.
* **`versioned_asset()`:** Appends dynamic file-modification timestamps (`filemtime`) to CSS/JS imports in Blade views to ensure browser-side cache busting.
