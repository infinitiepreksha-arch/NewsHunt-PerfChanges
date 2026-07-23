# Implementation Plan: Fix Clear Button & Pagination Hover and Dark Mode Responsiveness

This plan addresses the dark mode and hover state issues on the search results page:
1. The **Clear filters button** becomes solid purple with unreadable text on hover, and is hard to read in dark mode.
2. The **Pagination numbers** are invisible (black text on black background) on the search results page in dark mode.

---

## 🔍 Root Cause Analysis & Discovery

We investigated why pagination was working correctly on topic and channel pages (like `/topics/business`) but failing on the search results page:

1. **Blade Rendering (Invalid nesting):** On initial page load of topics and channels, the pagination is rendered via Blade as:
   ```html
   <ul class="nav-x uc-pagination ..."> <!-- Outer -->
       <ul class="nav-x uc-pagination ... text-black"> <!-- Inner (vendor/custom-pagination) -->
           <li><a href="...">1</a></li>
       </ul>
   </ul>
   ```
   Nesting `<ul>` directly inside `<ul>` is invalid HTML. Modern browsers auto-correct this by ignoring the inner `<ul>` tag and its classes (including `.text-black`). Consequently, the links inherit the body's dark mode text color (`white`).
2. **AJAX Rendering (Valid nesting):** On the search results page, filters and page changes trigger AJAX rendering via `search-news.js`. The JS generates:
   ```html
   <div class="nav-pagination ...">
       <ul class="... text-black">
   ```
   Because a `<div>` wrapping a `<ul>` is valid HTML, the browser does not alter the DOM. The `.text-black` class remains fully active, forcing all page links to render in black, making them invisible against the dark background.

---

## Proposed Changes

### Views & JavaScript

#### [MODIFY] [search-result.blade.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/resources/views/front_end/classic/pages/search-result.blade.php)
- Remove `text-primary` class from the mobile clear filters button (`#btn-clear-filters-mobile`, line 137).
- Remove `text-primary` class from the desktop clear filters button (`#btn-clear-filters-desktop`, line 243-244).

#### [MODIFY] [search-news.js](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/public/front_end/classic/js/custom/search-news.js)
- Remove the hardcoded `text-black` class from the dynamically generated pagination container `<ul>` (line 626).

---

### Assets & Styles

#### [MODIFY] [custom.css](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/public/front_end/classic/css/custom.css)
- Append CSS overrides to dynamically set `.btn-outline-primary` colors using the theme's `--color-primary` CSS variable.
- Append custom styles for `.nav-pagination` and `.uc-pagination` to style inactive, hovered, and active pagination items in both light and dark modes.

```css
/* Dynamic Theme Color and Dark Mode styling for Outline Primary Button */
.btn-outline-primary {
  --bs-btn-color: var(--color-primary, #e62323);
  --bs-btn-border-color: var(--color-primary, #e62323);
  --bs-btn-hover-bg: var(--color-primary, #e62323);
  --bs-btn-hover-border-color: var(--color-primary, #e62323);
  --bs-btn-active-bg: var(--color-primary, #e62323);
  --bs-btn-active-border-color: var(--color-primary, #e62323);
  --bs-btn-disabled-color: var(--color-primary, #e62323);
  --bs-btn-disabled-border-color: var(--color-primary, #e62323);
}

.uc-dark .btn-outline-primary,
:where(.uc-dark) .btn-outline-primary {
  --bs-btn-color: #ffffff;
  --bs-btn-border-color: var(--color-primary, #e62323);
  --bs-btn-hover-bg: var(--color-primary, #e62323);
  --bs-btn-hover-border-color: var(--color-primary, #e62323);
  --bs-btn-hover-color: #ffffff;
  --bs-btn-active-bg: var(--color-primary, #e62323);
  --bs-btn-active-border-color: var(--color-primary, #e62323);
  --bs-btn-active-color: #ffffff;
}

/* Pagination responsiveness and dark mode support */
.nav-pagination a,
.uc-pagination a {
  color: var(--bs-body-color, #1f2937) !important;
  transition: all 0.2s ease-in-out;
}

.nav-pagination a:hover,
.uc-pagination a:hover {
  background-color: var(--color-primary-10, rgba(230, 35, 35, 0.1)) !important;
  color: var(--color-primary) !important;
}

.nav-pagination a.uc-active,
.uc-pagination .uc-active a,
.uc-pagination a.uc-active {
  background-color: var(--color-primary) !important;
  color: #ffffff !important;
}

.uc-dark .nav-pagination a,
:where(.uc-dark) .nav-pagination a,
.uc-dark .uc-pagination a,
:where(.uc-dark) .uc-pagination a {
  color: #ffffff !important;
}

.uc-dark .nav-pagination a:hover,
:where(.uc-dark) .nav-pagination a:hover,
.uc-dark .uc-pagination a:hover,
:where(.uc-dark) .uc-pagination a:hover {
  background-color: rgba(255, 255, 255, 0.1) !important;
  color: #ffffff !important;
}

.uc-dark .nav-pagination a.uc-active,
:where(.uc-dark) .nav-pagination a.uc-active,
.uc-dark .uc-pagination .uc-active a,
:where(.uc-dark) .uc-pagination .uc-active a,
.uc-dark .uc-pagination a.uc-active,
:where(.uc-dark) .uc-pagination a.uc-active {
  background-color: var(--color-primary) !important;
  color: #ffffff !important;
}
```

---

## Verification Plan

### Manual Verification
- **Clear Button:**
  - Load search page in light mode. Hover over "Clear". Verify background becomes primary and text turns white.
  - Switch to dark mode. Verify text is white, outline is primary, and hover behavior is correct.
- **Pagination:**
  - In light mode, verify all page numbers are visible (dark gray/black) and the active page has a primary background with white text.
  - Switch to dark mode. Verify all page numbers are fully visible (white) and the active page has a primary background with white text on both static pages (topics/channels) and the AJAX search results page.
