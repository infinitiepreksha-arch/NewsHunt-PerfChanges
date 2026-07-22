# Performance Optimization Report & Project History

This document details the performance optimization process, comparisons of the original raw code vs. current code, and the rationale behind each change made to the NewsHunt system.

---

## 1. Preventing Multi-Execution of View Composer

### The Problem:
The View Composer in `AppServiceProvider.php` was registered with a wildcard `*` matching all view templates:
```php
View::composer('*', function ($view) { ... });
```
In Laravel, this composer callback runs **every time** any view template (layouts, headers, footers, sidebars, content widgets) is compiled. On a single page request, this View Composer callback executed **4 to 5 times**, resulting in the exact same 17 database queries running repeatedly (150+ total queries, mostly duplicates).

### The Fix:
We added a static cache checking mechanism inside the composer closure in `AppServiceProvider.php`.

#### Comparison:
* **Old Code (Original):**
  ```php
  View::composer('*', function ($view) {
      try {
          $composerData = [];
          // ... (executes 17 DB queries every single time a view compiles)
          $view->with($composerData);
      } catch (Throwable $e) { ... }
  });
  ```
* **Current Code (Optimized):**
  ```php
  View::composer('*', function ($view) {
      static $sharedViewData = null;
      if ($sharedViewData !== null) {
          if (isset($sharedViewData['finalLanguageCode'])) {
              app()->setLocale($sharedViewData['finalLanguageCode']);
          }
          $view->with($sharedViewData);
          return;
      }
      try {
          $composerData = [];
          // ... (executes database queries ONLY on the first run)
          $sharedViewData = $composerData;
          $view->with($sharedViewData);
      } catch (Throwable $e) { ... }
  });
  ```
* **Why:** By saving the loaded data into a static request-level variable `$sharedViewData`, subsequent templates bypass the database queries completely and read directly from memory.

---

## 2. Setting Table Database Query & Model Hydration Optimization

### The Problem:
1. **Redundant Queries:** The `settings` table was queried repeatedly across separate files (`HomeController`, `AppServiceProvider`, helper methods).
2. **Eloquent Model Hydration Overhead:** Setting configurations are basic key-value strings. Querying them using Eloquent models (`Setting::select(...)->get()`) caused Laravel to instantiate **146 separate model instances** in memory, which consumes significant RAM and CPU.

### The Fix:
1. Added a request-level settings container `AppServiceProvider::$settingsCache`.
2. Loaded settings once in `HomeController@index` using a raw query builder to prevent model instantiation, and cached it.
3. Made `AppServiceProvider` and helper functions retrieve values directly from the cache.

#### Comparison:
* **Old Code (Original):**
  * *HomeController.php:*
    ```php
    $this->allSettings = Setting::pluck('value', 'name');
    ```
  * *AppServiceProvider.php:*
    ```php
    // In view composer:
    $allSettings = Setting::select('name', 'value', 'updated_at')->get()->keyBy('name');
    $socialsettings = Setting::pluck('value', 'name');
    $freeTrialSettings = Setting::whereIn('name', [...])->pluck('value', 'name');
    $cookiesPopupStatus = Setting::select('value')->where('name', 'cookies_popup_status')->first();
    // In helper methods:
    $settings = Setting::whereIn('name', [...])->pluck('value', 'name'); // getNewsletterSettings()
    $firebaseSettings = Setting::whereIn('name', [...])->pluck('value', 'name'); // getFirebaseConfig()
    ```
* **Current Code (Optimized):**
  * *AppServiceProvider.php (Static properties):*
    ```php
    public static $settingsCache = null;
    ```
  * *HomeController.php:*
    ```php
    if (\App\Providers\AppServiceProvider::$settingsCache !== null) {
        $this->allSettings = \App\Providers\AppServiceProvider::$settingsCache->map(fn($item) => $item->value);
    } else {
        // Query DB directly to prevent Eloquent from instantiating 146 Setting models.
        $settingsList = \Illuminate\Support\Facades\DB::table('settings')->select('name', 'value', 'type')->get();
        $this->allSettings = $settingsList->mapWithKeys(function ($item) {
            $value = $item->value;
            if ($item->type === 'file') {
                $value = !empty($value) ? url(\Illuminate\Support\Facades\Storage::url($value)) : '';
            }
            return [$item->name => $value];
        });
        \App\Providers\AppServiceProvider::$settingsCache = $settingsList->keyBy('name');
    }
    ```
  * *AppServiceProvider.php:*
    ```php
    if (static::$settingsCache !== null) {
        $allSettings = static::$settingsCache;
    } else {
        $allSettings = \Illuminate\Support\Facades\DB::table('settings')->select('name', 'value', 'updated_at')->get()->keyBy('name');
        static::$settingsCache = $allSettings;
    }
    $socialsettings = $allSettings->map(fn($item) => $item->value);
    $freeTrialSettings = [
        'free_trial_status' => $getSetting('free_trial_status')->value ?? '0',
        ...
    ];
    $cookiesPopupStatus = $getSetting('cookies_popup_status');
    $newsletterSettings = $this->getNewsletterSettings($allSettings);
    $firebaseConfig = $this->getFirebaseConfig($allSettings);
    ```
* **Why:** This reduces database queries to **exactly zero** inside the View Composer for settings, and prevents the creation of 146 Eloquent setting models (reduced from 146 retrieved models to 0 setting models).

---

## 3. News Language Retrieval Optimization

### The Problem:
Both `HomeController` and `AppServiceProvider` view composer (and helper methods) were querying `NewsLanguage::where('is_active', 1)->first()` independently.

### The Fix:
Added `AppServiceProvider::$activeLanguageCache` static caching to reuse the default language model instance across files.

#### Comparison:
* **Old Code (Original):**
  ```php
  // Executed separately in HomeController.php and twice in AppServiceProvider.php:
  $defaultActiveLanguage = NewsLanguage::where('is_active', 1)->first();
  ```
* **Current Code (Optimized):**
  ```php
  // Declared in AppServiceProvider:
  public static $activeLanguageCache = null;

  // Resolved in HomeController and AppServiceProvider view composer:
  if (\App\Providers\AppServiceProvider::$activeLanguageCache !== null) {
      $defaultActiveLanguage = \App\Providers\AppServiceProvider::$activeLanguageCache;
  } else {
      $defaultActiveLanguage = NewsLanguage::where('is_active', 1)->first();
      \App\Providers\AppServiceProvider::$activeLanguageCache = $defaultActiveLanguage;
  }
  ```

---

## 4. Theme Check Caching

### The Problem:
`Theme::select('slug')->where('is_default', '1')->first()` was queried multiple times across the helper file `helper.php` and `AppServiceProvider.php` (once for each call to check default theme).

### The Fix:
Added PHP `static` caching to the `getTheme()` helper and modified `AppServiceProvider@getTheme` to call this helper.

#### Comparison:
* **Old Code (Original):**
  * *helper.php:*
    ```php
    function getTheme() {
        try {
            $themeData = Theme::select('slug')->where('is_default', '1')->first();
            return optional($themeData)->slug ?? 'classic';
        } catch (Throwable $e) { return ""; }
    }
    ```
  * *AppServiceProvider.php:*
    ```php
    protected function getTheme() {
        try {
            $themeData = Theme::select('slug')->where('is_default', '1')->first();
            return optional($themeData)->slug ?? 'classic';
        } catch (Throwable $e) { return ""; }
    }
    ```
* **Current Code (Optimized):**
  * *helper.php:*
    ```php
    function getTheme() {
        static $cachedTheme = null;
        if ($cachedTheme !== null) {
            return $cachedTheme;
        }
        try {
            $themeData = Theme::select('slug')->where('is_default', '1')->first();
            $cachedTheme = optional($themeData)->slug ?? 'classic';
            return $cachedTheme;
        } catch (Throwable $e) { return ""; }
    }
    ```
  * *AppServiceProvider.php:*
    ```php
    protected function getTheme() {
        return getTheme();
    }
    ```

---

## 5. Model Hydration Blowup Optimization (Topic & Channel Posts)

### The Problem:
While trying to optimize the N+1 query issue for Category and Channel lists, a single query was used to pull all posts `Post::whereIn('topic_id', $topicIds)->get()`. While this reduced queries, it fetched **every single post** (1,400+ rows) under those categories and instantiated them as Eloquent models, only to throw 95% of them away in memory (since we only show the top 5 per topic and 4 per channel).

### The Fix:
Switched back to running individual targeted queries inside the loops, but with a strict database-level limit (`take(5)` and `take(4)`).

#### Comparison:
* **Intermediate Code (Model Blowup):**
  ```php
  $allTopicPosts = Post::select(...)
      ->whereIn('topic_id', $topicIds)
      ->get() // Hydrated 1,400 models
      ->groupBy('topic_id');
  ```
