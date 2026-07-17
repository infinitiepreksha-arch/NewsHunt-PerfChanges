<?php
namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\NewsLanguage;
use App\Models\NewsLanguageSubscriber;
use App\Models\Post;
use App\Models\PostView;
use App\Models\Setting;
use App\Models\Topic;
use App\Traits\SelectsFields;
use Carbon\Carbon;
use DevDojo\LaravelReactions\Models\Reaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;

class PostDetailController extends Controller
{
    use SelectsFields;
    /**
     * Display a listing of the resource.
     */
    const TIME_FORMATE = 'Y-m-d H:i';
    public function index($slug)
    {
        // Cache reactions seeding check to avoid querying on every request
        $reactionCount = \Illuminate\Support\Facades\Cache::rememberForever('reaction_count_seeded', function () {
            $count = Reaction::count();
            if ($count === 0) {
                Artisan::call('db:seed', [
                    '--class' => 'ReactionsTableSeeder',
                    '--force' => true,
                ]);
                return Reaction::count();
            }
            return $count;
        });

        // Use system settings cache
        $defaultImageVal = \App\Services\CachingService::getSystemSettings('default_image') ?: 'public/front_end/classic/images/default/post-placeholder.jpg';
        $defaultImage = (object) ['value' => $defaultImageVal];

        $post         = Post::with('images')
            ->select($this->selectPostDescriptionFields())
            ->join('channels', 'posts.channel_id', '=', 'channels.id')
            ->leftJoin('topics', 'posts.topic_id', '=', 'topics.id')
            ->where('posts.slug', $slug)
            ->firstOrFail();

        // Get main image: post image > first gallery image > default placeholder
        $mainImage = $post->image ?? ($post->images->first()?->image) ?? asset($defaultImage->value ?? 'public/front_end/classic/images/default/post-placeholder.jpg');

        $galleryImages = $post->images
            ->pluck('image')                         // all gallery images
            ->filter(fn($img) => $img != $mainImage) // remove main image if present
            ->values()                               // reset array keys
            ->toArray();
            
        $postImages = array_merge([$mainImage], $galleryImages);

        $userId   = auth()->check() ? auth()->user()->id : null;
        $image    = $post->image;

        // Guest Favorites Bypass
        if ($userId) {
            $bookmark = Favorite::where('user_id', $userId)
                ->where('post_id', $post->id)
                ->first();
            $post->is_bookmark = $bookmark ? 1 : 0;
        } else {
            $post->is_bookmark = 0;
        }

        $getReactCountsData = $post->getReactionsSummary();

        $getReactCounts = $getReactCountsData->sortByDesc(function ($reaction) {
            return $reaction->count;
        });
        $getTopReactions = $getReactCounts->take(3);
        $emoji           = "";
        $reactionUsers   = [];

        // Fetch all reactions once for loop optimization
        $reactionsList = Reaction::all();

        foreach ($getReactCounts as $getReractCount) {
            $reaction             = $reactionsList->firstWhere('name', $getReractCount->name);
            $getReractCount->uuid = $reaction->uuid ?? '';

            $reactionUsers[$getReractCount->name] = [];

            foreach ($post->reactions as $reactor) {
                $userDetails  = $reactor->getResponder();
                $getEmoji     = $reactor->uuid;
                $reactionName = $reactor->name;
                $user_id      = $userDetails->id ?? 0;

                if ($getReractCount->name === $reactionName) {
                    $reactionUsers[$getReractCount->name][] = $userDetails;
                }
                if ($userId && $userId == $user_id) {
                    $emoji = $getEmoji;
                }
            }

            $getReractCount->users = $reactionUsers[$getReractCount->name];
        }

        /* Manage Post view count */
        $this->viewCount($post);

        if ($post->publish_date) {
            $post->publish_date_news = Carbon::parse($post->pubdate)->format(self::TIME_FORMATE);
            $post->publish_date      = Carbon::parse($post->publish_date)->diffForHumans();
        } elseif ($post->pubdate) {
            $post->pubdate = Carbon::parse($post->pubdate)->diffForHumans();
        }
        $post->channel_logo = url('storage/images/' . $post->channel_logo);

        $topics = Topic::select('id', 'name', 'slug')
            ->where('status', 'active')
            ->take(5)
            ->get();

        // Restrict prev/next queries to specific columns
        $previousPost = Post::select('id', 'title', 'slug', 'image', 'video_thumb', 'type')
            ->where('id', '<', $post->id)
            ->orderBy('id', 'desc')
            ->first();
        $nextPost     = Post::select('id', 'title', 'slug', 'image', 'video_thumb', 'type')
            ->where('id', '>', $post->id)
            ->orderBy('id')
            ->first();

        if ($userId) {
            $subscribedLanguageIds = NewsLanguageSubscriber::where('user_id', $userId)->pluck('news_language_id');
        } else {
            $sessionLanguageId = session('selected_news_language');
            if ($sessionLanguageId) {
                $subscribedLanguageIds = collect([$sessionLanguageId]);
            } else {
                $defaultActiveLanguage = NewsLanguage::where('is_active', 1)->first();
                $subscribedLanguageIds = $defaultActiveLanguage ? collect([$defaultActiveLanguage->id]) : collect();
            }
        }

        $relatedPosts = Post::select($this->selectPostDescriptionFields())
            ->join('channels', 'posts.channel_id', '=', 'channels.id')
            ->leftjoin('topics', 'posts.topic_id', '=', 'topics.id')
            ->where('topics.name', $post->topic_name)
            ->where('posts.slug', '!=', $slug)
            ->whereNotIn('posts.id', [$previousPost->id ?? null, $nextPost->id ?? null])
            ->whereIn('posts.news_language_id', $subscribedLanguageIds) // <-- language filter
            ->orderBy('posts.publish_date', 'desc')
            ->take(4)
            ->get()
            ->map(function ($item) use ($defaultImage) {
                $item->image             = $item->image ?? url($defaultImage->value ?? 'public/front_end/classic/images/default/post-placeholder.jpg');
                $item->publish_date_news = Carbon::parse($item->pubdate)->format(self::TIME_FORMATE);
                if ($item->publish_date) {
                    $item->publish_date = Carbon::parse($item->publish_date)->diffForHumans();
                } elseif ($item->pubdate) {
                    $item->pubdate = Carbon::parse($item->pubdate)->diffForHumans();
                }
                return $item;
            });

        $post_label_val  = \App\Services\CachingService::getSystemSettings('news_label_place_holder');
        $post_label      = (object) ['value' => $post_label_val];
        $reactions       = $reactionsList;
        $title           = "{$post->title} | {$post->topic_name}";
        $post_title      = $post->title;
        $description     = $post->description;
        $postVisitLimit  = \App\Services\CachingService::getSystemSettings('free_trial_post_limit') ?? 10;
        $storyVisitLimit = \App\Services\CachingService::getSystemSettings('free_trial_post_limit') ?? 10;
        $theme           = getTheme();

        $user                     = auth()->user();
        $subscription             = $user ? $user->subscription : null;
        $subscriptionLimitReached = false;
        $dailyLimitReached        = false; // This will now be handled primarily in JS

        $freeTrialLimit = (int) (\App\Services\CachingService::getSystemSettings('free_trial_post_limit') ?? 10);
        $isDailyLimitEligible = false;

        if ($subscription) {
            if ($subscription->hasReachedPostLimits()) {
                $subscriptionLimitReached = true;
                // If subscription limit is reached, they fall back to the daily free trial limit
                $isDailyLimitEligible = true;
            } else {
                // Subscription is active and has remaining count
                $subscription->incrementArticleCountWithValidation(1);
            }
        } else {
            // Guests or users without subscription are eligible for daily limit
            $isDailyLimitEligible = true;
        }

        $settings = \App\Services\CachingService::getSystemSettings();
        return view("front_end/" . $theme . "/pages/post-detail-page", compact('title', 'reactions', 'defaultImage', 'emoji', 'getTopReactions', 'settings', 'post', 'relatedPosts', 'topics', 'previousPost', 'nextPost', 'post_label', 'postImages', 'theme', 'image', 'post_title', 'description', 'freeTrialLimit', 'isDailyLimitEligible', 'dailyLimitReached', 'subscriptionLimitReached','mainImage'));
    }

