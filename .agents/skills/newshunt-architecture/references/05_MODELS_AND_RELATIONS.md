# 05 - Eloquent Models & Relationship Mappings

## 1. Primary Model Architecture
The system contains 60+ Eloquent models in `app/Models/`. Models use standard Eloquent conventions, attribute accessors/mutators, and relationship definitions.

---

## 2. Model Deep Dives & Key Relationships

### A. Post Model (`App\Models\Post`)
* **Table:** `posts`
* **Casts:** `publish_date` => `datetime`
* **Accessors:**
  * `getVideoThumbAttribute()`: Automatically resolves YouTube cover images to medium quality (`mqdefault.jpg`), reducing initial load sizes by 2.2MB+.
* **Relationships:**
  * `belongsTo(Channel::class)`: Publisher channel.
  * `belongsTo(Topic::class)`: Post category.
  * `belongsTo(NewsLanguage::class)`: Content language.
  * `hasMany(Comment::class)`: Post comments.
  * `hasMany(Favorite::class)`: Saved user bookmarks.
  * `hasMany(PostImage::class)`: Multi-image gallery attachments.
  * `hasMany(PostLink::class)`: External link resources.

---

### B. Subscription Model (`App\Models\Subscription`)
* **Table:** `subscription`
* **Casts:** `duration` => `integer`, `start_date` => `date`, `end_date` => `date`
* **Key Methods:**
  * `scopeCurrentActive($query)`: Scopes query to active subscriptions where `start_date <= today`, `end_date >= today`, and `status = 'active'`.
  * `hasReachedPostLimits()`: Decodes `$this->transactions->plan_details['features'][0]['number_of_articles']` and returns `bool` if `article_count` meets or exceeds maximum allowed articles.
  * `hasReachedStoryLimits()`: Evaluates `story_count` against `number_of_stories`.
  * `hasReachedEPaperLimits()`: Evaluates `e_paper_count` against `number_of_e_papers_and_magazines`.
  * `incrementArticleCountWithValidation()`, `incrementStoryCountWithValidation()`, `incrementEPaperCountWithValidation()`: Safely increments usage count or throws an `\Exception` if limit thresholds are breached.
* **Relationships:**
  * `belongsTo(User::class)`
  * `belongsTo(Plan::class)`
  * `belongsTo(PlanTenure::class)`
  * `belongsTo(Transaction::class, 'transaction_id')`

---

### C. Story & StorySlide Models (`App\Models\Story`, `App\Models\StorySlide`)
* **Tables:** `stories`, `story_slides`
* **Relationships:**
  * `Story->hasMany(StorySlide::class)`: Ordered slide deck.
  * `Story->belongsTo(Topic::class)`
  * `Story->belongsTo(NewsLanguage::class)`
  * `StorySlide->belongsTo(Story::class)`

---

### D. User & Auth Models (`App\Models\User`, `App\Models\Admin`)
* **Tables:** `users`, `admins`
* **Traits:** `HasApiTokens` (Sanctum), `HasRoles` (Spatie Permission), `Notifiable`.
* **Relationships:**
  * `User->hasOne(Subscription::class)->where('status', 'active')`: Active subscription record.
  * `User->hasMany(Transaction::class)`: Payment history.
  * `User->hasMany(Favorite::class)`: Bookmarked posts list.
  * `User->belongsToMany(Channel::class, 'channel_subscribers')`: Followed publisher channels.
  * `User->belongsToMany(NewsLanguage::class, 'news_languages_subscribers')`: Subscribed content languages.

---

### E. Ad Models (`SmartAd`, `CustomAdsRequest`)
* **Relationships:**
  * `SmartAd->hasMany(SmartAdTracking::class)`
  * `SmartAd->hasMany(SmartAdPlacement::class)`
  * `CustomAdsRequest->belongsTo(User::class)`
  * `CustomAdsRequest->hasOne(CustomAdsPayment::class)`
  * `CustomAdsRequest->hasMany(CustomAdsTracking::class)`