* **Current Code (Optimized):**
  ```php
  // We execute small, indexed queries that only load what we show:
  foreach ($topics as $topic) {
      $topic->posts = Post::select('id', 'image', 'video', 'video_thumb', 'type', 'title', 'slug', 'comment', 'publish_date', 'pubdate', 'status', 'view_count', 'reaction', 'topic_id')
          ->where('posts.status', 'active')
          ->where('topic_id', $topic->id)
          ->whereHas('channel', function ($query) {
              $query->where('status', 'active');
          })
          ->when($subscribedLanguageIds->isNotEmpty(), function ($q) use ($subscribedLanguageIds) {
              $q->whereIn('posts.news_language_id', $subscribedLanguageIds);
          })
          ->orderBy('publish_date', 'DESC')
          ->take(5)
          ->get()
          ->map(function ($item) use ($defaultImage) { ... });
  }
  ```
* **Why:** Since there are at most 8 topics and 6 channels, this runs a few very fast queries (about 12 more queries) but instantiates **only 64 post models** in memory instead of **1,400 post models**. This saves massive memory overhead and rendering lag.

---

## 6. Global Class Namespace Safety

### The Problem:
In specific hosting/deployment environments, global class aliases (like `DB` or `Storage`) might not be registered or mapped inside controllers. Using raw `\DB` resulted in "Class DB not found" runtime exceptions.

### The Fix:
Replaced all facade calls with their fully qualified paths:
* `\DB::` -> `\Illuminate\Support\Facades\DB::`
* `\Storage::` -> `\Illuminate\Support\Facades\Storage::`

---

## Final Performance Metrics Comparison

| Metric | Original baseline | Current Optimized Code | Improvement |
| :--- | :---: | :---: | :---: |
| **Total DB Queries** | **171** | **52** | **3.3x Fewer Queries** |
| **Duplicate Queries** | **150** | **4** | **97.3% Reduction** |
| **Hydrated Models** | **1,626** | **206** | **87.3% Memory Savings** |
| **Database Execution Time** | **470ms** | **38.27ms** | **12.2x Faster Queries** |
| **Total Page Load Time** | **1,170ms** | **343ms** | **3.4x Faster Loading** |

---

## [2026-07-06] Preloader FOUC (Flash of Unstyled Content) Fix

### Feature
* Prevent the navbar and page content from flashing on the screen before the loading preloader overlay shows up.

### Files Modified
* [resources/views/front_end/classic/layout/main.blade.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/resources/views/front_end/classic/layout/main.blade.php)
* [public/front_end/classic/js/app-head-bs.js](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/public/front_end/classic/js/app-head-bs.js)

### Logic Changes
* **The Root Cause:**
  1. The preloader class (`show-preloader`) was removed inside `app-head-bs.js` on the `DOMContentLoaded` event. Since `DOMContentLoaded` fires *before* CSS stylesheets and assets finish downloading, the body became visible before styles were applied, leading to a Flash of Unstyled Content (FOUC).
  2. Because the site is behind Cloudflare, the external JS file `app-head-bs.js` gets aggressively cached. Changes to it may not reflect instantly for all users.
* **The Fix:**
  1. Disabled the dynamic preloader generator in `app-head-bs.js` (`ENABLE_PAGE_PRELOADER = false`) to completely bypass script caching/CDN issues.
  2. Implemented a fully **static HTML preloader container** directly in the `<body>` of [main.blade.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/resources/views/front_end/classic/layout/main.blade.php).
  3. Added the preloader's core styling and a `DOMContentLoaded` listener inline within the layout file's `<head>`.
  4. The screen immediately paints a solid white/dark preloader screen containing the spinner with `z-index: 999999`. The navbar and content render invisibly behind it, completely eliminating any FOUC or navbar flashing before/during load. Once the DOM structure and CSS finish loading, the inline listener fades the preloader out and removes it, without waiting for heavy external scripts or slow images.

### 5. Media & Asset Loading Optimizations (July 7, 2026)
* **The Problem:** The homepage was loading over **270 requests** and downloading **50.7 MB** of raw resources (30.9 MB transferred) on initial load, causing heavy network contention. This delayed the `DOMContentLoaded` event to **16.49 seconds** and total load time to **18.97 seconds**.
* **The Root Causes:**
  1. **YouTube Iframe Bloat:** The homepage video carousel contained 5 YouTube video `<iframe>` embeds. Even when hidden, browsers immediately downloaded the full YouTube document player, custom stylesheets, and core scripts (`base.js` - **457 KB** each, total **2.3 MB**) for all 5 embeds concurrently on initial load.
  2. **Image Priority Conflict:** Below-the-fold images had a conflicting combination of `loading="lazy"` and `fetchpriority="high"`. Modern browsers prioritized the `high` fetch priority tag, completely bypassing the lazy-loading engine and preloading all images immediately with high priority on page load.
* **The Conceptual Solution:**
  1. **Programmatic Lazy Loading (IntersectionObserver):** Swap out the native browser-controlled lazy loading (which has aggressive, network-based preloading heuristics) for programmatic lazy loading.
  2. **Eliminate Contradictory Attributes:** Strip `fetchpriority="high"` from below-the-fold images so the browser's native lazy loading can take effect normally.
* **The Code Changes:**
  1. **YouTube IFrame Restructuring** in [index.blade.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/resources/views/front_end/classic/pages/index.blade.php):
     * Changed `src="{{ $videoPost->video }}"` to `data-src="{{ $videoPost->video }}"`.
     * Replaced `loading="lazy"` with `class="lazy-iframe"`.
     * *Why:* Omitting the `src` attribute prevents the browser from making any connection or request to YouTube during initial page paint.
  2. **IntersectionObserver Script** in [main.blade.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/resources/views/front_end/classic/layout/main.blade.php) head:
     * Injected a vanilla JavaScript listener that monitors all `iframe.lazy-iframe` elements using the browser's high-performance `IntersectionObserver` API:
       ```javascript
       const lazyIframes = document.querySelectorAll("iframe.lazy-iframe");
       if ("IntersectionObserver" in window) {
           const observer = new IntersectionObserver((entries, obs) => {
               entries.forEach(entry => {
                   if (entry.isIntersecting) {
                       const iframe = entry.target;
                       iframe.src = iframe.getAttribute("data-src");
                       iframe.classList.remove("lazy-iframe");
                       obs.unobserve(iframe);
                   }
               });
           }, { rootMargin: "0px 0px 400px 0px" });
           lazyIframes.forEach(iframe => observer.observe(iframe));
       } else {
           // Fallback
           lazyIframes.forEach(iframe => iframe.src = iframe.getAttribute("data-src"));
       }
       ```
     * *Why:* The YouTube video player page is only requested when the carousel/video container enters within `400px` of the visible viewport.
  3. **Attribute Cleanups** in [index.blade.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/resources/views/front_end/classic/pages/index.blade.php) and [header.blade.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/resources/views/front_end/classic/layout/header.blade.php):
     * Removed `fetchpriority="high"` from below-the-fold image cards (e.g. e-papers, trending list, audio lists, channel dropdowns) to re-enable correct lazy loading.
* **The Results (Verified):**
  * **Network requests** dropped from **270 to 182**.
  * **Total resource download size** dropped from **50.7 MB to 37.0 MB** (**13.7 MB saved** on initial paint!).
  * **DOMContentLoaded time** dropped from **16.49 seconds to 6.87 seconds** (**9.6 seconds saved**!).
  * **Total Load time** dropped from **18.97 seconds to 6.99 seconds** (**12 seconds saved**!).
  * **`base.js` player scripts** are completely deferred, loading only when scrolling down to the videos section.

#### 6. E-Newspaper Image URL Fix (July 7, 2026)
* **What We Did:**
  We fixed the broken background image of the e-newspaper banner section on the homepage, which was showing as a broken image icon.
* **Why We Did It:**
  In `HomeController@getEnewsSettings`, the image path was being wrapped in `url('storage/')`. However, because setting accessors are already simulated and returned fully-qualified URLs (e.g. `https://.../storage/settings/...`), this resulted in a double prefix. Furthermore, the frontend template in `index.blade.php` was also wrapping the path with `url('storage/')` again, which resulted in a broken URL: `https://newshunt-dev.infinitietech.in/storage/https://newshunt-dev.infinitietech.in/storage/settings/...`.
* **How We Fixed It:**
  1. **Controller-side Fix in `app/Http/Controllers/HomeController.php`:**
     We added defensive checks to check if the database value is already a full URL or starts with `storage/` before prepending directories:
     ```php
     // BEFORE:
     $paperImage = url('storage/' . $paperImage);

     // AFTER:
     if (str_starts_with($paperImage, 'http://') || str_starts_with($paperImage, 'https://')) {
         // Do nothing
     } elseif (str_starts_with($paperImage, 'storage/')) {
         $paperImage = url($paperImage);
     } else {
         $paperImage = url('storage/' . $paperImage);
     }
     ```
  2. **View-side Fix in `resources/views/front_end/classic/pages/index.blade.php`:**
     We updated the `data-src` attribute of the e-newspaper image (line 590) to output `$getEnewsSettings['paperimage']` directly, removing the redundant `url('storage/' . ...)` wrapper:
     ```html
     <!-- BEFORE: -->
     data-src="{{ url('storage/' . $getEnewsSettings['paperimage']) }}"

     <!-- AFTER: -->
     data-src="{{ $getEnewsSettings['paperimage'] }}"
     ```