    public function getRandomAd()
    {
        $ad = DB::table('smart_ad_placements as sap')
            ->join('smart_ads as sa', 'sap.smart_ad_id', '=', 'sa.id')
            ->join('smart_ads_details as sad', 'sap.smart_ad_id', '=', 'sad.smart_ad_id')
            ->where('sap.placement_key', 'post_detail')
            ->inRandomOrder()
            ->select(
                'sa.id as smart_ad_id',
                'sa.name',
                'sa.slug',
                'sa.body',
                'sa.adType as ad_type',
                'sa.vertical_image',
                'sa.horizontal_image',
                'sa.imageUrl',
                'sa.imageAlt as image_alt',
                'sa.views',
                'sa.clicks',
                'sa.created_at',
                'sad.contact_name',
                'sad.contact_email',
                'sad.contact_phone'
            )
            ->first();

        if ($ad) {
            // increment views
            DB::table('smart_ads')
                ->where('id', $ad->smart_ad_id)
                ->increment('views');

            return response()->json([
                "id"               => "ad_" . $ad->smart_ad_id,
                "smart_ad_id"      => $ad->smart_ad_id,
                "type"             => "ad",
                "name"             => $ad->name,
                "title"            => $ad->name,
                "description"      => $ad->body,
                "body"             => $ad->body,
                "vertical_image"   => $ad->vertical_image ? url('storage/' . $ad->vertical_image) : null,
                "horizontal_image" => $ad->horizontal_image ? url('storage/' . $ad->horizontal_image) : null,
                "image_alt"        => $ad->image_alt,
                "imageUrl"         => $ad->imageUrl,
                "ad_type"          => $ad->ad_type,
                "slug"             => $ad->slug,
                "views"            => $ad->views + 1,
                "clicks"           => $ad->clicks,
                "contact_info"     => [
                    "name"  => $ad->contact_name,
                    "email" => $ad->contact_email,
                    "phone" => $ad->contact_phone,
                ],
                "created_at"       => $ad->created_at,
                "publish_date"     => \Carbon\Carbon::parse($ad->created_at)->diffForHumans(),
            ]);
        }

        return response()->json(null);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function favorteToggle(Request $request)
    {
        if (! Auth::check()) {
            return response()->json([
                'error'   => true,
                'message' => 'Unauthorized user.',
            ], 401);
        }

        $validatedData = $request->validate([
            'id' => ['required', 'exists:posts,id'],
        ]);

        $postId = $validatedData['id'];
        $userId = auth()->user()->id;

        $post = Post::findOrFail($postId);

        $favorite = Favorite::where('user_id', $userId)
            ->where('post_id', $postId)
            ->first();

        if ($favorite) {
            // Unlike the post
            if ($post->favorite > 0) {
                $favorite->delete();
                $post->decrement('favorite');
                $status         = '0';
                $bookmark_count = Favorite::where('post_id', $postId)->count();
                $message        = __('frontend-labels.bookmarks.removed_success');
            }
        } else {
            Favorite::create([
                'user_id' => $userId,
                'post_id' => $postId,
            ]);
            $post->increment('favorite');
            $status         = '1';
            $bookmark_count = Favorite::where('post_id', $postId)->count();
            $message        = __('frontend-labels.bookmarks.added_success');
        }
        return response()->json([
            'error'   => false,
            'status'  => $status,
            'postId'  => $postId,
            'count'   => $bookmark_count,
            'message' => $message,
        ], 201);

    }

    public function viewCount($post)
    {

        $user_id    = Auth::user()->id ?? null;
        $cookieName = 'viewed_post_' . $post->id;

        $viewexist = PostView::where('post_id', $post->id)
            ->where('user_id', $user_id)
            ->first();

        if (! $viewexist) {
            if (! Cookie::has($cookieName)) {
                if ($user_id !== null) {
                    PostView::create([
                        'post_id' => $post->id,
                        'user_id' => $user_id,
                    ]);
                }
                Cookie::queue($cookieName, true, 21600);
                $post->increment('view_count');
                return $post;
            } else {
                if ($user_id !== null) {
                    PostView::create([
                        'post_id' => $post->id,
                        'user_id' => $user_id,
                    ]);
                }
                return $post;
            }
        } else {
            return $post;
        }

    }

}
