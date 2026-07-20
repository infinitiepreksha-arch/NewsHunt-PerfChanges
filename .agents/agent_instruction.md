# NewsHunt System Master Guidelines & Agent Instructions

This document is the master entry point for AI coding agents working on the **NewsHunt** codebase (v1.4.9).

Operational workflow, safety guards, and mandatory phase rules are codified in:
👉 **[.agents/AGENTS.md](file:///.agents/AGENTS.md)**

The complete, end-to-end knowledge base for this repository is codified inside the custom skill:
👉 **[.agents/skills/newshunt-architecture/SKILL.md](file:///.agents/skills/newshunt-architecture/SKILL.md)**

---

## ⚡ Non-Negotiable Operational Phase Protocol

Every AI agent working on this project MUST strictly follow these 8 execution phases:

1. **Phase 1 (Brainstorming & Discussion):** Discuss options, trade-offs, and align with the user before writing code.
2. **Phase 2 (Implementation Plan):** Draft an implementation plan (`implementation_plan.md`) detailing proposed changes.
3. **Phase 3 (User Plan Approval):** **STOP AND WAIT** for explicit user approval before proceeding to code edits.
4. **Phase 4 (Feature Branching):** Create a new branch (`git checkout -b feature/<feature-name>`) for any change to prevent merge conflicts.
5. **Phase 5 (Execution & Verification):** Implement minimal production-ready code & verify locally.
6. **Phase 6 (Code Review):** Present changed files & diffs to the user for review. **WAIT for user ok** before proceeding.
7. **Phase 7 (History & Tracking):** Append changes to [PROJECT_HISTORY.md](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/PROJECT_HISTORY.md), update [FILE_CHANGES_LOG.md](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/FILE_CHANGES_LOG.md) (code files), and update [.agents/AGENTS_CHANGES_LOG.md](file:///.agents/AGENTS_CHANGES_LOG.md) (agent files).
8. **Phase 8 (Git Commit Permission):** **NEVER COMMIT OR PUSH WITHOUT EXPLICIT USER PERMISSION.** Always ask before staging or committing.

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
