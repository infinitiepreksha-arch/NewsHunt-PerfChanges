# NewsHunt Feature Development & Enhancements History

This report documents all new features, functional enhancements, UI additions, and non-performance architectural updates implemented in the **NewsHunt** application. Each entry follows a standardized format to provide full technical visibility for developers and maintainers.

---

## Standard Entry Format
Every feature entry in this file MUST follow this structure:
1. **Feature Need / Requirement**
2. **Solution & Architecture Rationale**
3. **Files Modified**
4. **Code Comparison (Diffs / Code Snippets)**
5. **Impact & Future Scalability (Benefits & Cautions)**

---

## 1. Redirect-Based Advanced Search & Multi-Format Filters

### Feature Need / Requirement
1. **User Experience:** The previous search modal performed in-page replacement without updating the browser URL bar or supporting deep-linking. Users pressing Enter inside the header search box could not bookmark search results or share search URLs.
2. **Multi-Format Content Filtering:** Users needed the ability to filter search results specifically across different content types (Articles, Custom Videos, YouTube, Audio Podcasts, Web Stories, and E-Newspapers).

### Solution & Architecture Rationale
1. **Redirect Search Flow:** Intercepted search form submissions in [search-news.js](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/public/front_end/classic/js/custom/search-news.js) and redirected users to a dedicated `/posts?search=query` results page ([SearchPostController.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Http/Controllers/SearchPostController.php)).
2. **Multi-Table Union Queries:** Refactored `SearchPostController` to construct optimized SQL subquery unions across `posts`, `stories`, and `e_newspapers` tables.
3. **Offcanvas/Sidebar Synchronization:** Synchronized filter checkbox states between the mobile offcanvas drawer (`#search-offcanvas`) and the desktop filter sidebar.
4. **AJAX Pagination & PushState:** Enabled background pagination fetching that updates the browser URL bar using `window.history.pushState()` without full page reloads.

### Files Modified
* [public/front_end/classic/js/custom/search-news.js](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/public/front_end/classic/js/custom/search-news.js)
* [resources/views/front_end/classic/layout/header.blade.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/resources/views/front_end/classic/layout/header.blade.php)
* [app/Http/Controllers/SearchPostController.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Http/Controllers/SearchPostController.php)
* [resources/views/front_end/classic/pages/search-result.blade.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/resources/views/front_end/classic/pages/search-result.blade.php)
* [resources/views/front_end/classic/pages/partials/search_result_posts.blade.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/resources/views/front_end/classic/pages/partials/search_result_posts.blade.php)

### Code Comparison
```javascript
/* [search-news.js - Redirect & PushState Navigation] */
$(document).on('submit', '#header-search-form', function(e) {
    e.preventDefault();
    var query = $(this).find('input[name="search"]').val();
    if (query.trim() !== '') {
        window.location.href = '/posts?search=' + encodeURIComponent(query);
    }
});
```

```php
/* [SearchPostController.php - Subquery Union] */
$postsQuery = DB::table('posts')
    ->select('id', 'title', 'slug', 'image', 'publish_date', DB::raw("'post' as content_type"))
    ->where('title', 'LIKE', "%{$search}%");

$storiesQuery = DB::table('stories')
    ->select('id', 'title', 'slug', 'image', 'created_at as publish_date', DB::raw("'story' as content_type"))
    ->where('title', 'LIKE', "%{$search}%");

$results = $postsQuery->unionAll($storiesQuery)->paginate(12);
```

### Impact & Future Scalability
* **Benefit:** Gives users deep-linkable, shareable search URLs with full support for content type filtering.
* **Caution:** When adding new content types to NewsHunt, ensure their database tables are included in `SearchPostController`'s union query builder.

---

## 2. End-to-End Instant AJAX Live Search & Custom NewsHunt Pagination Engine

### Feature Need / Requirement
1. **Live Search & Navbar Enter Redirect:** Search modal input must support live suggestions and redirect on Enter keypress to `/posts?search=query`.
2. **Page Live Search & Centered Header:** Results page must pre-populate search query, feature a centered search bar, and update results dynamically via 300ms debounced live search.
3. **Custom NewsHunt Pagination:** Renders native NewsHunt pagination controls (`nav-x uc-pagination hstack gap-1 justify-center ft-secondary text-black`) matching `vendor/custom-pagination.blade.php` (`<`, `1`, `2`, `...`, `5`, `>`).
4. **Search Query Preservation:** Pagination controls must preserve all active search terms and channel/topic filter selections across page transitions.

### Solution & Architecture Rationale
1. **Independent Engine Initialization:** Separated `initSearchPageFilterEngine()` from modal element checks so page live search executes reliably across all page view states.
2. **Channels Filter Normalization:** Stripped `'all'` values from `$channels` in `SearchPostController.php` to allow unfiltered queries when "All Channels" is checked.
3. **Dynamic Pagination URL Construction:** Built `buildPageUrl(p)` in `search-news.js` to collect active search input and checked filters, ensuring seamless AJAX pagination.