### 7. Programmatic Image Lazy Loading (July 7, 2026)
* **What We Did:**
  We deferred the download of all 18 below-the-fold homepage images and the 7 hidden navigation dropdown images inside the header until they enter the user's viewport.
* **Why We Did It:**
  Chrome and other Chromium browsers use aggressive native lazy-loading distance thresholds on fast broadband connections, fetching images up to 4000px below the fold. This caused all 18 homepage images and 7 dropdown images (over 30MB combined) to download concurrently on load, clogging the network pipeline, causing huge load times, and triggering YouTube player auto-throttling (dropping video stream/thumbnail resolution to 360p due to lag).
* **How We Fixed It:**
  1. **Extended `IntersectionObserver` Engine in `resources/views/front_end/classic/layout/main.blade.php`:**
     We updated our global vanilla JS loader to observe both `iframe.lazy-iframe` and `img.lazy-img` tags, loading them dynamically when they scroll within 400px of the viewport:
     ```javascript
     const lazyElements = document.querySelectorAll("iframe.lazy-iframe, img.lazy-img");
     if ("IntersectionObserver" in window) {
         const observer = new IntersectionObserver((entries, obs) => {
             entries.forEach(entry => {
                 if (entry.isIntersecting) {
                     const el = entry.target;
                     el.src = el.getAttribute("data-src");
                     el.classList.remove("lazy-iframe", "lazy-img");
                     obs.unobserve(el);
                 }
             });
         }, { rootMargin: "0px 0px 400px 0px" });
         lazyElements.forEach(el => observer.observe(el));
     }
     ```
  2. **Restructured View Files (`index.blade.php` & `header.blade.php`):**
     We swapped out the `src` attribute of these 25 images for an inline 1x1 transparent spacer: `src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"`.
     We moved the real URLs to `data-src` and appended the `lazy-img` class.
     * *Note: We designed a robust regex `/\ssrc\s*=\s*(?:"[^"]*"|\'[^\']*\')/i` to ensure `data-src` was never overwritten or broken during parsing.*
     ```html
     <!-- BEFORE: -->
     <img class="media-cover image" src="{{ $post->image }}" data-src="{{ $post->image }}" loading="lazy">

     <!-- AFTER: -->
     <img class="media-cover image lazy-img" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="{{ $post->image }}" loading="lazy">
     ```
* **The Results:**
  * **Initial page payload** dropped from **35.0 MB to <2.5 MB**.
  * **Network requests** dropped, network congestion was cleared, and the initial page load time dropped to **1-2 seconds**!
  * With network congestion eliminated, the YouTube video player automatically streams at maximum high resolution instead of auto-throttling to 360p.

### 8. Phase 2 Homepage Image Optimization (July 7, 2026)
* **What We Did:**
  Optimized the remaining images on the homepage that were loading on page initialization: the hidden slides in the Banner Carousel, Web Stories carousel slides, and the remaining below-the-fold image card loops (News by Topic, Trending sidebar, etc.).
* **Why We Did It:**
  1. The Banner Carousel loops fetched 10 slides on page load, loading ~3.5MB of YouTube maxresdefault.jpg cover images immediately even though only the first slide is visible.
  2. The Web Stories and News by Topic cards lacked any lazy-loading logic, fetching their images immediately.
* **How We Fixed It:**
  1. **Banner Carousel Conditionally Lazy-Loaded:**
     Modified the banner carousel loop to render `fetchpriority="high"` and the real image path ONLY on the first slide. Subsequent slides are rendered with the transparent base64 spacer and `lazy-img` class:
     ```html
     <img class="media-cover image {{ !$loop->first ? 'lazy-img' : '' }}"
         src="{{ $loop->first ? ($banner->image ?? $defaultImage) : 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7' }}"
         data-src="{{ $banner->image ?? $defaultImage }}"
         @if($loop->first) fetchpriority="high" @endif>
     ```
  2. **Web Stories Lazy Loading:**
     Updated the cards in the Web Stories slide loop to use the `lazy-img` class and base64 spacer for their initial `src` attribute.
  3. **Below-the-fold Loops:**
     Automatically transformed the remaining image card loops (such as News by Topic, Channel Logos, and Audio Post cards) to defer download.
* **The Results:**
  Initial load payload is reduced by another ~6MB.

### 9. Phase 3 Header Language Image Optimization (July 7, 2026)
* **What We Did:**
  Optimized the language selection dropdown images inside `header.blade.php` to load lazily, saving an additional **4.8 MB** of initial page weight.
* **Why We Did It:**
  The administration uploaded very large images (approx. 2MB each) to represent language options. Because these dropdowns are rendered inside `header.blade.php`, they loaded immediately on page load even though they are hidden inside popup tabs, bloating the page size.
* **How We Fixed It:**
  Modified the dropdown layout loops (both News Languages and Web Languages tabs) inside [header.blade.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/resources/views/front_end/classic/layout/header.blade.php) to use programmatic lazy loading with our `lazy-img` class and base64 spacer:
  ```html
  <img data-src="{{ asset('storage/' . $news_language->image) ?? '' }}"
      src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
      class="media-cover image lazy-img"
      alt="{{ $news_language->name }}" />
  ```
* **The Results:**
  Initial download size drops by **4.8 MB**. The language flag/background images now only load when the user opens the Language Settings modal and switches tabs.

### 10. Phase 4 Modal/Hidden Element Lazy Load Optimization (July 7, 2026)
* **What We Did:**
  Improved the global `IntersectionObserver` callback inside `main.blade.php` to skip loading elements that are hidden inside closed modals or tabs on page load.
* **Why We Did It:**
  UIKit modals are styled with `display: none` but can briefly report as intersecting during initial layout rendering, which triggered immediate download of the 3.2MB language flag images even though the modal was closed.
* **How We Fixed It:**
  Added a dimensions check using `getBoundingClientRect()` inside [main.blade.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/resources/views/front_end/classic/layout/main.blade.php) line 24. If the target element has a width and height of 0, we skip swapping the `src` attribute and keep observing:
  ```javascript
  const rect = el.getBoundingClientRect();
  if (rect.width === 0 && rect.height === 0) {
      return;
  }
  ```
* **The Results:**
  All hidden lazy elements (such as language select flags inside modals) are now deferred completely until they are shown.

### 11. Phase 5 Video Thumbnail Poster Optimization (July 7, 2026)
* **What We Did:**
  Replaced the auto-loading YouTube iframes inside the "Latest News Videos" slider with high-resolution thumbnail images (`maxresdefault.jpg`) and overlay play buttons, mounting the player dynamically on demand.
* **Why We Did It:**
  YouTube embeds automatically throttle their stream/poster resolution to low-quality `default.jpg` (120x90) if the browser initializes multiple iframes concurrently or experiences network congestion on startup, creating a highly pixelated and blurry poster.
* **How We Fixed It:**
  1. Updated the slide loops in [index.blade.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/resources/views/front_end/classic/pages/index.blade.php) line 1549 to display a high-resolution `<img>` container (`video_thumb`) with a YouTube-styled SVG play button on top.
  2. Implemented a click delegate inside [main.blade.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/resources/views/front_end/classic/layout/main.blade.php) line 45. When the user clicks the play button, the placeholder is dynamically replaced with an autoplaying iframe.
* **The Results:**
  1. The video thumbnails load at maximum crisp resolution (up to 1280x720 pixels).
  2. Deferring the YouTube player embeds saves an additional **4.5 MB+** of initial bundle downloads and improves CPU blocking time (TBT).

### 12. Phase 6 Homepage Post De-duplication (July 7, 2026)
* **What We Did:**
  Implemented a post tracking mechanism in `HomeController` to prevent the exact same posts from showing in multiple sections (Banners, Top Posts, Latest Videos, and Latest News) on the homepage.
* **Why We Did It:**
  Because the same chronological queries (`orderBy('publish_date', 'desc')`) were used for multiple homepage sections, the newest posts (typically the latest RSS-fetched YouTube videos) repeated multiple times across Banners, Top Posts, Latest Videos, and Latest News sections, creating a redundant and poor user experience.
