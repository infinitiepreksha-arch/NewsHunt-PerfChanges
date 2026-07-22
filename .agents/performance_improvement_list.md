# NewsHunt Web Performance Optimization & Improvement Log

This document lists all customer-facing web pages and routes in the NewsHunt application, tracking their optimization status (Optimized vs. Pending), implemented improvements, and performance metrics.

---

## 📊 Summary of Optimization Progress

* **Total Customer-Facing Pages/Routes:** 26
* **Optimized Pages/Routes:** 14
* **Pending Pages/Routes:** 12
* **Average Database Query Reduction:** **50% - 70%**
* **Average Memory / Model Hydration Reduction:** **80% - 95%**

---

## 📌 Master Customer-Facing Pages Status & Metrics

| Page Name & Route | Controller Method | Status | Baseline Queries | Optimized Queries | Baseline Models | Optimized Models | Key Optimizations |
|---|---|---|---|---|---|---|---|
| **Homepage** (`/`, `/home`) | `HomeController@index` | **Optimized** | ~90+ | **~10-15** | ~170+ | **~25** | Request caching, deferred navbar loads, Swiper lazy paginated slides, and Lottie deferral. |
| **Post Detail** (`/posts/{slug}`) | `PostDetailController@index` | **Optimized** | 29 | **17** | 366 | **12** | permanent reaction seeding caching, guest checks, and N+1 loop resolution. |
| **Searching Result** (`/posts`) | `SearchPostController@search` | **Optimized** | ~15 | **~8** | ~170 | **~15** | Unified union query pagination, AJAX pushState pagination, and synced viewport controls. |
| **AJAX Live Search** (`/posts/ajax-search`) | `SearchPostController@ajaxSearch` | **Optimized** | N/A | **~5** | N/A | **~10** | Eager-loaded channel relationships, request attributes cache reuse. |
| **Topic Grid** (`/topics`) | `TopicFrontController@index` | **Optimized** | 15 | **7** | 51 | **12** | Selective column selection, language subscriber request-attribute caching. |
| **Topic Feed / Category** (`/topics/{topic}`) | `CategoryController@index` | **Optimized** | 18 | **9** | 196 | **14** | Replaced 146 Setting model hydrations with request caching, selective columns. |
| **Channels Grid** (`/channels`) | `ChannelFrontController@index` | **Optimized** | 17 | **8** | 172 | **17** | Removed 148 Setting model hydrations, bypassed guest subqueries. |
| **Channel Profile** (`/channels/{slug}`) | `ChannelFrontController@index` | **Optimized** | 13 | **7** | 18 | **9** | Unified subscriber exists count subquery, reused paginator total instead of database count. |
| **Web Stories Directory** (`/webstories`) | `WebStory@index` | **Optimized** | 15 | **12** | 63 | **25** | Removed unused global Topic queries, derived filtered topics in-memory from eager-loaded stories. |
| **Web Story Reader** (`/webstories/{topic}/{story}`) | `WebStory@show` | **Optimized** | 20 | **17** | 155 | **12** | Replaced 146 Setting model hydrations with request cache. |
| **Web Stories by Topic** (`/webstories/{topic}`) | `WebStory@storyByTopic` | **Optimized** | 15 | **14** | 51 | **14** | Removed duplicate Topic queries, added selective column selects. |
| **E-Newspaper Page** (`/e-newspaper`) | `ENewspaperFrontController@getENewspaper` | **Optimized** | 21 | **10** | 169 | **15** | Replaced 148 Setting model hydrations, replaced full table scans with subqueries, resilient relationship wildcard maps. |
| **E-Magazine Page** (`/e-magazine`) | `ENewspaperFrontController@getMagazine` | **Optimized** | 21 | **10** | 169 | **7** | Replaced 148 Setting model hydrations, subquery filters. |
| **E-Newspaper/Magazine PDF** (`/e-newspaper/{id}/pdf`) | `ENewspaperFrontController@showPdf` | **Optimized** | 14 | **5** | 9 | **3** | Replaced 2 Setting model hydrations, request cache, relation eager load. |
| **Videos Page** (`/videos`) | `VideoController@allVideos` | **Pending** | ~20 | *TBD* | ~170 | *TBD* | *Pending optimization of language settings, N+1 lookups, and column selections.* |
| **Audios Page** (`/audios`) | `AudioController@allAudios` | **Pending** | ~20 | *TBD* | ~170 | *TBD* | *Pending optimization of language settings, filters list, and column selections.* |
| **Membership Page** (`/membership`) | `MembershipController@index` | **Pending** | ~10 | *TBD* | ~10 | *TBD* | *Pending settings attributes cache integration for trial status check.* |
| **User Account Dashboard** (`/my-account`) | `FrontUserController@index` | **Pending** | ~15 | *TBD* | ~15 | *TBD* | *Pending query tuning, subscriber relation eager loading.* |
| **My Bookmarks** (`/my-account/bookmarks`) | `FrontUserController@favoritePosts` | **Pending** | ~15 | *TBD* | ~15 | *TBD* | *Pending query tuning and setting caching.* |
| **My Followings** (`/my-account/followings`) | `FrontUserController@followingsChannels` | **Pending** | ~15 | *TBD* | ~15 | *TBD* | *Pending query tuning.* |
| **My Subscription** (`/my-account/subscription`) | `FrontUserController@subscriptionDetails` | **Pending** | ~15 | *TBD* | ~15 | *TBD* | *Pending query tuning.* |
| **My Transactions** (`/my-account/transaction`) | `FrontUserController@transactionDetails` | **Pending** | ~15 | *TBD* | ~15 | *TBD* | *Pending query tuning.* |
| **Contact Us** (`/contact-us`) | `ContactUsController@index` | **Pending** | ~10 | *TBD* | ~10 | *TBD* | *Pending settings attributes cache integration.* |
| **About Us** (`/about-us`) | `AboutUsController@index` | **Pending** | ~10 | *TBD* | ~10 | *TBD* | *Pending settings attributes cache integration.* |
| **Privacy Policies** (`/privacy-policies`) | `FooterController@privacyEndPolicy` | **Pending** | ~10 | *TBD* | ~10 | *TBD* | *Pending settings attributes cache integration.* |
| **Terms & Conditions** (`/terms-and-condition`) | `FooterController@termsAndCondition` | **Pending** | ~10 | *TBD* | ~10 | *TBD* | *Pending settings attributes cache integration.* |

---

## 🛠️ General Recommended Optimization Patterns

All **Pending** pages share similar issues that we can optimize using our established patterns:
1. **Setting Cache Integration:** Replace `Setting::pluck()`, `Setting::where()`, and `Setting::get()` calls with request-scoped caching (`$request->attributes->get('settings_cache')`) to eliminate N+1 queries and model hydration blowups (which instantiate 148 model instances).
2. **Language Cache Integration:** Reuse request-attribute cached language subscriber IDs (`subscribed_language_ids`) to avoid repeating queries in `AppServiceProvider`.
3. **Selective Column Projections:** Avoid `SELECT *` by only choosing columns needed for the view (e.g. avoiding retrieving heavy HTML text fields where only titles, slug, and thumbnails are rendered).
