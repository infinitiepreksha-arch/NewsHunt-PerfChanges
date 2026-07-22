# Videos & Audios Performance Optimization Walkthrough

We have successfully optimized the **Videos Directory** (`/videos`) and **Audios Directory** (`/audios`) pages.

---

## Final Performance Metrics Achieved

| Page / Route | Baseline Queries | Optimized Queries | Baseline Models | Optimized Models |
|---|---|---|---|---|
| **Videos Directory** (`/videos`) | 7 Statements | **5 Statements** (0 duplicates) | 13 Models | **10 Models** |
| **Audios Directory** (`/audios`) | 8 Statements | **7 Statements** (0 duplicates) | 4 Models | **3 Models** |

---

## Detailed Modifications

### [VideoController.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Http/Controllers/VideoController.php)
1. **Settings Cache Integration:** Loaded settings from `$request->attributes` using `getSettingsCache()`, saving the `select name, value, updated_at from settings` query in `AppServiceProvider`.
2. **Language Cache Integration:** Subscribed news language lookups are cached on request attributes.
3. **Removed Unused Query:** Deleted the plucking query of `$topicIds` from `Post` table as it was never referenced or passed to the view.
4. **Selective Column Projections:** Restricted `Post` query to only required columns (`id`, `title`, `slug`, `video_thumb`, `comment`, `view_count`, `publish_date`, `pubdate`, `channel_id`) and eager-loaded only channel logos and slugs.

### [AudioController.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Http/Controllers/AudioController.php)
1. **Settings & Language Caching:** Reused request attribute caching systems.
2. **Subquery Existence Topics Filter:** Swapped the heavy pluck-based table scan on `Post` with a clean `whereHas('posts', ...)` query on `Topic`.
3. **Selective Column Projections:** Selective columns on both `Post` and relation queries to fetch only the fields rendered in the blade templates.