* **How We Fixed It:**
  1. Initialized a `$displayedPostIds` tracking array starting with the IDs retrieved from the banner carousel (`$postBanners`).
  2. Modified `$top_posts_query`, `getPostsWithVideos()`, and `$latesNewsQuery` in [HomeController.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Http/Controllers/HomeController.php) to filter out already displayed IDs via `whereNotIn('posts.id', $displayedPostIds)`.
  3. Merged newly retrieved post IDs into `$displayedPostIds` after each section query.
* **The Results:**
  All four primary chronological sections on the homepage are completely de-duplicated, showing a diverse, rich selection of posts across different categories and formats.

### 13. Phase 7 Load Time Optimizations (July 9, 2026)
* **What We Did:**
  Implemented automatic file-modification caching (via `versioned_asset`), removed unused e-paper stylesheets from the main head, and added Google Font preconnect links.
* **Why We Did It:**
  1. The classic layouts previously used `?v=<?= time() ?>` for local assets, preventing browser caching and forcing ~1.5MB of downloads on every single click or reload.
  2. Unused book-reader styles were loaded globally, acting as render-blocking requests on the homepage.
  3. Slow TLS handshakes for web fonts delayed FCP.
* **How We Fixed It:**
  1. Created a `versioned_asset()` helper inside [helper.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Helpers/helper.php) that appends `filemtime($filePath)` to asset URLs.
  2. Overhauled [style.blade.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/resources/views/front_end/classic/layout/style.blade.php) and [script.blade.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/resources/views/front_end/classic/layout/script.blade.php) to use `versioned_asset()` and deleted 4 unused book-view links.
  3. Added font domain preconnect links to [main.blade.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/resources/views/front_end/classic/layout/main.blade.php).
* **The Results:**
  Enabled full browser caching. Subsequent load time drops to under **1.5 seconds**, FCP is optimized, and redundant network handshakes are eliminated.

### 14. Phase 7.1 YouTube Thumbnail & Server Query Caching (July 10, 2026)
* **What We Did:**
  1. Optimized YouTube video cover image sizes by replacing `maxresdefault.jpg` with `mqdefault.jpg` at the database model layer.
  2. Changed system settings and languages caching from a 1-hour expiration to cache **forever** (using `rememberForever`).
  3. Cached all homepage widgets and category query results inside `HomeController` for 10 minutes.
* **Why We Did It:**
  1. High-resolution YouTube thumbnails were loading at 340KB each, bloating the homepage payload size by 2.2MB.
  2. Recurring database queries on the homepage settings, languages, and news loops caused a high Time-to-First-Byte (TTFB) delay on the server.
* **How We Fixed It:**
  1. Appended `optimize_youtube_thumb()` inside [helper.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Helpers/helper.php) and implemented the `getVideoThumbAttribute` Eloquent accessor inside [Post.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Models/Post.php).
  2. Modified [CachingService.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Services/CachingService.php) to use `Cache::rememberForever` for settings and languages.
  3. Wrapped all Eloquent news feed builders inside [HomeController.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Http/Controllers/HomeController.php) indexes with language-specific `Cache::remember` closures for 10 minutes.
* **The Results:**
  1. Reduced page payload weight by **over 2.2 Megabytes**, dramatically decreasing initial load times on all connections.
  2. Drops homepage backend processing time (TTFB) to **under 50ms**.

### 15. Phase 7.2 Script De-duplication and Lottie Lazy-loading (July 10, 2026)
* **What We Did:**
  1. Fixed CLI bootstrapping/Artisan fatal error by removing unused/conflicting `const STOREGE = 'storage/';` declarations from `PostController.php` and `AudioPostAdminController.php`.
  2. Removed global Lottie player script loads (~500KB) from `script.blade.php` and `app.blade.php`.
  3. Added conditional Lottie loading to views that actually contain `<dotlottie-player>` elements (`success.blade.php`, `cancel.blade.php`, `smart_ads_request.blade.php`, `create.blade.php`, `edit-password.blade.php`).
  4. De-duplicated sweetalert2 and iziToast imports inside `script.blade.php`.
* **Why We Did It:**
  1. Unused global player scripts were adding over 500KB of render-blocking JS on the homepage.
  2. Duplicate script tags loaded unminified files alongside minified files.
* **How We Fixed It:**
  1. Removed conflicting global constants `STOREGE`.
  2. Deleted `iziToast.js` and `sweetalert2.js` from `script.blade.php`, keeping only minified/unified scripts.
  3. Extracted Lottie imports into page-specific scripts sections using `@section('script')` and `@section('scripts')`.
* **The Results:**
  1. Restored full CLI and Artisan functionalities.
  2. Shaved off another **~500KB** from the initial bundle sizes on the home page and other dynamic pages, speeding up FCP.

### 16. Phase 7.3 Image Upload Compression & Artisan Command (July 10, 2026)
* **What We Did:**
  1. Created `resizeAndCompressUpload` static method in [FileService.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Services/FileService.php) to resize local images (max 800px width) and compress quality to 60% using Intervention Image.
  2. Modified [PostController.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Http/Controllers/AdminControllers/PostController.php), [AudioPostAdminController.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Http/Controllers/AdminControllers/AudioPostAdminController.php), and [VideoAdminController.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Http/Controllers/AdminControllers/VideoAdminController.php) local upload blocks to call this compression utility.
  3. Created an Artisan command [CompressExistingImages.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Console/Commands/CompressExistingImages.php) (`php artisan images:compress`) to scan and compress all existing uploaded featured, gallery, and video thumbnail images in-place.
* **Why We Did It:**
  1. High-resolution raw image uploads from administrators (up to 3MB-5MB per post) were causing the homepage payload size to reach **11.4 Megabytes**, resulting in 5s-12s load times.
* **How We Fixed It:**
  1. Integrated Intervention Image quality reduction (60%) and aspect-ratio scaling (max 800px width) at the upload layer.
  2. Built a console tool to optimize existing assets in-place.
* **The Results:**
  1. Newly uploaded images are compressed up to **80%** on storage size.
  2. Running the Artisan command on staging optimizes all existing uploads in-place, bringing the total page payload down to under **2 Megabytes**.
  3. Added a secure web-accessible route `/images-compress` in [web.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/routes/web.php) to allow triggering this compression via FTP deployment (no SSH terminal access required).

### 17. Phase 8: WebP Image Optimization & Lazy Loading Cleanups (July 11, 2026)

* **What We Did:**
  1. Modified image compression helper methods in [FileService.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Services/FileService.php) to automatically encode uploaded JPG/PNG images to WebP format.
  2. Upgraded admin controllers ([NewsLanguageController.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Http/Controllers/AdminControllers/NewsLanguageController.php), [LogoSettingController.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Http/Controllers/AdminControllers/LogoSettingController.php), [SettingController.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Http/Controllers/AdminControllers/SettingController.php), [TopicController.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Http/Controllers/AdminControllers/TopicController.php), [ChannelController.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Http/Controllers/AdminControllers/ChannelController.php)) to run their uploaded files through the WebP compression utility.
  3. Updated MIME validation rules in controllers that previously blocked `.webp` uploads ([PostController.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Http/Controllers/AdminControllers/PostController.php), [LanguageController.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Http/Controllers/AdminControllers/LanguageController.php), [NotificationController.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Http/Controllers/AdminControllers/NotificationController.php)).
  4. Redesigned the [CompressExistingImages.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Console/Commands/CompressExistingImages.php) artisan command to convert all existing JPG/PNG assets on the server to `.webp`, and execute search-and-replace queries across database tables (`posts`, `post_images`, `settings`, `news_languages`, `topics`, `channels`) to point references to the new `.webp` paths.
  5. Stripped conflicting static `fetchpriority="high"` attributes on all below-the-fold elements in [index.blade.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/resources/views/front_end/classic/pages/index.blade.php) to ensure proper execution of browser/programmatic lazy loading.

* **Why We Did It:**
  1. Although images were resized/compressed, they were still served in traditional JPEG/PNG formats. Auto-converting to modern WebP reduces payload sizes by an additional 30-50% with negligible quality loss.
  2. Substantial assets (logos, headers, language flags) bypass default pipelines and were stored raw (creating 2MB+ unoptimized requests).
  3. Validations had to be normalized to allow users and scripts to save WebP assets.
  4. Contradictory `fetchpriority="high"` attributes on lazy-loaded below-the-fold images caused the browser to bypass lazy-loading rules, generating immediate downloads on page initialization.

* **The Results:**
  1. All new manual uploads are saved on disk and database directly as `.webp`.
  2. Existing images on the server are fully migrated to WebP format, saving storage space and network transfer size.
  3. Below-the-fold page painting is completely deferred, optimizing FCP/LCP and overall home page weight.

### 18. Phase 9: Language Selection, Caching Refactor & Script Load Order Fixes (July 13, 2026)

