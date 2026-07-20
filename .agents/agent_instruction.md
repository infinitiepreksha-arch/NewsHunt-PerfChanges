# NewsHunt System Master Guidelines & Agent Instructions

This document is the master entry point for AI coding agents working on the **NewsHunt** codebase (v1.4.9).

The complete, end-to-end knowledge base for this repository is codified inside the custom skill:
👉 **[.agents/skills/newshunt-architecture/SKILL.md](file:///.agents/skills/newshunt-architecture/SKILL.md)**

---

## 📚 Master Knowledge Base Directory

Refer to the specialized reference documents inside `.agents/skills/newshunt-architecture/references/` whenever developing or modifying specific subsystems:

| Reference Document | Domain & Scope |
|---|---|
| [01_PROJECT_OVERVIEW.md](file:///.agents/skills/newshunt-architecture/references/01_PROJECT_OVERVIEW.md) | Business domain, CodeCanyon context, target users, capabilities (Admin, Web, Mobile API) & goals. |
| [02_TECH_STACK_AND_DEPENDENCIES.md](file:///.agents/skills/newshunt-architecture/references/02_TECH_STACK_AND_DEPENDENCIES.md) | PHP 8.1+, Laravel 10, MySQL 8, Vite 3, Composer & NPM packages matrix. |
| [03_FOLDER_STRUCTURE_AND_CONVENTIONS.md](file:///.agents/skills/newshunt-architecture/references/03_FOLDER_STRUCTURE_AND_CONVENTIONS.md) | Directory map (app, resources, database, routes) & architectural conventions. |
| [04_DATABASE_SCHEMA_AND_MIGRATIONS.md](file:///.agents/skills/newshunt-architecture/references/04_DATABASE_SCHEMA_AND_MIGRATIONS.md) | Deep dive into all 116 migrations, tables, columns, indexes & foreign key cascades. |
| [05_MODELS_AND_RELATIONS.md](file:///.agents/skills/newshunt-architecture/references/05_MODELS_AND_RELATIONS.md) | Complete breakdown of 60+ Eloquent models, relationships, fillables, scopes & casts. |
| [06_ROUTES_MIDDLEWARE_AND_APIS.md](file:///.agents/skills/newshunt-architecture/references/06_ROUTES_MIDDLEWARE_AND_APIS.md) | Full routing table (`web.php` & `api.php`), middleware stacks & Sanctum guards. |
| [07_CONTROLLERS_AND_CALL_CHAINS.md](file:///.agents/skills/newshunt-architecture/references/07_CONTROLLERS_AND_CALL_CHAINS.md) | Controller → Service → Trait → Model execution flow maps for Web, Admin, and REST APIs. |
| [08_BUSINESS_RULES_AND_PAYWALLS.md](file:///.agents/skills/newshunt-architecture/references/08_BUSINESS_RULES_AND_PAYWALLS.md) | Paywall limits checking, pricing tenures, Web Story view cookies, RSS ingestion & ad analytics. |
| [09_FRONTEND_ASSETS_AND_BLADE.md](file:///.agents/skills/newshunt-architecture/references/09_FRONTEND_ASSETS_AND_BLADE.md) | JS engines ([custom-jquery.js](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/public/front_end/classic/js/custom/custom-jquery.js), [search-news.js](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/public/front_end/classic/js/custom/search-news.js)), SwiperJS, UIKit & Blade layouts. |
| [10_EVENTS_QUEUES_AND_SCHEDULES.md](file:///.agents/skills/newshunt-architecture/references/10_EVENTS_QUEUES_AND_SCHEDULES.md) | Console scheduling ([Kernel.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Console/Kernel.php)), Artisan commands (`rss:fetch`, `images:compress`), queues & Horizon. |
| [11_AUTHENTICATION_AND_AUTHORIZATION.md](file:///.agents/skills/newshunt-architecture/references/11_AUTHENTICATION_AND_AUTHORIZATION.md) | Web auth, Firebase Google/Phone callbacks, and Spatie RBAC permission tables. |
| [12_CHANGE_IMPACT_AND_KNOWN_DEBT.md](file:///.agents/skills/newshunt-architecture/references/12_CHANGE_IMPACT_AND_KNOWN_DEBT.md) | Pre-flight safety checklist for AI subagents, PHP 8.1 compatibility rules & technical debt. |
| [13_RESPONSIVENESS_CROSS_BROWSER_AND_MOBILE_API_SAFETY.md](file:///.agents/skills/newshunt-architecture/references/13_RESPONSIVENESS_CROSS_BROWSER_AND_MOBILE_API_SAFETY.md) | Cross-device UI responsiveness, browser compatibility & Mobile REST API backward compatibility. |

---

## ⚡ Mandatory Working Protocol for AI Agents

1. **Read History & Update Files Log:**
   * Read [PROJECT_HISTORY.md](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/PROJECT_HISTORY.md) before starting any work.
   * After completing any task, append task entry to [PROJECT_HISTORY.md](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/PROJECT_HISTORY.md) and update the master go-to table in [FILE_CHANGES_LOG.md](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/FILE_CHANGES_LOG.md).
   * Report task file count and cumulative unique file count in response turn summaries.
2. **Preserve Business & Mobile API Compatibility:**
   * Never alter existing API response key names, types, or Sanctum authorization wrappers under `/api/v1/`.
3. **Cross-Device Responsiveness:**
   * Ensure modifications preserve UIkit responsive grid classes, Swiper JS breakpoints, and container heights (`.epaper_css` 300px block) across mobile, tablet, and desktop viewports.
4. **Request-Scoped Cache Protection:**
   * Use `request()->attributes` instead of class `static` properties inside Providers/Controllers to avoid memory leaks under Swoole/FrankenPHP.
5. **Optimized Model Loading & Image Pipeline:**
   * Use `take(5)` limits or `DB::table()` selections to avoid hydrating large Eloquent collections.
   * Pass uploaded image files through `FileService::resizeAndCompressUpload()` (max 800px width, 60% quality WebP).