### Files Modified
* [app/Http/Controllers/SearchPostController.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Http/Controllers/SearchPostController.php)
* [resources/views/front_end/classic/pages/search-result.blade.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/resources/views/front_end/classic/pages/search-result.blade.php)
* [public/front_end/classic/js/custom/search-news.js](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/public/front_end/classic/js/custom/search-news.js)

### Code Comparison
```javascript
/* [search-news.js - buildPageUrl] */
function buildPageUrl(p) {
    var baseUrl = window.location.origin + window.location.pathname;
    var params = new URLSearchParams();
    var searchVal = pageSearchInput ? pageSearchInput.value.trim() : '';
    if (searchVal) params.append('search', searchVal);
    // append active channels, topics, sort filter...
    params.set('page', p);
    return baseUrl + '?' + params.toString();
}
```

### Impact & Future Scalability
* **Benefit:** Provides an ultra-responsive, consistent, deep-linkable search & filter experience matching NewsHunt design standards.
* **Caution:** When adding new filters to the search page, ensure `buildPageUrl(p)` and `triggerAjaxFetch()` collect the new parameters.

---

## 3. Search Page Clear Button and Pagination Dark Mode Fixes

### Feature Need / Requirement
1. **Clear Button Styling:** The "Clear filters" buttons (both desktop and mobile viewports) became solid purple on hover with dark purple text, making the button label completely unreadable. In dark mode, the button was also difficult to read due to conflicting text classes.
2. **Responsive Pagination:** The dynamic AJAX-based pagination controls on the search results page rendered page numbers as black text on a black background when dark mode was enabled, rendering them completely invisible to the user.

### Solution & Architecture Rationale
1. **Clean Class Hierarchy:** Removed the hardcoded `.text-primary` utility class from the Blade templates for mobile and desktop clear buttons. This allowed standard hover styles to fill the background with primary color and transition the text color to white.
2. **Parent Class Inheritance:** Removed the hardcoded `.text-black` utility class from the AJAX template builder in `search-news.js`.
3. **Explicit Stylesheet Rules:** Appended custom responsive rules to `custom.css` with `!important` flags to override text and hover colors across all page navigation controls on all pages, ensuring high contrast in both light (dark text) and dark (white text) modes.
4. **CSS Theme Synchronization:** Updated the `.btn-outline-primary` definition to pull border and hover background colors dynamically from the `--color-primary` CSS variable rather than static hex codes.

### Files Modified
* [resources/views/front_end/classic/pages/search-result.blade.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/resources/views/front_end/classic/pages/search-result.blade.php)
* [public/front_end/classic/js/custom/search-news.js](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/public/front_end/classic/js/custom/search-news.js)
* [public/front_end/classic/css/custom.css](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/public/front_end/classic/css/custom.css)

### Code Comparison
```html
<!-- [search-result.blade.php - Desktop Clear Button] -->
- <button type="button" id="btn-clear-filters-desktop" class="btn btn-outline-primary btn-sm text-primary w-100">
+ <button type="button" id="btn-clear-filters-desktop" class="btn btn-outline-primary btn-sm w-100">
```

```javascript
/* [search-news.js - Dynamic Pagination Container] */
- html += '<ul class="nav-x uc-pagination hstack gap-1 justify-center ft-secondary text-black" data-uc-margin="">';
+ html += '<ul class="nav-x uc-pagination hstack gap-1 justify-center ft-secondary" data-uc-margin="">';
```

```css
/* [custom.css - Responsive Outline Primary & Pagination] */
.btn-outline-primary {
  --bs-btn-color: var(--color-primary, #e62323);
  --bs-btn-border-color: var(--color-primary, #e62323);
}

.uc-dark .btn-outline-primary,
:where(.uc-dark) .btn-outline-primary {
  --bs-btn-color: #ffffff;
  --bs-btn-border-color: var(--color-primary, #e62323);
  --bs-btn-hover-color: #ffffff;
}

.nav-pagination a,
.uc-pagination a {
  color: var(--bs-body-color, #1f2937) !important;
}

.uc-dark .nav-pagination a,
:where(.uc-dark) .nav-pagination a,
.uc-dark .uc-pagination a,
:where(.uc-dark) .uc-pagination a {
  color: #ffffff !important;
}
```

### Impact & Future Scalability
* **Benefit:** Ensures perfect visual contrast and theme-color compliance for search pagination and outline buttons across all light and dark mode viewport configurations.
* **Caution:** Since the styles are defined globally in `custom.css` targeting `.uc-pagination a` and `.nav-pagination a`, any future custom pagination grids using these class selectors will automatically inherit these correct responsive styles.

