# Videos & Audios Pages Performance Optimization

This plan optimizes database query count, model hydrations, and duplicate database lookups on the **Videos Page** (`/videos`) and **Audios Page** (`/audios`).

## Proposed Changes

### [Video & Audio Components]

#### [MODIFY] [VideoController.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Http/Controllers/VideoController.php)
1. **Initialize Settings Cache:** Call `$this->getSettingsCache($request)` to populate `$request->attributes` with cached global settings, eliminating the `select name, value, updated_at from settings` query inside `AppServiceProvider`.
2. **Reuse Language Cache:** Check and reuse cached subscriber language IDs using request attributes (`$request->attributes->get('subscribed_language_ids')`), preventing duplicate language lookup queries.
3. **Delete Unused Query:** Completely remove the `$topicIds = Post::where('type', 'video')->...->pluck('topic_id')` query since `$topicIds` is not passed to the view or used anywhere in `VideoController`.
4. **Selective Column Projections:** 
   - Restrict the primary query to selective columns: `id`, `title`, `slug`, `video_thumb`, `comment`, `view_count`, `publish_date`, `pubdate`, `channel_id`.
   - Bypassed redundant `topic` relationship eager-loading since it is not used in the template.
   - Project eager-loaded channel relations to selective columns: `['channel' => fn($q) => $q->select('id', 'name', 'slug', 'logo')]`.

#### [MODIFY] [AudioController.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Http/Controllers/AudioController.php)
1. **Initialize Settings Cache:** Reuse request attributes settings cache to avoid duplicate settings queries.
2. **Reuse Language Cache:** Share and check cached subscriber news language IDs.
3. **Optimize Filter Topics Query:** Replace the heavy plucking table scan on `Post` with a targeted relationship existence subquery:
   ```php
   $topics_for_filter = Topic::select('id', 'name', 'slug')
       ->whereHas('posts', function ($q) use ($subscribedLanguageIds) {
           $q->where('status', 'active')
             ->where('type', 'audio')
             ->when($subscribedLanguageIds->isNotEmpty(), function ($query) use ($subscribedLanguageIds) {
                 $query->whereIn('news_language_id', $subscribedLanguageIds);
             });
       })->get();
   ```
4. **Selective Column Projections:**
   - Project primary posts query: `id`, `title`, `slug`, `image`, `comment`, `view_count`, `publish_date`, `pubdate`, `channel_id`, `topic_id`.
   - Project eager loaded relationships: `['topic' => fn($q) => $q->select('id', 'name', 'slug'), 'channel' => fn($q) => $q->select('id', 'name', 'slug', 'logo')]`.

---

## Verification Plan

### Manual Verification
1. Open `/videos` and `/audios` pages.
2. Confirm pages render perfectly without layout shifts or missing details.
3. Inspect Debugbar to verify:
   - settings query `select name, value, updated_at from settings` is removed.
   - duplicate language subscribers lookup query is removed.
   - total statements and hydrated model counts are significantly reduced.
