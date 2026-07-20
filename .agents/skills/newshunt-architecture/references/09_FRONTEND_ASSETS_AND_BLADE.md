# 09 - Frontend Assets, JavaScript Engines & Blade Views

## 1. Frontend Directory Layout
All compiled and static frontend assets live in `public/front_end/classic/`.

```
public/front_end/classic/
├── css/
│   ├── custom.css                 # Primary custom CSS overrides & layout heights
│   ├── style.css                  # Core theme stylesheets
│   └── uikit.min.css              # UIKit framework styles
│
├── js/
│   ├── app-head-bs.js             # Early head execution scripts
│   ├── custom/
│   │   ├── custom-jquery.js       # Main AJAX pagination & slider template builder
│   │   └── search-news.js         # Interactive search filters, pushState & autocomplete
│   │
│   ├── uikit.min.js               # UIKit JavaScript components engine
│   └── swiper-bundle.min.js       # Touch slider carousel script engine
```

---

## 2. Core JavaScript Engines Deep Dive

### A. [custom-jquery.js](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/public/front_end/classic/js/custom/custom-jquery.js) (Frontend Master Controller)
* **Slider Pagination:** Handles Swiper slide transitions for Most Read, Web Stories, Followed Channels, and Top Posts carousels.
* **AJAX Pagination Fetcher:** Triggers dynamic GET requests to deferred endpoints (`/most-read-remaining`, `/web-stories-remaining`, etc.) when users click carousel next navigation arrows.
* **Client-Side Templating:** Converts returned raw JSON models into clean HTML snippets using functions:
  * `buildMostReadSlideHtml(post)`
  * `buildStorySlideHtml(story)`
  * `buildTopPostSlideHtml(post)`
  * `buildTopicDropdownPostHtml(post)`
* **IntersectionObserver Hook:** Triggers `window.lazyLoadElements()` after dynamic AJAX DOM injections to evaluate and load new `lazy-img` tags.
* **Toaster Safety Mock:** Defines a fallback mock for `window.iziToast` to prevent client JS crashes if Cloudflare Rocket Loader loads scripts out of order.

### B. [search-news.js](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/public/front_end/classic/js/custom/search-news.js) (Advanced Search Engine)
* **Form Submission Redirect:** Intercepts header search form submissions and redirects to `/posts?search=query`.
* **Sidebar Synchronization:** Listens to input checkbox events and automatically synchronizes selection states between the mobile offcanvas drawer and desktop filter sidebar.
* **AJAX Results Fetcher:** Intercepts pagination clicks, executes background requests, updates the browser URL bar using `window.history.pushState()`, and scrolls the user back to the top of `#content-area`.

---

## 3. Core Web Vitals & CSS Conventions

### A. CLS Prevention Rules
* **Fixed Aspect Heights:** `.epaper_css` preserves a fixed `300px` height container block to eliminate Cumulative Layout Shift (CLS) on mobile and desktop viewports.
* **Uninitialized Swiper Anti-Shift:** Hides secondary slides before Swiper JS initializes:
```css
.swiper:not(.swiper-initialized) .swiper-slide:not(:first-child) {
    display: none !important;
}
```

### B. Programmatic Lazy Loading
* Images and YouTube iframes use transparent 1x1 base64 placeholders (`src="data:image/gif;base64,..."`) and `data-src` attributes. The vanilla `IntersectionObserver` inside [main.blade.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/resources/views/front_end/classic/layout/main.blade.php) swaps real sources when elements scroll within 400px of the viewport.
