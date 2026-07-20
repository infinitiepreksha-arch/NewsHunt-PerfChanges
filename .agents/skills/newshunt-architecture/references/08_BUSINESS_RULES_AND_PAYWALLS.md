# 08 - Business Logic Rules, Paywalls & Algorithms

## 1. Subscription & Paywall Verification System

### A. Access Threshold Rule
Users access paid content based on active memberships (`subscription` table record with `status = 'active'`). 
Feature limits are retrieved from `$subscription->transactions->plan_details['features'][0]`.

* **Articles Paywall:** Checked against `number_of_articles`. Tracked via `subscription.article_count`.
* **Stories Paywall:** Checked against `number_of_stories`. Tracked via `subscription.story_count`.
* **E-Paper/Magazines Paywall:** Checked against `number_of_e_papers_and_magazines`. Tracked via `subscription.e_paper_count`.

### B. Fallback Free Trial Rules
If a visitor is unauthenticated or does not possess an active subscription record:
* The system retrieves system default settings:
  * `free_trial_story_limit` (default: 10)
  * `free_trial_post_limit` (default: 10)
* If the guest exceeds these limits, access is blocked and the frontend displays a modal redirecting them to the pricing plans page (`/membership`).

---

## 2. Dynamic Content Ingestion (RSS Feeds Parser)

### A. Automated Crawling Flow
1. Scheduler runs `rss:fetch` Artisan command every 15 minutes ([Kernel.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Console/Kernel.php)).
2. Reads all active target records from the `rss_feeds` table.
3. Downloads XML content using cURL / Guzzle HTTP client.
4. Parses XML feed items using DOMCrawler / SimpleXML.

### B. De-duplication & Image WebP Pipeline
1. Extracts `title`, `description`, `pubDate`, and `enclosure` cover images.
2. Checks existing posts for duplicate matching titles or slugs. Duplicate entries are discarded.
3. Downloads the post image to local disk, runs `FileService::resizeAndCompressUpload()` (resizing width to max 800px, 60% quality, WebP format).
4. Saves new `Post` record with `type = 'article'` assigned to target `topic_id` and `channel_id`.

---

## 3. Programmatic Advertising Logic

### A. Smart Ads (Inline Banner Injection)
* Smart Ads banners are injected into the homepage feed by `HomeController@injectAdsIntoBanners()`.
* Impresssions and clicks are logged in `smart_ads_tracking` (`ad_id`, `user_id`, `ip_address`).
* Expired ads are disabled daily by the `ads:expire` command.

### B. Custom Ads (Self-Serve Campaigns)
* Advertisers upload custom banner graphics via `/smart-ads/ads/create`.
* Campaign submissions require Admin review & approval (`CustomAdsRequestController@updateStatus`).
* Click tracking is processed through `/custom-ads/click` route updates.
