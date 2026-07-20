# 07 - Controller Execution Call Chains & Flow Maps

## 1. Web Homepage Data Flow Map

```
Client Requests GET / 
  │
  ├──► HomeController@index
  │      │
  │      ├──► Check request()->attributes for 'settings_cache'
  │      │      ├── [Hit]  Read settings collection from request attributes
  │      │      └── [Miss] Query DB (DB::table('settings')->select(...)) & store in request
  │      │
  │      ├──► Resolve Active News Languages (Request Attributes check)
  │      │
  │      ├──► Execute Consolidated Feed Query (Top 32 Posts)
  │      │      └── Filter: unique('title') -> shuffle() in PHP memory
  │      │
  │      ├──► Slice Shuffled Feed Collection
  │      │      ├── $postBanners Raw -> injectAdsIntoBanners()
  │      │      ├── $top_posts (Top horizontal carousel)
  │      │      ├── $sidebarPosts
  │      │      └── $latesNews
  │      │
  │      ├──► Query Personal Feeds (Followed Channels)
  │      │      └── Exclude displayed global post IDs via whereNotIn('posts.id', $displayedIds)
  │      │
  │      └──► Render view("front_end/classic/pages/index", ...)
```

---

## 2. Web Story Slider & Paywall Execution Flow

```
Client Requests GET /webstories/{topic}/{story}
  │
  ├──► WebStory@show
  │      │
  │      ├──► Verify Story Topic ID & Language Match (Abort 404 if invalid)
  │      │
  │      ├──► Subscription Paywall Threshold Check
  │      │      ├── Is User Subscribed? 
  │      │      │     ├── YES: $subscription->hasReachedStoryLimits()
  │      │      │     │          ├── Limit Exceeded -> $subscriptionLimitReached = true
  │      │      │     │          └── Limit OK       -> $subscription->incrementStoryCountWithValidation(1)
  │      │      │     └── NO : Apply $freeTrialLimit fallback setting
  │      │
  │      ├──► Story View Anti-Spam Cookie Check (storyCount)
  │      │      ├── Check Cookie::has('viewed_story_' . $story->id)
  │      │      │     ├── YES: Skip database increment
  │      │      │     └── NO : Cookie::queue(..., 21600) -> $story->increment('story_count')
  │      │
  │      ├──► Resolve Next Story Deck (Query next story by ID with story_slides)
  │      │
  │      └──► Render view("front_end/classic/pages/webstory_slide", ...)
```

---

## 3. Advanced Multi-Table Union Search Flow

```
Client Submits GET /posts?search=query
  │
  ├──► SearchPostController@search
  │      │
  │      ├──► Build Scoped Subqueries based on Selected Filters:
  │      │      ├── Posts Table (Articles, Custom Videos, YouTube, Audio formats)
  │      │      ├── Stories Table (Web Stories)
  │      │      └── ENewspapers Table (Newspapers & Magazines)
  │      │
  │      ├──► Merge Subqueries using DB::table()->unionAll()
  │      │
  │      ├──► Apply Topic, Channel, and Date Sort Orders
  │      │
  │      ├──► Detect Request Type
  │      │      ├── [AJAX Request] -> Return JSON payload { html: search_result_posts.blade }
  │      │      └── [Full Request] -> Render view("front_end/classic/pages/search-result")
  │      │
  │      └──► Client JS (search-news.js) updates pushState URL & scrolls smoothly to top
```