* **What We Did:**
  1. **Request-Scoped Cache Migration:** Replaced static properties (`static $sharedViewData`, `static::$settingsCache`, `static::$activeLanguageCache`) inside `AppServiceProvider.php` and `HomeController.php` with Laravel's dynamic request attributes (`request()->attributes`).
  2. **Script Load Order Correction:** Reordered `<script>` declarations inside `script.blade.php` to parse core libraries (`iziToast.min.js`, `sweetalert2.all.min.js`) immediately after core jQuery/Bootstrap bundles, before loading theme-specific scripts.
  3. **Global Toaster Protection Mock:** Added a window-level fallback mock definitions object for `iziToast` at the top of `custom-jquery.js`.
  4. **Cache-Busting Reloads:** Replaced `window.location.reload()` with a dynamic timestamp-appended parameter redirect (`?refresh=timestamp`) and integrated an auto-cleanup block using HTML5 `window.history.replaceState`.

* **Why We Did It:**
  1. **Persistent Static States:** In persistent PHP daemon environments (Swoole, FrankenPHP, RoadRunner, or cached multi-process threads), static variables are not cleared between HTTP requests. This caused the View Composer to serve the cached English view data even after the user session was updated to Hindi.
  2. **Rocket Loader Race Conditions:** Cloudflare's Rocket Loader processes deferred/async scripts out-of-order. Since `iziToast` script tags were located below custom page scripts, the language change AJAX promise triggered a fatal `ReferenceError: iziToast is not defined` crash. This exception crashed the thread, preventing it from reaching the reload command.
  3. **Browser Cache Reload Loop:** Standard `window.location.reload()` triggers a validation request. If the server or CDN (Cloudflare) returns `304 Not Modified`, the browser reloads the cached English view.

* **How We Fixed It:**
  1. **Request Attributes:**
     ```php
     // Check:
     $request = request();
     if ($request->attributes->has('shared_view_data')) {
         $sharedViewData = $request->attributes->get('shared_view_data');
         // ...
     }
     
     // Set:
     $request->attributes->set('shared_view_data', $sharedViewData);
     ```
  2. **Toaster Safety Mock & URL Cleaner in [custom-jquery.js](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/public/front_end/classic/js/custom/custom-jquery.js):**
     ```javascript
     if (typeof window.iziToast === 'undefined') {
         window.iziToast = {
             success: function (opt) { console.log('success fallback:', opt); },
             error: function (opt) { console.warn('error fallback:', opt); }
         };
     }
     (function () {
         try {
             var url = new URL(window.location.href);
             if (url.searchParams.has('refresh')) {
                 url.searchParams.delete('refresh');
                 window.history.replaceState({}, document.title, url.pathname + url.search);
             }
         } catch (e) { }
     })();
     ```
  3. **Forced Reloads:**
     ```javascript
     var refreshUrl = new URL(window.location.href);
     refreshUrl.searchParams.set('refresh', Date.now().toString());
     window.location.href = refreshUrl.toString();
     ```

* **The Results:**
  1. Language selections sync instantaneously on the first reload request with zero delay or double-reload loops.
  2. Eliminated client-side Javascript ReferenceError exceptions entirely.
  3. Preserved all server database query caching speeds within individual requests while keeping distinct requests fully isolated.

---

## 2026-07-14: AJAX Deferred Loading & Slider Pagination

### Feature:
Deferred AJAX loading for Navbar Category Dropdowns and homepage sliders (Most Read and Web Stories) with dynamic pagination.

### Files Modified:
- [AppServiceProvider.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Providers/AppServiceProvider.php)
- [HomeController.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Http/Controllers/HomeController.php)
- [web.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/routes/web.php)
- [header.blade.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/resources/views/front_end/classic/layout/header.blade.php)
- [index.blade.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/resources/views/front_end/classic/pages/index.blade.php)
- [main.blade.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/resources/views/front_end/classic/layout/main.blade.php)
- [custom-jquery.js](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/public/front_end/classic/js/custom/custom-jquery.js)

### Files Created:
- [topic_dropdown_posts.blade.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/resources/views/front_end/classic/pages/partials/topic_dropdown_posts.blade.php)
- [most_read_slides.blade.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/resources/views/front_end/classic/pages/partials/most_read_slides.blade.php)
- [web_story_slides.blade.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/resources/views/front_end/classic/pages/partials/web_story_slides.blade.php)

### Logic Changes:
1. **Navbar Dropdown**: Removed `$topic->posts` relationship static querying from View Composer inside `AppServiceProvider.php`. Registered dynamic route and controller action to fetch top 5 category posts only when the category dropdown triggers open. Implemented request cancellation via `.abort()` on dropdown close to prevent redundant queries.
   - *Bugfix*: Because UIkit custom events (`beforeshow`, `hide`) do not bubble, delegated jQuery events bound to `document` were not firing. Refactored to bind native JavaScript event listeners directly to each `.topic-dropdown` element.
2. **Carousel Sliders**: Reduced homepage initial database query limits of Most Read posts and Web Stories to only retrieve the first 7 items. Implemented a reusable, generic JS pagination function (`window.initLazySliderLoad`) to dynamically append additional items in chunks of 7 when users interact with the slider.
3. **Image Lazy Loading**: Refactored IntersectionObserver in `main.blade.php` to run globally via `window.lazyLoadElements()`, enabling dynamic image lazy-loading on AJAX-injected slides.
4. **Top Posts AJAX Slider**: Extended the generic pagination framework to the top-of-homepage Top Posts swiper slider. Limited the initial query to 7 items, created a new slide partial (`top_posts_slides.blade.php`), and registered route `/top-posts-remaining` in `web.php` to load subsequent Top Posts chunks dynamically using offset queries.
5. **Followed Channels AJAX Slider**: Implemented AJAX dynamic loading for the "From the Channels You May Followed" slider. Reduced the initial cache query limit to 7 items, created a slide partial (`followed_channels_slides.blade.php`), registered route `/followed-channels-remaining` in `web.php`, and instantiated the loader in `custom-jquery.js`.
6. **JSON Dynamic Response Migration**: Migrated all 5 AJAX endpoints to return structured JSON arrays directly (eager loading nested relations like `channel` and `story_slides`) instead of pre-rendered HTML chunks. Built corresponding client-side template builders (`buildTopicDropdownPostHtml`, `buildMostReadSlideHtml`, `buildStorySlideHtml`, `buildTopPostSlideHtml`, `buildFollowedChannelSlideHtml`) inside `custom-jquery.js` to process and construct correct HTML markup dynamically on the client side.
7. **Topic Dropdown Loader Centering**: Fixed an issue where the topic dropdown loading spinner was squished and aligned on the far left. Relocated the `.dropdown-loader` markup outside the `row child-cols` wrapper in `header.blade.php` to prevent it from being styled as a grid column, allowing the loader to align centered at 100% width. Also changed the spinner icon to use the bootstrap hourglass split icon (`bi-hourglass-split`) and loading label to match the look of the channels dropdown loading design. Added DOM cleanup to remove the loader element in `custom-jquery.js` on successful AJAX response.
### [2026-07-15] Homepage Feed De-duplication & Viewport-Based Shuffling

* **Feature**: Prevent duplicate articles across the homepage sections (Top horizontal swiper, Banner Carousel, Top Posts sidebar, Followed Channels swiper, and Latest News list) and dynamically hide sections if the database has limited content.
* **Files Modified**:
  * [app/Http/Controllers/HomeController.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Http/Controllers/HomeController.php)
  * [resources/views/front_end/classic/pages/index.blade.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/resources/views/front_end/classic/pages/index.blade.php)
  * [public/front_end/classic/js/custom/custom-jquery.js](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/public/front_end/classic/js/custom/custom-jquery.js)
  * [resources/views/front_end/classic/pages/partials/followed_channels_slides.blade.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/resources/views/front_end/classic/pages/partials/followed_channels_slides.blade.php)
  * [resources/views/front_end/classic/pages/partials/most_read_slides.blade.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/resources/views/front_end/classic/pages/partials/most_read_slides.blade.php)
  * [resources/views/front_end/classic/pages/partials/top_posts_slides.blade.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/resources/views/front_end/classic/pages/partials/top_posts_slides.blade.php)
  * [resources/views/front_end/classic/pages/partials/web_story_slides.blade.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/resources/views/front_end/classic/pages/partials/web_story_slides.blade.php)
