# User Account Dashboard Page Performance Optimization

This plan optimizes the database queries and memory overhead on the User Account Profile Page (`/my-account`) and all its subpages: followed channels (`/my-account/followings`), favorites (`/my-account/bookmarks`), subscription details (`/my-account/subscription`), and transaction details (`/my-account/transaction`).

## Proposed Changes

### [Theme Helper]

#### [MODIFY] [helper.php](file:///c:/Users/user/Downloads/Code - v1.4.9/app/Helpers/helper.php)
1. **Cache Active Theme Slug:** Wrap the `Theme::select('slug')->where('is_default', '1')->first()` query inside a `Cache::rememberForever('active_theme_slug')` call to completely eliminate the theme database query from every page request across the site.

---

### [FrontUserController]

#### [MODIFY] [FrontUserController.php](file:///c:/Users/user/Downloads/Code - v1.4.9/app/Http/Controllers/FrontUserController.php)
1. **Paginate Followed Channels with Selective Columns:** Update `followingsChannels()` to project only required columns (`id`, `name`, `slug`, `logo`, `follow_count`) on the `Channel` relation query, preventing the retrieval of heavy text columns.
2. **Re-use Subscribed News Language Cache:** Update `favoritePosts()` to pull the user's subscribed language IDs from the `user_subscribed_languages_{userId}` cache instead of triggering a fresh database query.
3. **Optimize Subscription Details Queries:**
   - Eager load only the essential `plan` and `feature` relationships on `subscription` (eliminating unused queries on `plan_tenures` and `transactions` tables).
   - Use the cached `'active_payment_setting'` to retrieve Stripe/Razorpay currency symbols and prevent Undefined Variable PHP warnings for `$paymentSetting`.
   - Remove the completely unused `Plan::with(...)` database query (saving 3 redundant queries).
4. **Selective Column Projection on Transactions:** Update `transactionDetails()` to project only rendered columns (`id`, `plan_details`, `transaction_id`, `amount`, `created_at`, `status`, `user_id`) from the `transactions` table.

---

### [AppServiceProvider]

#### [MODIFY] [AppServiceProvider.php](file:///c:/Users/user/Downloads/Code - v1.4.9/app/Providers/AppServiceProvider.php)
1. **Clear Theme Cache on Save/Delete:** Add observers on `Theme` model to invalidate the `active_theme_slug` cache when any theme is modified or deleted in the admin dashboard.

---

## Verification Plan

### Automated Tests
- Run `php -l app/Helpers/helper.php` to verify PHP syntax.
- Run `php -l app/Http/Controllers/FrontUserController.php` to verify PHP syntax.
- Run `php -l app/Providers/AppServiceProvider.php` to verify PHP syntax.

### Manual Verification
1. **User Dashboard (`/my-account`):**
   - Access the main dashboard as an authenticated user.
   - Verify Debugbar metrics: Queries should drop from **5** to **4**.
2. **Followed Channels (`/my-account/followings`):**
   - Access `/my-account/followings` and verify pagination and unfollow functionality.
   - Verify Debugbar metrics: Queries should drop to **5** (4 user auth + 1 count query + 1 channel paginate query).
3. **My Bookmarks (`/my-account/bookmarks`):**
   - Access `/my-account/bookmarks` and verify pin/unpin and layout.
   - Verify Debugbar metrics: Queries should drop to **6** (4 user auth + 1 count + 1 paginate, saving subscriber query).
4. **Subscription Details (`/my-account/subscription`):**
   - Access `/my-account/subscription`.
   - Verify subscription cards, progress bars, and remaining limits render correctly.
   - Verify Debugbar metrics: Queries should drop from **10+** down to **6**.
5. **Transaction Details (`/my-account/transaction`):**
   - Access `/my-account/transaction` and verify transactions display correctly.
   - Verify Debugbar metrics: Queries should drop to **5** (4 user auth + 1 transaction query).
