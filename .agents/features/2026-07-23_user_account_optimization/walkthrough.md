# User Account & Dashboard Performance Optimization Walkthrough

We have successfully optimized the **User Account Dashboard** page and all its subpages.

---

## Final Performance Metrics Achieved

| Page / Route | Baseline Queries | Optimized Queries | Baseline Models | Optimized Models |
|---|---|---|---|---|
| **User Profile** (`/my-account`) | 5 Statements | **4 Statements** | 2 Models | **2 Models** |
| **Followed Channels** (`/my-account/followings`) | 7 Statements | **6 Statements** | 4 Models | **4 Models** |
| **My Bookmarks** (`/my-account/bookmarks`) | 8 Statements | **6 Statements** | 3 Models | **3 Models** |
| **My Subscription** (`/my-account/subscription`) | 13 Statements | **7 Statements** | 28 Models | **5 Models** |
| **My Transactions** (`/my-account/transaction`) | 6 Statements | **5 Statements** | 3 Models | **3 Models** |

---

## Detailed Modifications

### [helper.php](file:///c:/Users/user/Downloads/Code - v1.4.9/app/Helpers/helper.php)
1. **Cache Active Theme Slug:** Wrapped theme query in `getTheme()` with `Cache::rememberForever('active_theme_slug')`.

### [AppServiceProvider.php](file:///c:/Users/user/Downloads/Code - v1.4.9/app/Providers/AppServiceProvider.php)
1. **Theme Cache Invalidation Observers:** Added saved/deleted model observers on `Theme` to clear `active_theme_slug` cache when admin updates themes.

### [FrontUserController.php](file:///c:/Users/user/Downloads/Code - v1.4.9/app/Http/Controllers/FrontUserController.php)
1. **Followed Channels Query:** Projected selective columns (`id`, `name`, `slug`, `logo`, `follow_count`) on the `Channel` relation pagination to avoid loading unused fields.
2. **Subscribed News Languages Cache:** Reused the cached `user_subscribed_languages_{userId}` key inside `favoritePosts()` to save 1 language subscriber query.
3. **Eager Load Subscription:** Restructured the `subscription` eager-load to load only essential columns from `feature` and `plan` relations (saving 2 queries on `plan_tenures` and `transactions` tables).
4. **Deleted Unused Query:** Removed the completely unused `Plan::with(...)` database query in `subscriptionDetails()`, saving 3 heavy queries.
5. **Cache Payment Setting:** Retreived currency formatting from `active_payment_setting` cache instead of raw DB to fix undefined variable warnings.
6. **Transactions Query:** Restricted fields queried on the `transactions` table to only those rendered.

### [Transaction.php](file:///c:/Users/user/Downloads/Code - v1.4.9/app/Models/Transaction.php)
1. **JSON Attribute Accessor:** Created a `plan_name` accessor that extracts the plan name from `plan_details` JSON metadata to prevent Column Not Found errors since the DB column never existed.
