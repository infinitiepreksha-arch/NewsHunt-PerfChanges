# File Changes Log & Master Codebase Tracking Index

This document is the master log of all feature updates, performance optimizations, and bug fixes performed on the **NewsHunt** codebase, tracking every application code file modified or created since project inception.

*(Note: Internal AI configuration files under `.agents/` are tracked separately in [.agents/AGENTS_CHANGES_LOG.md](file:///.agents/AGENTS_CHANGES_LOG.md)).*

---

## Task Change Logs (Chronological History)

### 1. [Initial Phase] Core Architecture & Database Query Optimizations
* **Task Description:** Fixed wildcard View Composer multi-execution loop, added setting table memory container, resolved N+1 topic/channel model hydration blowups, cached default themes/languages, and applied global namespace facade safety.
* **Files Changed:**
  * [app/Providers/AppServiceProvider.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Providers/AppServiceProvider.php)
  * [app/Http/Controllers/HomeController.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Http/Controllers/HomeController.php)
  * [app/Helpers/helper.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Helpers/helper.php)

---

### 2. [2026-07-06] Preloader FOUC (Flash of Unstyled Content) Fix
* **Task Description:** Replaced dynamic javascript-generated preloader in `app-head-bs.js` with a static inline HTML preloader overlay inside `<body>` to eliminate content flashing before stylesheet compilation.
* **Files Changed:**
  * [resources/views/front_end/classic/layout/main.blade.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/resources/views/front_end/classic/layout/main.blade.php)
  * [public/front_end/classic/js/app-head-bs.js](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/public/front_end/classic/js/app-head-bs.js)

---

### 3. [2026-07-07] Media & YouTube Asset Loading Deferral
* **Task Description:** Converted homepage YouTube carousel `<iframe>` tags to use `data-src` and `lazy-iframe` classes monitored via `IntersectionObserver` in `main.blade.php`, saving 13.7MB of initial downloads. Cleaned up conflicting `fetchpriority="high"` attributes on below-the-fold images.
* **Files Changed:**
  * [resources/views/front_end/classic/pages/index.blade.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/resources/views/front_end/classic/pages/index.blade.php)
  * [resources/views/front_end/classic/layout/main.blade.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/resources/views/front_end/classic/layout/main.blade.php)
  * [resources/views/front_end/classic/layout/header.blade.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/resources/views/front_end/classic/layout/header.blade.php)

---

### 4. [2026-07-07] E-Newspaper Banner Image URL Fix
* **Task Description:** Fixed double `storage/` domain prefixing in `HomeController@getEnewsSettings` and `index.blade.php` data attributes to repair broken e-paper banner background graphics.
* **Files Changed:**
  * [app/Http/Controllers/HomeController.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Http/Controllers/HomeController.php)
  * [resources/views/front_end/classic/pages/index.blade.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/resources/views/front_end/classic/pages/index.blade.php)

---

### 5. [2026-07-07] Programmatic Image Lazy Loading (IntersectionObserver)
* **Task Description:** Deferred download of 25 below-the-fold images and hidden header dropdown flags by swapping `src` with a 1x1 transparent base64 spacer and monitoring `lazy-img` tags via `IntersectionObserver`.
* **Files Changed:**
  * [resources/views/front_end/classic/layout/main.blade.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/resources/views/front_end/classic/layout/main.blade.php)
  * [resources/views/front_end/classic/pages/index.blade.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/resources/views/front_end/classic/pages/index.blade.php)
  * [resources/views/front_end/classic/layout/header.blade.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/resources/views/front_end/classic/layout/header.blade.php)

---

### 6. [2026-07-07] Video Thumbnail Poster & Carousel Slide Optimization
* **Task Description:** Replaced auto-loading YouTube iframes inside video sliders with high-res `maxresdefault.jpg` posters and overlay play buttons, mounting players dynamically upon click. Added zero-dimension layout checks in `main.blade.php` to defer hidden modal flags.
* **Files Changed:**
  * [resources/views/front_end/classic/pages/index.blade.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/resources/views/front_end/classic/pages/index.blade.php)
  * [resources/views/front_end/classic/layout/main.blade.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/resources/views/front_end/classic/layout/main.blade.php)
  * [resources/views/front_end/classic/layout/header.blade.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/resources/views/front_end/classic/layout/header.blade.php)

---

### 7. [2026-07-09] Versioned Asset Cache Busting
* **Task Description:** Implemented `versioned_asset()` helper in `helper.php` to append `filemtime` to static assets, enabling browser caching while preventing outdated script locks across layout templates.
* **Files Changed:**
  * [app/Helpers/helper.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Helpers/helper.php)
  * [resources/views/front_end/classic/layout/style.blade.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/resources/views/front_end/classic/layout/style.blade.php)
  * [resources/views/front_end/classic/layout/script.blade.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/resources/views/front_end/classic/layout/script.blade.php)
  * [resources/views/front_end/classic/layout/main.blade.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/resources/views/front_end/classic/layout/main.blade.php)

---

### 8. [2026-07-10] YouTube Thumbnail Optimization & Database Query Caching
* **Task Description:** Downscaled YouTube cover images to `mqdefault.jpg` via `Post` Eloquent model accessor. Cached system settings forever (`rememberForever`) and homepage feed queries for 10 minutes in `HomeController`.
* **Files Changed:**
  * [app/Helpers/helper.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Helpers/helper.php)
  * [app/Models/Post.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Models/Post.php)
  * [app/Services/CachingService.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Services/CachingService.php)
  * [app/Http/Controllers/HomeController.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Http/Controllers/HomeController.php)

---

### 9. [2026-07-10] WebP Image Upload Compression & Artisan Conversion Command
* **Task Description:** Integrated Intervention Image aspect-ratio scaling (max 800px width) and 60% quality WebP encoding in `FileService`. Updated Admin controllers and created `CompressExistingImages` command (`images:compress`) along with web trigger route `/images-compress`.
* **Files Changed:**
  * [app/Services/FileService.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Services/FileService.php)
  * [app/Http/Controllers/AdminControllers/PostController.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Http/Controllers/AdminControllers/PostController.php)
  * [app/Http/Controllers/AdminControllers/AudioPostAdminController.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Http/Controllers/AdminControllers/AudioPostAdminController.php)
  * [app/Http/Controllers/AdminControllers/VideoAdminController.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Http/Controllers/AdminControllers/VideoAdminController.php)
  * [app/Http/Controllers/AdminControllers/NewsLanguageController.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Http/Controllers/AdminControllers/NewsLanguageController.php)
  * [app/Http/Controllers/AdminControllers/LogoSettingController.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Http/Controllers/AdminControllers/LogoSettingController.php)
  * [app/Http/Controllers/AdminControllers/SettingController.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Http/Controllers/AdminControllers/SettingController.php)
  * [app/Http/Controllers/AdminControllers/TopicController.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Http/Controllers/AdminControllers/TopicController.php)
  * [app/Http/Controllers/AdminControllers/ChannelController.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Http/Controllers/AdminControllers/ChannelController.php)
  * [app/Http/Controllers/AdminControllers/LanguageController.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Http/Controllers/AdminControllers/LanguageController.php)
  * [app/Http/Controllers/AdminControllers/NotificationController.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Http/Controllers/AdminControllers/NotificationController.php)
  * [app/Console/Commands/CompressExistingImages.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Console/Commands/CompressExistingImages.php)
  * [routes/web.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/routes/web.php)

---

### 10. [2026-07-13] Request Attribute Caching Migration & Script Load Order
* **Task Description:** Replaced static properties in providers with Symfony request attributes (`request()->attributes`). Fixed Cloudflare Rocket Loader script race conditions by adding a `window.iziToast` fallback definition in `custom-jquery.js` and forced timestamp cache-busting reloads.
* **Files Changed:**
  * [app/Providers/AppServiceProvider.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Providers/AppServiceProvider.php)
  * [app/Http/Controllers/HomeController.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Http/Controllers/HomeController.php)
  * [resources/views/front_end/classic/layout/script.blade.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/resources/views/front_end/classic/layout/script.blade.php)
  * [public/front_end/classic/js/custom/custom-jquery.js](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/public/front_end/classic/js/custom/custom-jquery.js)

---

### 11. [2026-07-14] AJAX Deferred Loading & Slider Pagination
* **Task Description:** Deferred navbar dropdowns and carousel feeds (Most Read, Web Stories, Top Posts, Followed Channels) to AJAX endpoints, returning JSON arrays to client-side HTML builders in `custom-jquery.js`. Centered category loading spinners.
* **Files Changed:**
  * [app/Providers/AppServiceProvider.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Providers/AppServiceProvider.php)
  * [app/Http/Controllers/HomeController.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Http/Controllers/HomeController.php)
  * [routes/web.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/routes/web.php)
  * [resources/views/front_end/classic/layout/header.blade.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/resources/views/front_end/classic/layout/header.blade.php)
  * [resources/views/front_end/classic/pages/index.blade.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/resources/views/front_end/classic/pages/index.blade.php)
  * [resources/views/front_end/classic/layout/main.blade.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/resources/views/front_end/classic/layout/main.blade.php)
  * [public/front_end/classic/js/custom/custom-jquery.js](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/public/front_end/classic/js/custom/custom-jquery.js)
  * [resources/views/front_end/classic/pages/partials/topic_dropdown_posts.blade.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/resources/views/front_end/classic/pages/partials/topic_dropdown_posts.blade.php)
  * [resources/views/front_end/classic/pages/partials/most_read_slides.blade.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/resources/views/front_end/classic/pages/partials/most_read_slides.blade.php)
  * [resources/views/front_end/classic/pages/partials/web_story_slides.blade.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/resources/views/front_end/classic/pages/partials/web_story_slides.blade.php)

---

### 12. [2026-07-15] Homepage Feed Shuffling & Boundary De-duplication
* **Task Description:** Consolidated homepage feed queries into a single 32-item collection, unique-filtered by title and shuffled in memory. Passed client-side displayed slide IDs to AJAX endpoints via `displayed_ids` array to prevent duplicates across Swiper boundaries.
* **Files Changed:**
  * [app/Http/Controllers/HomeController.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Http/Controllers/HomeController.php)
  * [resources/views/front_end/classic/pages/index.blade.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/resources/views/front_end/classic/pages/index.blade.php)
  * [public/front_end/classic/js/custom/custom-jquery.js](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/public/front_end/classic/js/custom/custom-jquery.js)
  * [resources/views/front_end/classic/pages/partials/followed_channels_slides.blade.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/resources/views/front_end/classic/pages/partials/followed_channels_slides.blade.php)
  * [resources/views/front_end/classic/pages/partials/most_read_slides.blade.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/resources/views/front_end/classic/pages/partials/most_read_slides.blade.php)
  * [resources/views/front_end/classic/pages/partials/top_posts_slides.blade.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/resources/views/front_end/classic/pages/partials/top_posts_slides.blade.php)
  * [resources/views/front_end/classic/pages/partials/web_story_slides.blade.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/resources/views/front_end/classic/pages/partials/web_story_slides.blade.php)

---

### 13. [2026-07-16] Advanced Search Refactoring & Multi-Format Filters
* **Task Description:** Redirected search submission modal to dedicated page `/posts?search=query`. Refactored `SearchPostController` to execute subquery unions across `posts`, `stories`, and `e_newspapers`. Added split Video/YouTube/Audio checkboxes and history `pushState` pagination.
* **Files Changed:**
  * [public/front_end/classic/js/custom/search-news.js](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/public/front_end/classic/js/custom/search-news.js)
  * [resources/views/front_end/classic/layout/header.blade.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/resources/views/front_end/classic/layout/header.blade.php)
  * [app/Http/Controllers/SearchPostController.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Http/Controllers/SearchPostController.php)
  * [resources/views/front_end/classic/pages/search-result.blade.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/resources/views/front_end/classic/pages/search-result.blade.php)
  * [resources/views/front_end/classic/pages/partials/search_result_posts.blade.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/resources/views/front_end/classic/pages/partials/search_result_posts.blade.php)

---

### 14. [2026-07-16] PageSpeed Score & Core Web Vitals Optimization
* **Task Description:** Added `width`/`height` attributes to channel badges and weather icons. Added pre-initialization CSS overrides for Swiper slides to eliminate CLS layout shifts, eager-loaded LCP banner images (`fetchpriority="high"`), and added WebP flag image compression in `LanguageController`.
* **Files Changed:**
  * [app/Http/Controllers/AdminControllers/LanguageController.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Http/Controllers/AdminControllers/LanguageController.php)
  * [resources/views/front_end/classic/layout/main.blade.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/resources/views/front_end/classic/layout/main.blade.php)
  * [resources/views/front_end/classic/layout/header.blade.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/resources/views/front_end/classic/layout/header.blade.php)
  * [resources/views/front_end/classic/pages/index.blade.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/resources/views/front_end/classic/pages/index.blade.php)
  * [resources/views/front_end/classic/pages/partials/top_posts_slides.blade.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/resources/views/front_end/classic/pages/partials/top_posts_slides.blade.php)
  * [resources/views/front_end/classic/pages/partials/search_result_posts.blade.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/resources/views/front_end/classic/pages/partials/search_result_posts.blade.php)
  * [public/front_end/classic/css/custom.css](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/public/front_end/classic/css/custom.css)

---

### 15. [2026-07-17] Post Detail Page & Global View Composer Caching
* **Task Description:** Cached reaction definitions permanently in memory, mapped UUIDs without N+1 loops, bypassed favorites/views DB lookups for guests, restored `.epaper_css` 300px height CLS fix, added Eloquent event listeners to clear language caches, and set up `view_composer_cache_buster`.
* **Files Changed:**
  * [app/Providers/AppServiceProvider.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Providers/AppServiceProvider.php)
  * [app/Http/Controllers/PostDetailController.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Http/Controllers/PostDetailController.php)
  * [resources/views/front_end/classic/pages/index.blade.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/resources/views/front_end/classic/pages/index.blade.php)
  * [app/Http/Controllers/Apis/BookmarkController.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Http/Controllers/Apis/BookmarkController.php)
  * [app/Http/Controllers/Apis/FetchRssFeedController.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Http/Controllers/Apis/FetchRssFeedController.php)

---

### 16. [2026-07-20] Enterprise Architecture Knowledge Base & Tracking Index Setup
* **Task Description:** Created the complete 13-part architecture knowledge base skill inside `.agents/skills/newshunt-architecture/`, including master rules, technical specifications, database mappings, business rules, paywalls, and cross-device responsiveness guidelines.
* **Files Changed:**
  * [FILE_CHANGES_LOG.md](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/FILE_CHANGES_LOG.md)
  * [PROJECT_HISTORY.md](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/PROJECT_HISTORY.md)

---

### 17. [2026-07-20] Git Repository Cleanup & Ignore Rules Update
* **Task Description:** Added `.claude/`, `.vscode/`, and `test_route_list.php` to `.gitignore` and removed the temporary scratch script `test_route_list.php`.
* **Files Changed:**
  * [.gitignore](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/.gitignore)

---

## 📌 Master Go-To Index of All Changed Codebase Files

**Total Unique Codebase Files Modified/Created Till Now:** 42 Files


| # | File Path | Primary Task / Feature | Date Modified |
|---|---|---|---|
| 1 | [PROJECT_HISTORY.md](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/PROJECT_HISTORY.md) | Documentation & Architecture Sync | 2026-07-20 |
| 2 | [FILE_CHANGES_LOG.md](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/FILE_CHANGES_LOG.md) | File Changes Tracker Setup | 2026-07-20 |
| 3 | [routes/web.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/routes/web.php) | AJAX Sliders & WebP Route Triggers | 2026-07-14 |
| 4 | [app/Helpers/helper.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Helpers/helper.php) | Versioned Asset & YouTube Accessor | 2026-07-10 |
| 5 | [app/Models/Post.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Models/Post.php) | YouTube Thumbnail Downscaler Accessor | 2026-07-10 |
| 6 | [app/Services/FileService.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Services/FileService.php) | WebP Image Upload Compression | 2026-07-10 |
| 7 | [app/Services/CachingService.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Services/CachingService.php) | Settings Permanent Caching Layer | 2026-07-10 |
| 8 | [app/Providers/AppServiceProvider.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Providers/AppServiceProvider.php) | View Composer Caching & Buster | 2026-07-17 |
| 9 | [app/Http/Controllers/HomeController.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Http/Controllers/HomeController.php) | Homepage Feed Shuffling & Deduplication | 2026-07-15 |
| 10 | [app/Http/Controllers/PostDetailController.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Http/Controllers/PostDetailController.php) | Post Detail Page Query Optimization | 2026-07-17 |
| 11 | [app/Http/Controllers/SearchPostController.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Http/Controllers/SearchPostController.php) | Multi-Table Subquery Union Search | 2026-07-16 |
| 12 | [app/Http/Controllers/AdminControllers/PostController.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Http/Controllers/AdminControllers/PostController.php) | WebP Image Upload Compression | 2026-07-10 |
| 13 | [app/Http/Controllers/AdminControllers/AudioPostAdminController.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Http/Controllers/AdminControllers/AudioPostAdminController.php) | WebP Uploads & Constants Cleanups | 2026-07-10 |
| 14 | [app/Http/Controllers/AdminControllers/VideoAdminController.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Http/Controllers/AdminControllers/VideoAdminController.php) | WebP Image Upload Compression | 2026-07-10 |
| 15 | [app/Http/Controllers/AdminControllers/NewsLanguageController.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Http/Controllers/AdminControllers/NewsLanguageController.php) | WebP Flag Compression | 2026-07-11 |
| 16 | [app/Http/Controllers/AdminControllers/LogoSettingController.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Http/Controllers/AdminControllers/LogoSettingController.php) | WebP Logo Upload Compression | 2026-07-11 |
| 17 | [app/Http/Controllers/AdminControllers/SettingController.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Http/Controllers/AdminControllers/SettingController.php) | WebP Setting Image Compression | 2026-07-11 |
| 18 | [app/Http/Controllers/AdminControllers/TopicController.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Http/Controllers/AdminControllers/TopicController.php) | WebP Category Image Compression | 2026-07-11 |
| 19 | [app/Http/Controllers/AdminControllers/ChannelController.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Http/Controllers/AdminControllers/ChannelController.php) | WebP Channel Logo Compression | 2026-07-11 |
| 20 | [app/Http/Controllers/AdminControllers/LanguageController.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Http/Controllers/AdminControllers/LanguageController.php) | Flag Image Resizing & WebP | 2026-07-16 |
| 21 | [app/Http/Controllers/AdminControllers/NotificationController.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Http/Controllers/AdminControllers/NotificationController.php) | Notification Image WebP Support | 2026-07-11 |
| 22 | [app/Http/Controllers/Apis/BookmarkController.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Http/Controllers/Apis/BookmarkController.php) | PHP 8.1 Null-Safety Decoding | 2026-07-17 |
| 23 | [app/Http/Controllers/Apis/FetchRssFeedController.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Http/Controllers/Apis/FetchRssFeedController.php) | PHP 8.1 Null-Safety Decoding | 2026-07-17 |
| 24 | [app/Console/Commands/CompressExistingImages.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Console/Commands/CompressExistingImages.php) | Artisan In-Place WebP Converter | 2026-07-11 |
| 25 | [resources/views/admin/layouts/app.blade.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/resources/views/admin/layouts/app.blade.php) | Conditional Lottie Script Deferral | 2026-07-10 |
| 26 | [resources/views/front_end/classic/layout/main.blade.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/resources/views/front_end/classic/layout/main.blade.php) | Preloader & IntersectionObserver | 2026-07-16 |
| 27 | [resources/views/front_end/classic/layout/header.blade.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/resources/views/front_end/classic/layout/header.blade.php) | Flag Images Deferred Loading | 2026-07-16 |
| 28 | [resources/views/front_end/classic/layout/style.blade.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/resources/views/front_end/classic/layout/style.blade.php) | Versioned Asset Links | 2026-07-09 |
| 29 | [resources/views/front_end/classic/layout/script.blade.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/resources/views/front_end/classic/layout/script.blade.php) | Script Load Order & De-duplication | 2026-07-13 |
| 30 | [resources/views/front_end/classic/pages/index.blade.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/resources/views/front_end/classic/pages/index.blade.php) | LCP Prioritization & E-Newspaper CLS | 2026-07-17 |
| 31 | [resources/views/front_end/classic/pages/search-result.blade.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/resources/views/front_end/classic/pages/search-result.blade.php) | Search Filter Grid View | 2026-07-16 |
| 32 | [resources/views/front_end/classic/pages/partials/topic_dropdown_posts.blade.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/resources/views/front_end/classic/pages/partials/topic_dropdown_posts.blade.php) | Navbar Dropdown Post Partial | 2026-07-14 |
| 33 | [resources/views/front_end/classic/pages/partials/most_read_slides.blade.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/resources/views/front_end/classic/pages/partials/most_read_slides.blade.php) | Most Read Slider Partial | 2026-07-15 |
| 34 | [resources/views/front_end/classic/pages/partials/web_story_slides.blade.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/resources/views/front_end/classic/pages/partials/web_story_slides.blade.php) | Web Story Slider Partial | 2026-07-15 |
| 35 | [resources/views/front_end/classic/pages/partials/top_posts_slides.blade.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/resources/views/front_end/classic/pages/partials/top_posts_slides.blade.php) | Top Posts Carousel Partial | 2026-07-16 |
| 36 | [resources/views/front_end/classic/pages/partials/followed_channels_slides.blade.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/resources/views/front_end/classic/pages/partials/followed_channels_slides.blade.php) | Followed Channels Slider Partial | 2026-07-15 |
| 37 | [resources/views/front_end/classic/pages/partials/search_result_posts.blade.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/resources/views/front_end/classic/pages/partials/search_result_posts.blade.php) | Search Results Grid Partial | 2026-07-16 |
| 38 | [public/front_end/classic/css/custom.css](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/public/front_end/classic/css/custom.css) | `.epaper_css` CLS Fix & Swiper CSS | 2026-07-16 |
| 39 | [public/front_end/classic/js/app-head-bs.js](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/public/front_end/classic/js/app-head-bs.js) | Static Preloader Configuration | 2026-07-06 |
| 40 | [public/front_end/classic/js/custom/custom-jquery.js](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/public/front_end/classic/js/custom/custom-jquery.js) | Slider Paginators & JSON Builders | 2026-07-15 |
| 41 | [public/front_end/classic/js/custom/search-news.js](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/public/front_end/classic/js/custom/search-news.js) | Search AJAX & pushState Paginator | 2026-07-16 |
| 42 | [.gitignore](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/.gitignore) | Local IDE Folders & Scratch Script Exclusions | 2026-07-20 |

