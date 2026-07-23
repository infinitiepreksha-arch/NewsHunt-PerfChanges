# Channels Directory & Single Channel Profile Performance Optimization

Optimizing database query execution, Eloquent model hydrations, and duplicate queries on both the **Channels Directory Page** (`/channels`) and **Single Channel Profile Page** (`/channels/{slug}`).

## Debugbar Trace Analysis

### Channels Directory (`/channels`) Baseline
- **11 Queries (2 Duplicates)**, 7 Hydrated Models.

### Single Channel Profile (`/channels/{slug}`) Baseline
- **14 Queries (2 Duplicates)**, 20 Hydrated Models (`15 Post`, `1 User`, `1 Role`, `1 Setting`, `1 Channel`, `1 ChannelSubscriber`).

### Key Issues Identified from Debugbar Logs:
1. **Duplicate `settings` Query & Model Hydration:** `ChannelFrontController.php` line 21 queries `settings` for `default_image`, instantiating 1 `Setting` Eloquent model. `AppServiceProvider` line 95 queries `settings` again.
2. **Duplicate Subscribed News Languages Query:** `ChannelFrontController.php` line 23 queries `news_languages_subscribers`. `AppServiceProvider` line 116 queries `news_languages_subscribers` again.
3. **Redundant `$post_count` Database Query (Line 69):** `ChannelFrontController.php` line 69 executes `select count(*) as aggregate from posts...` to populate `$post_count`. The paginator on line 63 ALREADY executes `count(*)` and provides `$getChannelPosts->total()`.
4. **Unauthenticated Guest Subquery Overhead:** On `/channels`, `withCount(['subscribers as is_followed' => ...])` executes a subquery `where user_id = ''` for guest visitors.
5. **Missing Column Aliases:** `Post::select(...)` on `/channels/{slug}` is missing `channels.slug as channel_slug`.

---

## Proposed Changes

### Controllers & Business Logic

#### [MODIFY] [ChannelFrontController.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Http/Controllers/ChannelFrontController.php)

1. **Request-Scoped Settings Cache:** Retrieve `default_image` from `$request->attributes->get('settings_cache')` stdClass objects. Eliminates `Setting` model hydration and 1 SQL statement.
2. **Request-Scoped Language Cache:** Store and reuse `$subscribedLanguageIds` in `$request->attributes` bag, eliminating duplicate `news_languages_subscribers` query in `AppServiceProvider`.
3. **Channel Profile (`/channels/{slug}`):**
   - Selective column selection: `Channel::select('id', 'name', 'slug', 'logo', 'description', 'follow_count', 'status')->where('slug', $channel)->firstOrFail()`.
   - Add `channels.slug as channel_slug` in `Post::select(...)` column list.
   - Remove redundant `Post::where(...)->count()` query on line 69; replace with `$post_count = $getChannelPosts->total()`.
4. **Channels Directory (`/channels`):**
   - Selective column selection: `Channel::select('id', 'name', 'slug', 'logo', 'description', 'follow_count', 'status')`.
   - Wrap `withCount(['subscribers as is_followed' => ...])` in `$user ? ... : ...` check to bypass subquery execution for guests.

---

## Expected Impact

| Metric | Channels Page (`/channels`) | Single Channel (`/channels/{slug}`) |
|---|---|---|
| **Original Queries** | 11 Statements (2 duplicates) | 14 Statements (2 duplicates) |
| **Optimized Queries** | **7 Statements** (0 duplicates) | **9 Statements** (0 duplicates) |
| **Eloquent Models** | **6 Models** (0 Setting models) | **19 Models** (0 Setting models) |

---

## Verification Plan

### Automated / Manual Verification
1. Open `/channels` in browser with Debugbar active:
   - Confirm queries drop from 11 to 7 statements with 0 duplicates.
   - Confirm Setting models count drops to 0.
2. Open `/channels/abp-livee` (or any channel slug) in browser with Debugbar active:
   - Confirm queries drop from 14 to 9 statements with 0 duplicates.
   - Confirm `$post_count` displays accurately in channel header.
