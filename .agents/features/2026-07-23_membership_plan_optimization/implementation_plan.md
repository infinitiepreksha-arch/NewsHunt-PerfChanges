# Membership Plan Page Performance Optimization

This plan optimizes the database queries and memory overhead on the Membership Plan Page (`/membership`) for both logged-in and guest visitors.

## Proposed Changes

### [MembershipController]

#### [MODIFY] [MembershipController.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Http/Controllers/MembershipController.php)
1. **Cache Active Payment Setting:** Cache the active payment setting query using `Cache::rememberForever` to avoid querying the `payment_settings` table on every visit.
2. **Use Cached Settings Service:** Swap the direct database pluck query for `free_trial_status` with `CachingService::getSystemSettings('free_trial_status')`.
3. **Eager Load User Subscription:** Eager-load the `subscription` relationship on the authenticated user model to prevent lazy-loading in the Blade template.

---

### [AppServiceProvider]

#### [MODIFY] [AppServiceProvider.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Providers/AppServiceProvider.php)
1. **Add Model Observers for Cache Invalidation:**
   - Invalidate the cached active payment setting (`active_payment_setting`) when any `PaymentSetting` is saved or deleted.
   - Invalidate the cached user subscribed news languages (`user_subscribed_languages_{userId}`) when a `NewsLanguageSubscriber` is saved or deleted.
   - Invalidate the cached View Composer settings list (`view_composer_settings_list`) when a `Setting` is saved or deleted.
2. **Cache View Composer Settings Query:** Cache the raw settings table query used to populate the View Composer's configuration array.
3. **Cache User Subscribed Language IDs:** Cache the user's subscribed language IDs query for 1 hour to prevent hitting the `news_languages_subscribers` table on every request.

---

## Verification Plan

### Automated Tests
- Run `php -l app/Http/Controllers/MembershipController.php` to verify PHP syntax.
- Run `php -l app/Providers/AppServiceProvider.php` to verify PHP syntax.

### Manual Verification
1. **Guest Visitor Verification:**
   - Access `/membership` as a logged-out user.
   - Verify that all plans, features, and buy buttons (linking to login/register modal) render correctly.
   - Verify Debugbar metrics: Queries should drop from **7** to **4**.
2. **Logged-In User Verification:**
   - Log in and access `/membership`.
   - Verify that the subscription status is checked correctly.
   - Verify Debugbar metrics: Queries should drop from **13** to **9**.
3. **Cache Invalidation Verification:**
   - Change a payment setting in the admin panel and verify it invalidates the cache instantly.
   - Update user language preferences in settings and verify it invalidates the language preference cache instantly.