* **Logic Changes**:
  1. **Consolidated Global Query**: Replaced separate database queries for Top Posts, Banners, and Latest News with a single consolidated query fetching the top 32 latest posts. This reduces database connection/query overhead.
  2. **In-Memory Shuffling**: Shuffled the consolidated query in PHP memory using Laravel's `$collection->shuffle()` before slicing and distributing them to different view variables (`$top_posts`, `$postBannersRaw`, `$sidebarPosts`, `$latesNews`). This keeps only recent content on the screen, randomizes the layout dynamically on every refresh, and guarantees 100% unique posts on initial load.
  3. **Ad Injection Refactoring**: Refactored the ad-injection logic from `getPostsWithBanners()` into a clean private helper method `injectAdsIntoBanners($posts)` to preserve all random ad placement behavior and improve reusability.
  4. **Dynamic Section Hiding**: Wrapped the Top Section, Banner Section, Sidebar List, Followed Channels Section, and Latest News Section inside Blade checks (`@if (isset(...) && ...->isNotEmpty())`). If the database has fewer posts than needed, sections hide cleanly from the DOM without displaying empty titles or broken carousels.
  5. **Personalized Exclusions**: Passed the global post IDs displayed on initial load to the Followed Channels cache remember query context to exclude them via `whereNotIn('posts.id', $displayedGlobalIds)`, ensuring personalized feeds also remain unique.
  6. **Top Posts Slider Restoration**: Reverted the Top horizontal posts count slice back to exactly 4 items (as requested by viewport specs). Replaced `disable-class: d-none;` with `disable-class: disabled;` in the Swiper HTML options, and added an global CSS override `pointer-events: auto !important; opacity: 1 !important;` to ensure the slide next buttons remain visible and clickable even when the DOM slides count equals the visible viewport width. This allows users to trigger AJAX loading on click.
  7. **Followed Channels Slider Restoration**: Reverted the initial page load limit of followed channels back to exactly 5 posts (indices 17 to 21). Changed the Swiper config to use `disable-class: disabled;` and applied CSS overrides so that the Next navigation arrow is not hidden with `d-none`. The arrow remains visible and clickable, enabling users to click it to trigger the AJAX pagination request to load followed channel posts dynamically, even if the initial load count is equal to the desktop viewport width of 5.
  8. **Title-Based & Pagination Boundary De-duplication**: Integrated `unique('title')` collection filtering for the homepage consolidated query feed, initial followed channels query, and all AJAX pagination queries. Added `data-post-id` attributes to swiper slides across layout files and partials, and modified `custom-jquery.js` to dynamically collect all currently displayed slide IDs on the client side and pass them inside the AJAX `displayed_ids` array. Updated all four AJAX controller methods inside `HomeController.php` to exclude these displayed IDs via `whereNotIn` instead of database offset skips, completely preventing duplicates near swiper pagination boundary shifts (caused by duplicate RSS imports or client/server unique-filtering discrepancies).

### [2026-07-16] Search Refactoring & AJAX Filters (Including Audio, Split Videos/YouTube)

* **Feature**: Redirect search modal submission to a dedicated search page with advanced filters (Articles, Videos, YouTube, Audios, Web Stories, Newspapers, Magazines), active vs. user-followed channels and topics, popularity/date sorting, and dynamic JS AJAX pagination.
* **Files Modified**:
  * [public/front_end/classic/js/custom/search-news.js](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/public/front_end/classic/js/custom/search-news.js)
  * [resources/views/front_end/classic/layout/header.blade.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/resources/views/front_end/classic/layout/header.blade.php)
  * [app/Http/Controllers/SearchPostController.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Http/Controllers/SearchPostController.php)
  * [resources/views/front_end/classic/pages/search-result.blade.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/resources/views/front_end/classic/pages/search-result.blade.php)
* **Files Created**:
  * [search_result_posts.blade.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/resources/views/front_end/classic/pages/partials/search_result_posts.blade.php)
* **Logic Changes**:
  1. **Modal Form Submission Redirect**: Modified the search form submit event listener in `search-news.js` to submit naturally to `/posts` rather than intercepting it with an in-modal AJAX search.
  2. **Autocomplete Redirect**: Updated `performSearch()` in `search-news.js` to perform a full page redirect to `/posts?search=query` when clicking a suggestion keyword or recent search item. Removed the unused `onsubmit` handler on the header search form.
  3. **Multi-Table Query Union**: Refactored the `search` method in `SearchPostController.php` to run conditional, scoped subqueries on the `posts` (Articles, Videos, YouTube, Audios), `stories` (Web Stories), and `e_newspapers` (Newspapers, Magazines) tables based on selected type filters, merging results using a `unionAll` query wrapped in `DB::table()`.
  4. **AJAX Response Format**: Enabled `SearchPostController@search` to detect AJAX requests and return a JSON payload containing the pre-rendered HTML of the results cards partial.
  5. **Client-Side AJAX pagination**: Added event listeners in `search-news.js` to intercept pagination clicks, update the browser URL bar via `pushState()`, fetch the matching page via AJAX, and smoothly scroll the page view back to the top of the `#content-area`.
  6. **Interactive Filters**: Added listeners to form inputs and checkboxes to automatically sync selections between the mobile offcanvas drawer and desktop sidebar, applying active filters instantly via AJAX.
  7. **Split Videos & YouTube Checkboxes**: Divided the video format filter into distinct "Videos" (`posts.type = 'video'`) and "YouTube" (`posts.type = 'youtube'`) checkboxes in both desktop and mobile layouts. Added Audio format support.
  8. **Dynamic Search Count Header**: Removed static centered page headers and moved search query summary text inside the AJAX-loaded posts grid partial. Designed it as left-aligned with a smaller, premium font format, updating instantly on filter actions.
  9. **Interactive Channel 'All' Toggle**: Refactored the "All" channel option into a dynamic, mutually exclusive checkbox that handles selection states and synchronizes across viewports.
  10. **Swiper Navigation Arrow States Fix**: Updated CSS overrides in `index.blade.php` to target next arrows only, and added a custom `.swiper-nav-hidden` class. Implemented dynamic arrow visibility inside `custom-jquery.js` to hide the previous (`<`) arrow at slide index 0 and hide the next (`>`) arrow when all database records are exhausted. This custom class prevents UIKit/Swiper from overriding the visibility rules on page load (specifically fixing the Web Stories previous arrow visibility).

### [2026-07-16] Page Speed & Core Web Vitals Optimization

* **Feature**: Optimized home page PageSpeed score (focusing on LCP and CLS) by lazy-loading flag modals, adding explicit dimensions to channel logos, preconnecting to openweathermap, and hiding non-initialized Swiper slides.
* **Files Modified**:
  * [app/Http/Controllers/AdminControllers/LanguageController.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Http/Controllers/AdminControllers/LanguageController.php)
  * [resources/views/front_end/classic/layout/main.blade.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/resources/views/front_end/classic/layout/main.blade.php)
  * [resources/views/front_end/classic/layout/header.blade.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/resources/views/front_end/classic/layout/header.blade.php)
  * [resources/views/front_end/classic/pages/index.blade.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/resources/views/front_end/classic/pages/index.blade.php)
  * [resources/views/front_end/classic/pages/partials/top_posts_slides.blade.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/resources/views/front_end/classic/pages/partials/top_posts_slides.blade.php)
  * [resources/views/front_end/classic/pages/partials/search_result_posts.blade.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/resources/views/front_end/classic/pages/partials/search_result_posts.blade.php)
  * [public/front_end/classic/css/custom.css](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/public/front_end/classic/css/custom.css)
* **Logic Changes**:
  1. **New Flag Image Resizing & WebP Compression**: Modified the `store_language` method in `LanguageController.php` to call `FileService::resizeAndCompressUpload` with 150px max width and WebP output format. Ensures new flags are highly optimized.
  2. **Deferred Modal Flag Loading**: Converted web language modal flag images inside `header.blade.php` to use the `lazy-img` class and a base64 spacer. Excluded modal flags from the page's global lazy load `IntersectionObserver` in `main.blade.php` to prevent premature load trigger on page initialize. Instead, added dedicated event listeners in `main.blade.php` to swap the images' sources on modal `beforeshow` events.
  3. **Above-the-Fold LCP Prioritization**: Removed `loading="lazy"` from the first 4 slides of the Top Posts swiper in `index.blade.php`, and added `fetchpriority="high"` to the very first slide's image. This allows the browser to request the visible above-the-fold banner assets immediately, decreasing LCP resource delay.
  4. **Swiper Pre-Initialization Anti-Shift CSS**: Injected a CSS override ruleset in `index.blade.php` to hide all slides except the first one in uninitialized Swiper containers (`.swiper:not(.swiper-initialized)`). This prevents carousels from stacking slides vertically and causing height collapse shifts when JavaScript executes.
  5. **Explicit Dimensions for Channel Logos**: Added explicit `width="20" height="20"` attributes to all channel logo elements in `index.blade.php`, `top_posts_slides.blade.php`, and `search_result_posts.blade.php` to avoid browser dimension calculation changes on download.
  6. **Third-Party Preconnection**: Added a `<link rel="preconnect" href="https://api.openweathermap.org">` tag in `main.blade.php`'s head to establish early handshakes for the weather data API.
  7. **Universal JavaScript Lazy Load Filter**: Updated `main.blade.php` to query all `lazy-img` tags and filter out language modal images dynamically using `.closest()` rather than complex CSS Selectors Level 4 `:not()` exclusions. This fixes flag images loading eagerly in environments (like headless crawler browsers) that do not support Level 4 selectors.
  8. **Weather Icon Dimensions**: Added explicit `width="100" height="100"` attributes and inline styling to `img#weather-icon` inside `index.blade.php` to prevent it from shifting the page layout when the weather API completes.
  9. **E-Newspaper Aspect-Ratio CLS Resolution**: Removed conflicting `height: 300px;` styling from the `.epaper_css` class inside `custom.css`, allowing UIKit's aspect ratio containers (`ratio-1x1`, `ratio-16x9`) to size the E-Newspaper block consistently on mobile and desktop viewports.

