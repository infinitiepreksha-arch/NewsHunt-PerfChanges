# 13 - Responsiveness, Cross-Browser & Mobile API Safety Guidelines

## 1. CodeCanyon Commercial Product Context
NewsHunt is a commercial project listed on CodeCanyon (Envato platform). The codebase must remain clean, modular, well-documented, and easy for third-party buyers to install, configure, and maintain via standard Laravel practices and web installer scripts without requiring custom server patches.

---

## 2. Cross-Device Responsiveness (Mobile, Tablet & Desktop)

All frontend web pages must look premium and function flawlessly across screen sizes:
* **Mobile Handsets (320px – 480px):** Single-column grid layouts (`uk-child-width-1-1`), compact touch buttons, collapsible offcanvas search drawer (`#search-offcanvas`).
* **Tablets (768px – 1024px):** 2-column or 3-column cards (`uk-child-width-1-2@s`, `uk-child-width-1-3@m`), tablet-adapted Swiper slides (`slidesPerView: 2`).
* **Desktop Monitors (1200px+):** Full 4-column or 5-column grid layouts, expanded search filter sidebars, multi-item banner carousels (`slidesPerView: 4`).

### CLS & Layout Stability Enforcement
* Explicitly set `width` and `height` attributes on dynamic images, channel badges, and weather widget containers.
* Retain CSS container heights (such as `.epaper_css` 300px block) to prevent structural shifts when images finish downloading.

---

## 3. Mobile REST API Backward Compatibility

Mobile apps deployed on Apple App Store and Google Play depend on endpoints in [routes/api.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/routes/api.php).

### Strict API Rules
1. **Never Remove Response Keys:** Never delete, rename, or retype existing JSON keys returned by REST API controllers under `/api/v1/`.
2. **Sanctum Authentication Integrity:** Retain standard Bearer Token authorization handling (`auth:sanctum`).
3. **Additive-Only Modifications:** Any new backend features or model attributes must be added as optional fields with fallback default values to ensure existing mobile app builds do not crash.

---

## 4. Cross-Browser JavaScript Compatibility

* **Feature Detection:** Never call modern Web APIs without checking support (e.g. `"IntersectionObserver" in window`).
* **Browser Engines:** Ensure layout styling and JavaScript functions run consistently across Chrome, Safari (Desktop & iOS Mobile Safari), Firefox, Edge, and Android Chrome webviews.
