---
name: newshunt-architecture
description: Comprehensive architecture, tech stack, database models, business rules, API safety, and change guidelines for the NewsHunt Laravel project. Use whenever developing, refactoring, or adding features to NewsHunt.
---

# NewsHunt Architecture & System Knowledge Base

This skill provides an exhaustive, intern-friendly "gold mine" blueprint of the **NewsHunt** codebase (v1.4.9). It covers technical specifications, database models, payment integration flows, paywalls, cross-device UI rules, mobile REST API compatibility, and deployment safety guidelines.

---

## 📌 Master Reference Directory

Every subsystem of this repository is documented in detail inside the `references/` directory. Refer to these files whenever working on specific features:

| Reference Document | Contents & Scope |
|---|---|
| [01_PROJECT_OVERVIEW.md](file:///.agents/skills/newshunt-architecture/references/01_PROJECT_OVERVIEW.md) | Business domain, CodeCanyon context, target users, capabilities (Admin, Web, Mobile API) & goals. |
| [02_TECH_STACK_AND_DEPENDENCIES.md](file:///.agents/skills/newshunt-architecture/references/02_TECH_STACK_AND_DEPENDENCIES.md) | PHP 8.1+, Laravel 10, MySQL 8, Vite 3, Composer packages & NPM libraries matrix. |
| [03_FOLDER_STRUCTURE_AND_CONVENTIONS.md](file:///.agents/skills/newshunt-architecture/references/03_FOLDER_STRUCTURE_AND_CONVENTIONS.md) | Comprehensive folder map, MVC patterns, Services, Traits (`SelectsFields`), and request attributes. |
| [04_DATABASE_SCHEMA_AND_MIGRATIONS.md](file:///.agents/skills/newshunt-architecture/references/04_DATABASE_SCHEMA_AND_MIGRATIONS.md) | Deep dive into all 116 database migrations, tables, columns, indexes, and foreign key cascades. |
| [05_MODELS_AND_RELATIONS.md](file:///.agents/skills/newshunt-architecture/references/05_MODELS_AND_RELATIONS.md) | Complete breakdown of 60+ Eloquent models, relationships, fillables, scopes, and casts. |
| [06_ROUTES_MIDDLEWARE_AND_APIS.md](file:///.agents/skills/newshunt-architecture/references/06_ROUTES_MIDDLEWARE_AND_APIS.md) | Route matrix for `web.php` and `api.php`, middleware stacks, named routes, and Sanctum tokens. |
| [07_CONTROLLERS_AND_CALL_CHAINS.md](file:///.agents/skills/newshunt-architecture/references/07_CONTROLLERS_AND_CALL_CHAINS.md) | Controller → Service → Trait → Model execution flow maps for Web, Admin, and REST APIs. |
| [08_BUSINESS_RULES_AND_PAYWALLS.md](file:///.agents/skills/newshunt-architecture/references/08_BUSINESS_RULES_AND_PAYWALLS.md) | Paywall limits checking, pricing tenures, Web Story view cookies, RSS ingestion, and ad analytics. |
| [09_FRONTEND_ASSETS_AND_BLADE.md](file:///.agents/skills/newshunt-architecture/references/09_FRONTEND_ASSETS_AND_BLADE.md) | JS engines ([custom-jquery.js](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/public/front_end/classic/js/custom/custom-jquery.js), [search-news.js](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/public/front_end/classic/js/custom/search-news.js)), SwiperJS, UIKit & Blade layouts. |
| [10_EVENTS_QUEUES_AND_SCHEDULES.md](file:///.agents/skills/newshunt-architecture/references/10_EVENTS_QUEUES_AND_SCHEDULES.md) | Console scheduling ([Kernel.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Console/Kernel.php)), Artisan commands (`rss:fetch`, `images:compress`), queues & Horizon. |
| [11_AUTHENTICATION_AND_AUTHORIZATION.md](file:///.agents/skills/newshunt-architecture/references/11_AUTHENTICATION_AND_AUTHORIZATION.md) | Web session auth, Firebase Phone/Google callbacks, and Spatie RBAC permission tables. |
| [12_CHANGE_IMPACT_AND_KNOWN_DEBT.md](file:///.agents/skills/newshunt-architecture/references/12_CHANGE_IMPACT_AND_KNOWN_DEBT.md) | Pre-flight safety checklist for AI subagents, PHP 8.1 compatibility rules, and technical debt. |
| [13_RESPONSIVENESS_CROSS_BROWSER_AND_MOBILE_API_SAFETY.md](file:///.agents/skills/newshunt-architecture/references/13_RESPONSIVENESS_CROSS_BROWSER_AND_MOBILE_API_SAFETY.md) | Mobile/tablet/desktop responsive design rules, cross-browser JS, and Mobile REST API backward compatibility. |

---

## ⚡ Master Working Rules for AI Agents

Whenever developing or modifying code in this codebase:

1. **Check History & File Changes Log:**
   * Always read [PROJECT_HISTORY.md](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/PROJECT_HISTORY.md) before starting.
   * After completion, append task entries to [PROJECT_HISTORY.md](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/PROJECT_HISTORY.md) and update the master index table in [FILE_CHANGES_LOG.md](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/FILE_CHANGES_LOG.md).
2. **Never Break Existing Business Logic:**
   * Do not alter existing routes, middleware, models, database schemas, or payment flows without explicit approval.
3. **Mobile API Compatibility:**
   * Never modify existing key names or data types under `/api/v1/` routes. Keep all mobile REST API modifications additive and backward-compatible.
4. **Cross-Device UI Responsiveness:**
   * Preserve responsive UIKit grids (`uk-child-width-*`), SwiperJS breakpoint settings, and explicit element aspect ratio/height constraints (`.epaper_css` 300px block) to prevent layout shifts across mobile, tablet, and desktop viewports.
5. **Request-Scoped State Caching:**
   * Use `request()->attributes` instead of PHP class `static` properties inside Providers/Controllers to avoid memory leaks under daemon runners (FrankenPHP/Swoole).
6. **Optimized Model Loading:**
   * Avoid hydrating large Eloquent collections in loops (`get()`). Use database-level limits (`take(5)`) or raw query selections (`DB::table(...)`) to keep RAM consumption under control.
7. **WebP Compression Pipeline:**
   * Route uploaded media files through `FileService::resizeAndCompressUpload` to scale to maximum width (800px) and compress to WebP at 60% quality.
8. **PHP 8.1 Null-Safety:**
   * Null-coalesce variables passed to native string functions (`html_entity_decode($str ?? '')`) to prevent deprecation exceptions.
