# 12 - Change Impact Guide & Technical Debt

## 1. AI Agent Pre-Flight Safety Checklist

Before modifying any file in this codebase, AI coding agents MUST verify:

1. **Read History Logs:** Read [PROJECT_HISTORY.md](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/PROJECT_HISTORY.md) to understand past optimizations and architectural decisions.
2. **Preserve Business Logic:** Never alter existing routes, middleware, paywall validation methods, or database schemas without explicit user request.
3. **Check PHP 8.1 Null-Safety:** Safeguard native string methods (`html_entity_decode($var ?? '')`) to prevent deprecation exceptions.
4. **Preserve Image Pipeline:** Always use `FileService::resizeAndCompressUpload()` to resize uploads to 800px max width and encode to 60% WebP.
5. **Update History & File Logs:**
   * Append changes to [PROJECT_HISTORY.md](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/PROJECT_HISTORY.md).
   * Update the master index table in [FILE_CHANGES_LOG.md](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/FILE_CHANGES_LOG.md).
   * Report task changed file count and cumulative unique file count in the response summary.

---

## 2. Known Technical Debt & Edge Cases

* **Cloudflare Rocket Loader Script Race Conditions:** Cloudflare Rocket Loader can load scripts out of order. Always provide fallback mocks (e.g. `window.iziToast` fallback in [custom-jquery.js](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/public/front_end/classic/js/custom/custom-jquery.js)) before executing client toast or modal calls.
* **UIKit Event Bubbling:** UIKit custom events (`beforeshow`, `hide`) do not bubble up the DOM tree to `document`. Always attach native Event Listeners directly to targeted elements when managing UIkit dropdowns or modals.
* **Database Model Blowup:** Do NOT fetch large Eloquent collections inside category loops. Always enforce database-level `take(5)` limits or use raw Query Builder selections (`DB::table(...)`).
