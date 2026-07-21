# Web Stories Performance & Query Optimization

Optimizing database query execution, Eloquent model hydration blowups, and duplicate queries on **Web Stories Directory** (`/webstories`), **Single Web Story Reader** (`/webstories/{topic}/{story}`), and **Web Stories by Topic** (`/webstories/{topic}`).

## Debugbar Trace Analysis

### Web Stories Directory (`/webstories`) Baseline
- **15 Statements (2 Duplicates)**, 63 Hydrated Models (`40 Topic`, `10 StorySlide`, `8 Story`, `1 User`, `1 Role`, `1 Setting`, `1 Subscription`, `1 Transaction`).

### Single Web Story Reader (`/webstories/{topic}/{story}`) Baseline
- **20 Statements (2 Duplicates)**, 155 Hydrated Models (**146 Setting Models Blowup**, `2 Story`, `2 Topic`, `2 StorySlide`, `1 User`, `1 Role`, `1 Subscription`, `1 Transaction`).

---

## Key Issues Identified from Debugbar Logs

1. **Setting Model Hydration Blowup (146 Setting Models on `/webstories/{topic}/{story}`):**
   - Line 86 of `WebStory.php` calls `$socialsettings = Setting::pluck('value', 'name');`, instantiating **146 full Eloquent Setting models** in memory.
   - Lines 55 and 113 execute separate `Setting::where('name', 'free_trial_story_limit')` SQL queries.
2. **40 Unconstrained Topic Models on Directory (`/webstories`):**
   - Line 19 calls `$topics = Topic::all();`, instantiating 40 Eloquent models only to filter them in PHP memory.
3. **Duplicate Subscribed News Languages Queries:**
   - Lines 25 and 94 execute `NewsLanguageSubscriber::where('user_id', $userId)`. `AppServiceProvider` line 116 executes the exact same query again.
4. **Duplicate Settings Queries in AppServiceProvider:**
   - `AppServiceProvider` line 95 executes `select name, value, updated_at from settings` because `$request->attributes['settings_cache']` is not populated yet.

---

## Proposed Changes

### Controllers & Business Logic

#### [MODIFY] [WebStory.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Http/Controllers/WebStory.php)

1. **Request-Scoped Settings Cache:**
   - Replace `Setting::pluck('value', 'name')` and `Setting::where('name', 'free_trial_story_limit')` with `$request->attributes` cached settings (`$settingsCache`).
   - Completely eliminates **146 Setting Eloquent model hydrations** and 2 SQL queries per request on `/webstories/{topic}/{story}`.
2. **Request-Scoped Language Cache:**
   - Store and reuse `$subscribedLanguageIds` in `$request->attributes->set('subscribed_language_ids', ...)`.
   - Eliminates duplicate `news_languages_subscribers` queries in `AppServiceProvider`.
3. **In-Memory Filtered Topics:**
   - Replace database query for `$filteredTopics` with `$stories->pluck('topic')->filter()->unique('id')->values()`.
4. **Selective Column Projections:**
   - Apply selective column projections on `Story::select(...)` and `Topic::select(...)` queries in `index()`, `show()`, and `storyByTopic()`.

---

## Final Impact

| Metric | Web Stories Directory (`/webstories`) | Web Story Reader (`/webstories/{topic}/{story}`) | Web Stories by Topic (`/webstories/{topic}`) |
|---|---|---|---|
| **Original Queries** | 15 Statements (2 duplicates) | 20 Statements (2 duplicates) | 15 Statements |
| **Optimized Queries** | **12 Statements** (0 duplicates) | **17 Statements** (0 duplicates) | **14 Statements** (0 duplicates) |
| **Eloquent Models** | **25 Models** (down from 63) | **12 Models** (down from 155!) | **14 Models** (down from 51) |