### [2026-07-17] Post Detail Page & Global View Composer Optimization

* **Feature**: Optimized the performance of the single Post Detail page and globally cached the shared View Composer variables (categories, channels, settings, sidebars) to reduce database hits across all front-end page requests.
* **Files Modified**:
  * [app/Providers/AppServiceProvider.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Providers/AppServiceProvider.php)
  * [app/Http/Controllers/PostDetailController.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Http/Controllers/PostDetailController.php)
* **Logic Changes**:
  1. **Global View Composer Caching**: Wrapped the wildcard view composer dataset (topics list, channel lists, channel top posts, settings, custom configurations) inside `AppServiceProvider.php` in a `Cache::remember` block for 10 minutes. The cache key is scoped dynamically by active Web Language and Subscribed News Languages. This cuts down database query overhead globally on every single frontend page request by 12 to 15 queries.
  2. **getRecentPosts Signature Update**: Optimized `getRecentPosts` inside `AppServiceProvider.php` to accept `$subscribedLanguageIds` directly, preventing it from executing redundant queries on user subscription statuses.
  3. **Reaction Seeding Check Bypass**: Replaced the expensive `Reaction::count() === 0` check inside `PostDetailController.php` index method with a forever-cached value, avoiding hitting the reactions table count on every single post load.
  4. **System Settings Cache Usage**: Replaced all direct database queries on the `settings` table inside `PostDetailController.php` (such as plucking settings, getting `news_label_place_holder`, and getting `free_trial_post_limit`) with values fetched from `CachingService::getSystemSettings()`. This also resolved an existing bug where `$setting` was accessed without initialization.
  5. **Guest Favorites Bypass**: Configured the favorite status lookup inside `PostDetailController.php` to bypass the `favorites` table completely if the visitor is a guest (null or `'0'`), directly assigning `is_bookmark = 0`.
  6. **Reactions N+1 Query Fix**: Fetched all active reaction models once using `Reaction::all()` outside of the post reactions loop inside `PostDetailController.php`. Used collection filters (`firstWhere`) to map UUIDs in memory, eliminating multiple sequential queries inside the loop.
  7. **Next/Previous Selective Columns**: Modified next and previous post queries to retrieve only the required fields (`id`, `title`, `slug`, `image`, `video_thumb`, `type`) instead of executing `select *`, saving database payload and model hydration memory.
  8. **Guest PostView Query Bypass**: In the `viewCount` method of `PostDetailController.php`, skipped the `PostView` database select query entirely for guest visitors, since views are only tracked via cookies for guests and are never written to the `post_views` table.
  9. **Language & Status Caching**: Cached static language lists (`Language::all()`, `NewsLanguage::all()`) and the active default language in memory to avoid redundant DB hits when the View Composer cache is accessed. Cached the active language status check inside `NewsLanguageStatus::getCurrentStatus()` to prevent redundant lookups.
  10. **E-Newspaper Height Fix**: Restored `height: 300px` inside `.epaper_css` in `custom.css` and applied inline `style="height: 300px;"` to the container and image in `index.blade.php` to match the original layout design exactly while keeping Cumulative Layout Shift (CLS) at 0.
  11. **Language Cache Invalidation**: Added Eloquent `saved` and `deleted` event listeners for `Language` and `NewsLanguage` models in `AppServiceProvider.php` to clear their respective cache keys (`all_languages_list`, `all_news_languages_list`, and `news_language_default_active`) when modified by an administrator.
  12. **View Composer Cache Buster**: Introduced a global `view_composer_cache_buster` cache key and appended it to the View Composer cache key. Configured listeners on `Language`, `NewsLanguage`, `Setting`, and `NewsLanguageStatus` to increment this buster on any modifications. This ensures that language toggles and setting changes in the admin panel invalidate the cached view variables instantly for all users.
  13. **html_entity_decode Null Warnings Resolution**: Safeguarded calls to `html_entity_decode()` in `index.blade.php`, `BookmarkController.php`, and `FetchRssFeedController.php` with a null-coalescing default empty string (`?? ''`). This prevents PHP 8.1+ deprecation warnings from firing when post descriptions are null.
  14. **Additional Post Page Query Reductions**:
      - Cached `Reaction::all()` definitions forever inside `PostDetailController.php`, saving 1 SQL statement.
      - Cached subscriber news language IDs in Symfony request attributes shared between `PostDetailController.php` and `AppServiceProvider.php`, eliminating duplicate DB queries.
      - Removed the redundant categories/topics list query from `PostDetailController.php`, inheriting the globally shared View Composer `$topics` variable instead.

### [2026-07-17] Agent Guidelines Documentation Setup

* **Feature**: Set up developer agent guidelines and coding patterns mapping for the NewsHunt Laravel project.
* **Files Created/Modified**:
  * [.agents/agent_instruction.md](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/.agents/agent_instruction.md) (Created and expanded to cover end-to-end codebase systems, tech stack specifications, frontend assets, and data flows)
* **Logic Changes**:
  * Codified the architectural standards, caching strategies, Eloquent optimization guidelines, frontend Core Web Vitals practices, and WebP compression guidelines into a unified instructions markdown file for AI coding subagents working on this workspace to prevent breaking existing systems.
  * Expanded the guidelines to include database model definitions, subscription limit and increment validations, homepage feed consolidation and shuffling, payment gateway checkout routines, RSS ingestion automation, web stories cookie-based tracking, and custom advertiser ad operations.
  * Added detailed technical stack breakdowns (Laravel, Sanctum, Spatie, jQuery, UIKit, SwiperJS), frontend script mappings (custom-jquery.js, search-news.js), CSS layout guidelines (CLS heights, Swiper anti-shift templates), and visual sequence data flow diagrams.

### [2026-07-20] Enterprise Architecture Knowledge Base Skill & File Changes Log Setup

* **Feature**: Built a 13-part architecture knowledge base skill inside `.agents/skills/newshunt-architecture/` and created the project master index tracker `FILE_CHANGES_LOG.md`.
* **Files Created/Modified**:
  * [FILE_CHANGES_LOG.md](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/FILE_CHANGES_LOG.md) (Created master file changes index log and running tally table)
  * [.agents/skills/newshunt-architecture/SKILL.md](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/.agents/skills/newshunt-architecture/SKILL.md) (Created master skill entry file)
  * [.agents/skills/newshunt-architecture/references/01_PROJECT_OVERVIEW.md](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/.agents/skills/newshunt-architecture/references/01_PROJECT_OVERVIEW.md)
  * [.agents/skills/newshunt-architecture/references/02_TECH_STACK_AND_DEPENDENCIES.md](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/.agents/skills/newshunt-architecture/references/02_TECH_STACK_AND_DEPENDENCIES.md)
  * [.agents/skills/newshunt-architecture/references/03_FOLDER_STRUCTURE_AND_CONVENTIONS.md](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/.agents/skills/newshunt-architecture/references/03_FOLDER_STRUCTURE_AND_CONVENTIONS.md)
  * [.agents/skills/newshunt-architecture/references/04_DATABASE_SCHEMA_AND_MIGRATIONS.md](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/.agents/skills/newshunt-architecture/references/04_DATABASE_SCHEMA_AND_MIGRATIONS.md)
  * [.agents/skills/newshunt-architecture/references/05_MODELS_AND_RELATIONS.md](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/.agents/skills/newshunt-architecture/references/05_MODELS_AND_RELATIONS.md)
  * [.agents/skills/newshunt-architecture/references/06_ROUTES_MIDDLEWARE_AND_APIS.md](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/.agents/skills/newshunt-architecture/references/06_ROUTES_MIDDLEWARE_AND_APIS.md)
  * [.agents/skills/newshunt-architecture/references/07_CONTROLLERS_AND_CALL_CHAINS.md](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/.agents/skills/newshunt-architecture/references/07_CONTROLLERS_AND_CALL_CHAINS.md)
  * [.agents/skills/newshunt-architecture/references/08_BUSINESS_RULES_AND_PAYWALLS.md](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/.agents/skills/newshunt-architecture/references/08_BUSINESS_RULES_AND_PAYWALLS.md)
  * [.agents/skills/newshunt-architecture/references/09_FRONTEND_ASSETS_AND_BLADE.md](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/.agents/skills/newshunt-architecture/references/09_FRONTEND_ASSETS_AND_BLADE.md)
  * [.agents/skills/newshunt-architecture/references/10_EVENTS_QUEUES_AND_SCHEDULES.md](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/.agents/skills/newshunt-architecture/references/10_EVENTS_QUEUES_AND_SCHEDULES.md)
  * [.agents/skills/newshunt-architecture/references/11_AUTHENTICATION_AND_AUTHORIZATION.md](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/.agents/skills/newshunt-architecture/references/11_AUTHENTICATION_AND_AUTHORIZATION.md)
  * [.agents/skills/newshunt-architecture/references/12_CHANGE_IMPACT_AND_KNOWN_DEBT.md](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/.agents/skills/newshunt-architecture/references/12_CHANGE_IMPACT_AND_KNOWN_DEBT.md)
  * [.agents/skills/newshunt-architecture/references/13_RESPONSIVENESS_CROSS_BROWSER_AND_MOBILE_API_SAFETY.md](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/.agents/skills/newshunt-architecture/references/13_RESPONSIVENESS_CROSS_BROWSER_AND_MOBILE_API_SAFETY.md)
  * [.agents/agent_instruction.md](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/.agents/agent_instruction.md) (Updated master pointer table)
