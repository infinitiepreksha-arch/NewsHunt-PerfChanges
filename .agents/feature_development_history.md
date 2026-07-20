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
