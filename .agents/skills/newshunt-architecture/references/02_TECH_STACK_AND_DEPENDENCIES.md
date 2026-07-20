# 02 - Tech Stack & Dependencies Matrix

## 1. System Requirements & Core Runtimes
* **PHP:** `^8.1.0` (Enforces strict typing and null safety for internal PHP string functions).
* **Laravel Framework:** `^10.0` (Core MVC framework).
* **Database Engine:** MySQL `8.0+` (InnoDB engine with foreign key constraint checks).
* **Frontend Build System:** Vite `^3.1.0` with `@vitejs/plugin-vue` (`^3.1.0`) and `laravel-vite-plugin` (`^0.6.0`).
* **Package Managers:** Composer for PHP dependencies; NPM for Node.js modules.

---

## 2. Composer Packages Matrix (`composer.json`)

| Package Name | Version Constraint | Domain & Purpose |
|---|---|---|
| `laravel/framework` | `^10.0` | Core framework framework services. |
| `laravel/sanctum` | `^3.3` | Mobile REST API token authentication. |
| `laravel/passport` | `^12.3` | OAuth2 server infrastructure for API clients. |
| `laravel/socialite` | `^5.15` | Social authentication connectors (Google, etc.). |
| `spatie/laravel-permission` | `^6.20` | Role-based access control (RBAC) permissions. |
| `intervention/image` | `^2.7` | Image upload resizing, aspect ratio scaling & WebP conversion. |
| `spatie/image` | `^2.2` | Image manipulation utilities. |
| `spatie/laravel-medialibrary` | `^10.15` | Advanced model media collections management. |
| `stripe/stripe-php` | `^17.1` | Stripe subscription checkout & webhook API wrapper. |
| `razorpay/razorpay` | `^2.9` | Razorpay order generation and payment verification. |
| `unicodeveloper/laravel-paystack` | `^1.2` | Paystack payment gateway connector. |
| `kreait/firebase-php` | `^7.16` | Firebase SDK for authentication & token verification. |
| `kreait/laravel-firebase` | `^5.10` | Laravel integration wrapper for Kreait Firebase. |
| `dacoto/laravel-wizard-installer` | `^1.0` | Interactive Web Installer wizard for database seeding & keys setup. |
| `5balloons/laravel-smart-ads` | `^1.2` | Programmatic advertisement banner manager. |
| `devdojo/laravel-reactions` | `^1.2` | Core post reactions framework. |
| `spatie/laravel-sitemap` | `^7.0` | Automatic XML sitemap generation (`sitemap.xml`). |
| `laravel/horizon` | `^5.31` | Redis queue monitoring dashboard. |
| `vladimir-yuldashev/laravel-queue-rabbitmq` | `^14.1` | RabbitMQ messaging queue driver integration. |
| `rap2hpoutre/laravel-log-viewer` | `^2.4` | In-admin web log viewer interface (`/admin/settings/error-logs`). |
| `barryvdh/laravel-debugbar` | `^3.7` (dev) | Real-time SQL query profiling & debug metrics. |

---

## 3. Node & NPM Frontend Packages (`package.json`)

| Package Name | Version Constraint | Purpose |
|---|---|---|
| `vite` | `^3.1.0` | Fast frontend asset bundling engine. |
| `bootstrap` | `^5.1.3` | Grid CSS classes and utility buttons. |
| `axios` | `^0.27` | Client-side HTTP requests wrapper. |
| `highcharts` | `^11.0.1` | Admin dashboard analytics charts. |
| `@popperjs/core` | `^2.10.2` | Dynamic tooltip and dropdown positioning. |
| `sass` | `^1.32.11` | SCSS stylesheet compiler. |

---

## 4. Frontend Vendor Libraries (Direct Script Libraries)
* **UIKit:** Responsive grid structure (`uk-child-width-*`), offcanvas drawers, modal lifecycle events (`beforeshow`).
* **SwiperJS:** Touch carousel sliders on homepage (`slidesPerView`, `breakpoints`).
* **iziToast:** Client-side notification toasts.
* **SweetAlert2:** Modal alert dialogs.
* **DotLottie Player:** Vectorized Lottie animation loader widget (`dotlottie-player`).
* **Bootstrap Icons:** SVG Icon set (`bi-` prefixed icons).
