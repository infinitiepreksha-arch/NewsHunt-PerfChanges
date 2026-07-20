# 04 - Database Schema & Migrations Reference

## 1. Migration Lifecycle & Schema Overview
The NewsHunt database consists of 116 migration files in `database/migrations/`. All tables use the MySQL `InnoDB` engine with UTF-8 (`utf8mb4_unicode_ci`) encoding.

---

## 2. Core Database Tables & Specifications

### A. Core Content Tables
* **`posts`**
  * `id` (bigint unsigned, auto-increment, primary)
  * `title` (string, indexed)
  * `slug` (string, unique)
  * `type` (enum: `'article'`, `'video'`, `'youtube'`, `'audio'`)
  * `topic_id` (bigint unsigned, foreign key -> `topics.id`)
  * `channel_id` (bigint unsigned, foreign key -> `channels.id`)
  * `news_language_id` (bigint unsigned, foreign key -> `news_languages.id`)
  * `image` (string, path/URL)
  * `video` (string, nullable)
  * `video_thumb` (string, nullable)
  * `audio` (string, nullable)
  * `description` (longText, nullable)
  * `status` (enum: `'active'`, `'inactive'`, default: `'active'`)
  * `view_count` (bigint, default: 0)
  * `reaction` (bigint, default: 0)
  * `comment` (bigint, default: 0)
  * `favorite` (bigint, default: 0)
  * `shere` (bigint, default: 0)
  * `pubdate` (string, nullable)
  * `publish_date` (dateTime, indexed)
  * `created_at`, `updated_at` (timestamps)

* **`stories` & `story_slides`**
  * `stories.id` (primary key), `title`, `slug`, `topic_id`, `news_language_id`, `image`, `story_count` (bigint, default 0).
  * `story_slides.id` (primary key), `story_id` (foreign key -> `stories.id`), `image`, `description`, `slide_type`, `animation_details` (json/text), `order`.

* **`e_newspapers`**
  * `id` (primary key), `title`, `slug`, `topic_id`, `news_language_id`, `paperimage` (cover image), `file` (PDF document path), `total_page` (integer), `type` (enum: `'newspaper'`, `'magazine'`), `added_by_name`.

* **`topics` & `channels`**
  * `topics.id`, `name`, `slug`, `image`, `topic_order` (integer), `status`.
  * `channels.id`, `name`, `slug`, `logo`, `description`, `status`.

---

### B. Monetization & Subscriptions Tables
* **`pricing_plans` & `plan_tenure`**
  * `pricing_plans.id`, `title`, `price`, `status`, `description`.
  * `plan_tenure.id`, `plan_id` (foreign key), `tenure_name` (e.g. "1 Month", "1 Year"), `price`, `discount_price`, `duration` (days).

* **`features`**
  * `id`, `plan_id` (foreign key), `number_of_articles` (int), `number_of_stories` (int), `number_of_e_papers_and_magazines` (int).

* **`subscription`**
  * `id`, `user_id` (foreign key), `plan_id`, `plan_tenure_id`, `transaction_id`, `start_date`, `end_date`, `article_count` (int), `story_count` (int), `e_paper_count` (int), `status` (enum: `'active'`, `'expired'`).

* **`transaction`**
  * `id`, `user_id`, `transaction_id` (gateway order ID string), `payment_gateway` (enum: `'stripe'`, `'razorpay'`, `'paystack'`, `'apple'`), `amount`, `plan_details` (JSON payload of purchased features), `status`.

---

### C. Advertisements Tables
* **`smart_ads` & `smart_ad_placements`**
  * Programmatic ad campaigns, horizontal images, destination URLs, status, target placements.
* **`custom_ads_requests` & `custom_ads_payments`**
  * Advertiser self-serve ad requests, image assets, banner target dates, approval statuses, and transaction payment proofs.
* **`smart_ads_tracking` & `custom_ads_tracking`**
  * Logs user impression hits and click counters.

---

### D. User Engagement & System Logs Tables
* **`comments`**, **`blocked_comments`**, **`report_comments`**, **`report_types`**
  * Handles article comments, nested parent replies, flagged reasons, and admin moderation blocks.
* **`favorites`**
  * `user_id`, `post_id`, `is_pinned` (boolean flag for pin-to-top status).
* **`post_views`**, **`app_post_views`**, **`story_view`**
  * Individual view records for analytical tracking.
* **`news_languages`**, **`news_language_subscribers`**, **`news_language_status`**
  * Manages active content languages and user language subscriptions.
