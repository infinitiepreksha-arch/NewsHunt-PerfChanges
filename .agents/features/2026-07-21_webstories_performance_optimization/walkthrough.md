# Web Stories Performance & Query Optimization Walkthrough

We have implemented query execution, setting model hydration blowup, and duplicate query optimizations for the **Web Stories Directory Page** (`/webstories`), **Single Web Story Reader Page** (`/webstories/{topic}/{story}`), and **Web Stories by Topic Page** (`/webstories/{topic}`).

---

## Final Performance Metrics Achieved

| Page / Route | Baseline Queries | Optimized Queries | Baseline Models | Optimized Models |
|---|---|---|---|---|
| **Web Stories Directory** (`/webstories`) | 15 Statements (2 duplicates) | **12 Statements** (0 duplicates) | 63 Models (40 Topic) | **25 Models** (3 Topic) |
| **Web Story Reader** (`/webstories/{topic}/{story}`) | 20 Statements (2 duplicates) | **17 Statements** (0 duplicates, incl. 2 updates) | 155 Models (146 Setting) | **12 Models** (0 Setting) |
| **Web Stories by Topic** (`/webstories/{topic}`) | 15 Statements | **14 Statements** (0 duplicates) | 51 Models (39 Topic) | **14 Models** (2 Topic) |

---

## Detailed Modifications

### [WebStory.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Http/Controllers/WebStory.php)

1. **Eliminated 146 Setting Model Hydrations:** Replaced `$socialsettings = Setting::pluck('value', 'name');` and `Setting::where('name', 'free_trial_story_limit')` with `$request->attributes` cached settings (`$settingsCache`), dropping setting model hydrations on story views from **146 models down to 0**.
2. **Request-Scoped Language Cache:** Saved resolved `$subscribedLanguageIds` into `$request->attributes->set('subscribed_language_ids', ...)` so `AppServiceProvider` reuses it, eliminating duplicate queries on `news_languages_subscribers`.
3. **In-Memory Filtered Topic Collection:** Replaced database query for `$filteredTopics` on `/webstories` with an in-memory pluck from the eagerly-loaded `$stories` collection (`$stories->pluck('topic')->filter()->unique('id')->values()`), eliminating 1 SQL query.
4. **Removed Unused Topic Query:** Removed `$topics = Topic::all()` from `storyByTopic()`, eliminating 1 SQL query and 39 unused `Topic` model hydrations.
5. **Selective Query Projections:** Constrained `Story::select(...)` and `Topic::select(...)` column fields across `index()`, `show()`, and `storyByTopic()` methods.

---

## Verification Results

1. **Syntax Verification:** Verified [WebStory.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Http/Controllers/WebStory.php) via `php -l` with 0 errors and 0 deprecations.
2. **Debugbar Verification:**
   - `/webstories`: **12 statements**, **0 duplicates**, **25 models**.
   - `/webstories/{topic}/{story}`: **17 statements** (incl. 2 update queries), **0 duplicates**, **12 models** (0 Setting models).
   - `/webstories/{topic}`: **14 statements**, **0 duplicates**, **14 models**.
