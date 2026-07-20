# 10 - Events, Queues, Commands & Schedules

## 1. Console Scheduling Kernel ([app/Console/Kernel.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Console/Kernel.php))

The system relies on Laravel's Artisan command scheduler to run background jobs automatically:

```php
protected function schedule(Schedule $schedule)
{
    $schedule->command('rss:fetch')->everyFifteenMinutes();
    $schedule->command('email:send-recent-posts')->everyFourHours();
    $schedule->command('expired:plan')->daily();
    $schedule->command('ads:expire')->daily();
}
```

---

## 2. Artisan Console Commands Matrix

| Command Signature | Command Class | Frequency / Trigger | Purpose |
|---|---|---|---|
| `rss:fetch` | `App\Console\Commands\FetchRssFeeds` | Every 15 minutes | Fetches registered RSS XML feeds, parses articles, downloads & compresses cover images to WebP. |
| `email:send-recent-posts` | `App\Console\Commands\SendRecentPostsEmail` | Every 4 hours | Sends bulk email summaries of recent news articles to subscribed users. |
| `expired:plan` | `App\Console\Commands\PlanExpiry` | Daily | Checks active subscriptions in `subscription` table; updates status to `'expired'` if `end_date < today`. |
| `ads:expire` | `App\Console\Commands\ExpireSmartAds` | Daily | Checks ad campaigns end dates and updates active statuses. |
| `images:compress` | `App\Console\Commands\CompressExistingImages` | On demand / Web route | Scans local image uploads, resizes width (max 800px), compresses to 60% quality WebP, and updates DB paths. |

---

## 3. Queue Architecture & Workers
* **Queue Drivers:** Supports standard `database`, `redis` (monitored via `laravel/horizon`), and `rabbitmq` (`vladimir-yuldashev/laravel-queue-rabbitmq`).
* **Queue Supervisor:** Background daemon workers execute queued email dispatches and push notification notifications (`php artisan queue:work --tries=3`).
