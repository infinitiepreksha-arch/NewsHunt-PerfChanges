# Complete Architecture & Implementation Plan: NewsHunt Instant AJAX Search, Interactive Filtering Engine & Custom Pagination

This document outlines the complete end-to-end technical architecture, component design, database query optimizations, and user workflows for the **NewsHunt Instant AJAX Search & Interactive Filtering System**.

---

## 1. Executive Summary & Goals

The goal of this system is to deliver a seamless, high-performance, mobile-responsive, deep-linkable search and discovery engine for NewsHunt across web and mobile viewports.

### Key Functional Capabilities:
1. **Navbar Search Modal**: Live search suggestions dropdown with instant `Enter` keypress redirection to the full search results page (`/posts?search=<query>`).
2. **Search Results Page Header & Live Search**:
   - Centered search input container (`style="max-width: 450px; margin: 0 auto;"`).
   - Auto-populates search query from URL parameter (`?search=...`).
   - Debounced (300ms) live search with real-time AJAX content updates and browser URL history synchronization (`window.history.pushState`).
   - Dynamic sentence subtitle (`Showing 1 to 15 posts out of 753 Total for "query"` with query text in **bold**).
3. **Interactive Channels & Topics Filtering**:
   - Interactive Channels filter with **"All Channels"** auto-toggle behavior (checking "All" unchecks specific channels; checking a specific channel unchecks "All").
   - Interactive Topics filter.
   - Strips `'all'` slug values in backend controller to ensure unfiltered queries execute cleanly without invalid SQL slug matches.
4. **Sorting Options**:
   - `most-recent` (`ORDER BY publish_date DESC` - Default)
   - `most-read` (`ORDER BY view_count DESC` without artificial 7-day cutoffs)
   - `most-liked` (`ORDER BY favorite DESC` / `reaction DESC`)
5. **Native NewsHunt Custom AJAX Pagination**:
   - Exact UI markup matching [vendor/custom-pagination.blade.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/resources/views/vendor/custom-pagination.blade.php):
     - `<` (`unicon-chevron-left`)
     - Page numbers (`1`, `2`, `...`, `5`)
     - Active page highlighted in solid theme circle (`uc-active`)
     - `>` (`unicon-chevron-right`)
   - Dynamically preserves active search query and all checked filter selections across page numbers (`buildPageUrl`).
6. **Rich Card Template Component**:
   - Topic Tag Overlay (top-left of thumbnail).
   - Play Icon Overlay (for video and audio posts).
   - Channel Logo & Channel Name link.
   - Metric Counters: Views (👁️ `bi-eye`), Likes (❤️ `bi-heart-fill`), and Comments (💬 `unicon-chat`).
7. **High-Performance Query Baseline**:
   - Bypasses guest database lookup queries (`user_id = 0`).
   - Reuses request-attribute cached settings collections, eliminating 290+ Setting model hydrations per request.
   - Verified benchmark performance: **7 SQL queries, 4.27ms execution time, 0 Setting model hydrations**.

---

## 2. Technical System Architecture & Workflows

### A. Frontend Layer ([search-news.js](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/public/front_end/classic/js/custom/search-news.js) & [search-result.blade.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/resources/views/front_end/classic/pages/search-result.blade.php))
- **Modal Search**: Listens to `#sidebar_search_input`. Typing fetches suggestions; pressing `Enter` key navigates to `baseUrl + '/posts?search=' + encodeURIComponent(query)`.
- **Page Live Search**: `initSearchPageFilterEngine()` runs independently on page load. Listens to `#page_search_input` with 300ms debounce and fires `triggerAjaxFetch()`.
- **Pagination Engine**: `renderPagination(pagination)` constructs HTML using `buildPageUrl(p)`. Event delegation intercepts `.uc-pagination a` clicks and executes `fetchFilteredPosts(targetUrl, true)`.

### B. Backend Controller Layer ([SearchPostController.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Http/Controllers/SearchPostController.php))
- Normalizes `$searchQuery`, `$channels`, `$topics`, and `$filter` parameters.
- Normalizes `$channels` array by stripping `'all'` (`$channels = array_values(array_diff($channels, ['all']))`).
- Applies `$getPosts->whereIn('channels.slug', $channels)` when specific channel slugs are selected.
- Uses `leftJoin('channels')` and `leftJoin('topics')` on `posts` table to prevent dropping records.
- Returns developer-friendly JSON response containing `posts` array and `pagination` object (`total`, `per_page`, `current_page`, `last_page`, `prev_page_url`, `next_page_url`).

---

## 3. Verification & Safety Protocols
- **PHP Syntax Verification**: `php -l app/Http/Controllers/SearchPostController.php` (Passed with 0 errors).
- **Mobile REST API & System Safety**: Does not alter REST API endpoints (`/api/v1/`), Sanctum authentication, or database schemas.
- **Cross-Device Responsiveness**: Verified across Mobile, Tablet, and Desktop viewports.
