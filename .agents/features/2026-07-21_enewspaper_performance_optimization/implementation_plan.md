# E-Newspaper & PDF Viewer Performance & Query Optimization

Optimizing database query execution, Eloquent model hydration blowups, and duplicate queries on **E-Newspaper Page** (`/e-newspaper`), **E-Magazine Page** (`/e-magazine`), and **PDF Viewer Page** (`/e-newspaper/{id}/pdf`).

## Debugbar Trace Analysis

### E-Newspaper Page (`/e-newspaper`) Baseline
- **21 Statements (6 Duplicates)**, 169 Hydrated Models (**148 Setting Models Blowup!**, `6 ENewspaper`, `6 Topic`, `4 Channel`, `1 User`, `1 Role`, `1 Subscription`, `1 Transaction`, `1 NewsLanguage`).

### E-Newspaper PDF Viewer (`/e-newspaper/{id}/pdf`) Baseline
- **14 Statements (incl. 1 update)**, 9 Hydrated Models (`2 Setting`, `1 Subscription`, `1 User`, `1 Role`, `1 ENewspaper`, `1 Channel`, `1 Transaction`).

---

## Key Issues Identified from Debugbar Logs

1. **Setting Model Hydration Blowup (148 Setting Models on `/e-newspaper`):**
   - Line 116 of `ENewspaperFrontController.php` calls `$socialsettings = Setting::pluck('value', 'name');`, instantiating **148 full Eloquent Setting models** in memory.
   - Lines 40, 138, 175, 288, 320, 343 execute individual `Setting::where(...)` SQL queries.
2. **Redundant `$allEpapers` Full Table Scan for Filters:**
   - Lines 77-83 and 212-227 execute `$allEpapers = ENewspaper::with(['channel', 'topic'])->get();` which retrieves **ALL E-Newspaper records in the database**, firing 3 SQL queries and instantiating heavy `ENewspaper` models in RAM just to extract unique channel and topic filter dropdowns.
3. **Duplicate Subscribed News Languages Queries:**
   - Lines 22 and 157 execute `NewsLanguageSubscriber::where('user_id', $userId)`. `AppServiceProvider` line 116 executes the exact same query again.
4. **Duplicate Settings Queries in AppServiceProvider:**
   - `AppServiceProvider` line 95 executes `select name, value, updated_at from settings` because `$request->attributes['settings_cache']` is not populated yet.

---

## Proposed Changes

### Controllers & Business Logic

#### [MODIFY] [ENewspaperFrontController.php](file:///c:/Users/user/Downloads/Code%20-%20v1.4.9/app/Http/Controllers/ENewspaperFrontController.php)

1. **Request-Scoped Settings Cache:**
   - Replace `Setting::pluck('value', 'name')`, `Setting::where('name', ...)`, and `getENewsletterSettings()` with `$request->attributes` cached settings (`$settingsCache`).
   - Completely eliminates **148 Setting Eloquent model hydrations** and 4 SQL queries per request.
2. **Request-Scoped Language Cache:**
   - Store and reuse `$subscribedLanguageIds` in `$request->attributes->set('subscribed_language_ids', ...)`.
   - Eliminates duplicate `news_languages_subscribers` queries in `AppServiceProvider`.
3. **Targeted Channel & Topic Filter Queries:**
   - Replace full table scan `$allEpapers = ENewspaper::with(...)->get();` with targeted subqueries:
     ```php
     $epaperChannels = Channel::select('id', 'name', 'slug', 'logo')
         ->whereHas('eNewspapers', function ($q) use ($subscribedLanguageIds) {
             $q->whereIn('news_language_id', $subscribedLanguageIds)->where('type', 'paper');
         })->orderBy('name', 'asc')->get();

     $epapertopics = Topic::select('id', 'name', 'slug')
         ->whereHas('eNewspapers', function ($q) use ($subscribedLanguageIds) {
             $q->whereIn('news_language_id', $subscribedLanguageIds)->where('type', 'paper');
         })->orderBy('name', 'asc')->get();
     ```
   - Eliminates fetching all `ENewspaper` rows from the database.
4. **Resilient Wildcard Relationship Projections:**
   - Left wildcard selections on related models where appropriate to avoid unmigrated column errors (e.g. `language_code` vs `code` column in `news_languages` table), keeping the code highly robust.
