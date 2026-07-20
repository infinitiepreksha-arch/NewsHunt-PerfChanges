# 01 - Project Overview & Business Domain

## 1. Executive Summary
**NewsHunt** (v1.4.9) is a commercial, multi-channel news, audio, video, web stories, and e-paper publishing portal built on Laravel. It is listed on CodeCanyon (Envato platform) and serves as an all-in-one content management, publishing, and monetization product for publishers worldwide.

---

## 2. Platform Portals & User Roles

### A. Back-Office Administration Panel (`/admin`)
* **Target Users:** Super Administrators, Editors, Content Managers, and Advertisers.
* **Capabilities:**
  * Complete CMS management for Articles, Videos, Audio Podcasts, Web Stories, and E-Newspapers.
  * Automated RSS Feed crawling configuration and manual single-fetch triggers.
  * Custom sponsored advertisement review, approval, placement mapping, and impression/click analytics.
  * User role & permission management using Spatie RBAC.
  * Subscription model creation, pricing plans, duration tenures, and transaction logs monitoring.
  * System health check, SMTP mail configuration, Firebase keys setup, and logo/theme customizations.

### B. Customer Website (`/`)
* **Target Users:** Web visitors, desktop readers, mobile web users, and paid subscribers.
* **Capabilities:**
  * Multi-theme responsive layouts (e.g. `classic` visual theme).
  * Category (Topic) and Publisher (Channel) navigation and channel follow features.
  * Paywalled article, web story, and e-paper preview limits enforced via user subscriptions.
  * User account management, bookmarking (with pin-to-top status), and dynamic language switching.
  * Interactive engagement: article reaction counters, nested comment threads, comment reporting/flagging.

### C. Mobile Application APIs (`/api/v1/`)
* **Target Users:** Native iOS and Android mobile app users.
* **Capabilities:**
  * Sanctum token-based authentication and Firebase token verification.
  * Eager-loaded homepage content feeds, topic lists, channel profiles, and user favorites.
  * Native payment gateway integration (Stripe, Razorpay, Apple Receipt Verification, Paystack).
  * Push notification registration (FCM tokens storage and click handlers).

---

## 3. High-Level Architecture Diagram

```
+-----------------------------------------------------------------------------------+
|                                  CLIENT LAYER                                     |
|   +-----------------------+   +-----------------------+   +-------------------+   |
|   | Desktop & Mobile Web  |   | Mobile Apps (iOS/Android) | Admin Dashboard   |   |
|   +-----------+-----------+   +-----------+-----------+   +---------+---------+   |
+---------------+---------------------------+-------------------------+-------------+
                |                           |                         |
                v                           v                         v
+-----------------------------------------------------------------------------------+
|                                 APPLICATION LAYER                                 |
|   +-----------------------+   +-----------------------+   +-------------------+   |
|   | Web Controllers &     |   | REST API Controllers  |   | Admin CMS         |   |
|   | Blade Views           |   | (Sanctum Auth)        |   | Controllers       |   |
|   +-----------+-----------+   +-----------+-----------+   +---------+---------+   |
|               |                           |                         |             |
|               +-------------------+-------+-------------------------+             |
|                                   |                                               |
|                                   v                                               |
|               +---------------------------------------+                           |
|               | AppServiceProvider & Request Caching |                           |
|               +-------------------+-------------------+                           |
+-----------------------------------|-----------------------------------------------+
                                    |
                                    v
+-----------------------------------------------------------------------------------+
|                                   DATA LAYER                                      |
|   +-----------------------+   +-----------------------+   +-------------------+   |
|   | MySQL Database 8.0+   |   | Cache (Redis/File)    |   | Disk Storage      |   |
|   +-----------------------+   +-----------------------+   +-------------------+   |
+-----------------------------------------------------------------------------------+
```

---

## 4. Key Business Domain Goals
1. **Performance:** Deliver high-speed page loads (< 1.5s) using request attributes, Eloquent hydration limits, and WebP asset compression.
2. **Monetization:** Provide flexible subscription tiers (articles, stories, e-papers) alongside custom advertiser self-serve campaigns.
3. **Multi-Language Access:** Support distinct Web UI localization (UI labels) and Content News Languages (filtered feed items).
4. **Third-Party Product Stability:** Preserve CodeCanyon buyer maintainability by avoiding hardcoded dependencies and strictly maintaining backward compatibility across database migrations and REST APIs.