* **Logic Changes**:
  * Codified complete 13-document reference architecture covering CodeCanyon domain context, full tech stack dependencies matrix, folder structures, all 116 database migrations, 60+ Eloquent models, full route tables (`web.php` and `api.php`), controller execution call chains, paywall limit verification algorithms, frontend JS/CSS engines, scheduled console tasks, authentication guards, change safety rules, cross-device responsiveness practices, and mobile REST API backward compatibility guarantees.

### [2026-07-20] Phase 1.1: Topic & Category Pages Query & Model Performance Optimization

* **Feature**: Optimized database query statements and Eloquent model hydration overhead on Topic Directory (`/topics`) and Topic News Feed (`/topics/{slug}`) pages.
* **Files Modified**:
  * [TopicFrontController.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Http/Controllers/TopicFrontController.php)
  * [CategoryController.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Http/Controllers/CategoryController.php)
  * [helper.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Helpers/helper.php)
* **Logic Changes**:
  * Reused subscriber language IDs cached in `request()->attributes` to eliminate duplicate database lookup queries across Controllers and `AppServiceProvider` View Composer.
  * Replaced `Setting::get()` and `Setting::where()` in `CategoryController.php` with `request()->attributes` cached settings arrays, eliminating **146 Setting Eloquent model hydrations** per request.
  * Added selective column selection (`select(...)`) to `Topic::select(...)` and `Post::select(...)` queries, excluding heavy HTML description blobs from topic list feeds.
  * Added `request()->attributes` theme slug caching to `getTheme()` in `helper.php` to eliminate repeated default theme queries per request across all pages.
* **Verification Results**:
  * `/topics`: Queries reduced from `10 Statements` to `9 Statements` (0 duplicates).
  * `/topics/world`: Queries reduced from `12 Statements` to `9 Statements` (25% query reduction, 0 duplicates); Eloquent hydrated models dropped from `163 Models` to `17 Models` (~90% memory reduction).

### [2026-07-21] Channels Directory & Single Channel Profile Performance Optimization

* **Feature**: Optimized database query execution, Eloquent model hydrations, and duplicate queries for Channels Directory (`/channels`) and Single Channel Profile (`/channels/{slug}`) pages.
* **Files Modified**:
  * [ChannelFrontController.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Http/Controllers/ChannelFrontController.php)
* **Logic Changes**:
  * Replaced `Setting::where('name', 'default_image')->first()` with `$request->attributes` cached settings (`$settingsCache->get('default_image')->value ?? null`), eliminating 1 SQL query and 1 `Setting` Eloquent model hydration per request.
  * Shared `$subscribedLanguageIds` via `$request->attributes->set('subscribed_language_ids', ...)`, eliminating duplicate `news_languages_subscribers` queries in `AppServiceProvider`.
  * Replaced the standalone `$post_count = Post::where(...)->count()` query on `/channels/{slug}` with `$post_count = $getChannelPosts->total()`, removing 1 database query.
  * Wrapped `withCount(['subscribers as is_followed' => ...])` on `/channels` in a `$user ? ... : ...` check to avoid executing unnecessary subqueries (`where user_id = ''`) for unauthenticated visitors.
  * Integrated `subscribers as is_followed` `withCount` subquery directly into `$channelData` query on `/channels/{slug}`, eliminating the extra `ChannelSubscriber::where('channel_id', ...)` SQL query and model hydration.
  * Added explicit column selection (`Channel::select(...)`) and added `'channels.slug as channel_slug'` to `Post::select(...)`.
### [2026-07-21] Web Stories Performance & Query Optimization

* **Feature**: Optimized database query execution, Eloquent model hydrations, and duplicate queries for Web Stories Directory (`/webstories`), Single Web Story Reader (`/webstories/{topic}/{story}`), and Web Stories by Topic (`/webstories/{topic}`) pages.
* **Files Modified**:
  * [WebStory.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Http/Controllers/WebStory.php)
* **Logic Changes**:
  * Replaced `Setting::pluck('value', 'name')` and `Setting::where('name', 'free_trial_story_limit')` with `$request->attributes` cached settings (`$settingsCache`), completely eliminating **146 Setting Eloquent model hydrations** per story reader hit.
  * Shared `$subscribedLanguageIds` via `$request->attributes->set('subscribed_language_ids', ...)`, eliminating duplicate `news_languages_subscribers` queries in `AppServiceProvider`.
  * Computed `$filteredTopics` on `/webstories` directly in memory from the eagerly-loaded `$stories` collection (`$stories->pluck('topic')->filter()->unique('id')->values()`), eliminating 1 SQL query.
  * Removed unused `Topic::all()` query from `storyByTopic()`, eliminating 1 SQL query and **39 unused `Topic` model hydrations**.
  * Added selective column projections (`Story::select(...)`, `Topic::select(...)`) across all methods.
### [2026-07-22] E-Newspaper & PDF Viewer Performance & Query Optimization

* **Feature**: Optimized database query execution, Eloquent model hydrations, and duplicate queries for E-Newspaper Page (`/e-newspaper`), E-Magazine Page (`/e-magazine`), and PDF Viewer Page (`/e-newspaper/{id}/pdf`).
* **Files Modified**:
  * [ENewspaperFrontController.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Http/Controllers/ENewspaperFrontController.php)
* **Logic Changes**:
  * Replaced `Setting::pluck('value', 'name')` and individual `Setting::where(...)` queries with `$request->attributes` cached settings (`$settingsCache`), completely eliminating **148 Setting Eloquent model hydrations** per page load.
  * Shared `$subscribedLanguageIds` via `$request->attributes->set('subscribed_language_ids', ...)`, eliminating duplicate `news_languages_subscribers` queries in `AppServiceProvider`.
  * Replaced full table scan query `$allEpapers = ENewspaper::with(['channel', 'topic'])->get();` (which fetched all newspaper records to build filter dropdowns) with lightweight `exists` subqueries on `Channel` and `Topic` models.
  * Ensured wildcard relationship fields are queried to maintain robust schema compatibility (avoiding missing/unmigrated column errors like `language_code`).
* **Verification Results**:
  * `/e-newspaper`: Queries reduced from `21 Statements` (6 duplicates) down to `10 Statements` (0 duplicates); Models reduced from `169 Models` down to `15 Models` (0 Setting models).
  * `/e-newspaper/{id}/pdf`: Queries reduced from `14 Statements` (1 duplicate) down to `5 Statements` (0 duplicates); Models reduced from `9 Models` down to `3 Models` (0 Setting models).

### [2026-07-22] Videos & Audios Performance & Query Optimization

* **Feature**: Optimized database query execution, Eloquent model hydrations, and duplicate queries for Videos Page (`/videos`) and Audios Page (`/audios`).
* **Files Modified**:
  * [VideoController.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Http/Controllers/VideoController.php)
  * [AudioController.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Http/Controllers/AudioController.php)
* **Logic Changes**:
  * Integrated request attributes settings cache, eliminating the `select name, value, updated_at from settings` query inside `AppServiceProvider`.
  * Cached subscriber news language IDs in request attributes, preventing duplicate language lookup queries.
  * Deleted completely unused `$topicIds` pluck query in `VideoController`.
  * Replaced plucking table scan on `Post` in `AudioController` with a clean `whereHas('posts', ...)` query on `Topic`.
  * Added selective column projections for posts, topics, and channels.
* **Verification Results**:
  * `/videos`: Queries reduced from `7 Statements` down to `5 Statements` (0 duplicates); Models reduced from `13 Models` down to `10 Models` (0 Setting models).
  * `/audios`: Queries reduced from `8 Statements` down to `7 Statements` (0 duplicates); Models reduced from `4 Models` down to `3 Models` (0 Setting models).











