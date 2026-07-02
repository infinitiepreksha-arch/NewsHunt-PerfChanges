<?php
namespace App\Http\Controllers;

use App\Models\Channel;
use App\Models\ChannelSubscriber;
use App\Models\NewsLanguage;
use App\Models\NewsLanguageSubscriber;
use App\Models\Post;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ChannelFrontController extends Controller
{

    public function Index($channel = null)
    {
        $theme  = getTheme();
        $userId = Auth::user()->id ?? 0;

        $defaultImage = Setting::where('name', 'default_image')->first()->value ?? null;
        if ($userId) {
            $subscribedLanguageIds = NewsLanguageSubscriber::where('user_id', $userId)->pluck('news_language_id');
        } else {
            $sessionLanguageId = session('selected_news_language');
            if ($sessionLanguageId) {
                // If user selected a language, use it (even if not active)
                $subscribedLanguageIds = collect([$sessionLanguageId]);
            } else {
                // If not selected, use the first active language
                $defaultActiveLanguage = NewsLanguage::where('is_active', 1)->first();
                $subscribedLanguageIds = $defaultActiveLanguage ? collect([$defaultActiveLanguage->id]) : collect();
            }
        }
        if ($channel !== null) {
            $channelData       = Channel::where('slug', $channel)->firstOrFail();
            $channelData->logo = url('storage/images/' . $channelData->logo);

            $perPage = 15;

            $getChannelPosts = Post::select(
                'posts.id', 'posts.slug', 'posts.type', 'posts.video', 'posts.video_thumb',
                'posts.image', 'posts.comment',
                'channels.name as channel_name', 'channels.logo as channel_logo',
                'topics.name as topic_name', 'topics.slug as topic_slug',
                'posts.title', 'posts.favorite', 'posts.description',
                'posts.status', 'posts.publish_date', 'posts.pubdate'
            )
                ->join('channels', function ($join) {
                    $join->on('posts.channel_id', '=', 'channels.id')
                        ->where('channels.status', 'active');
                })
                ->leftjoin('topics', function ($join) {
                    $join->on('posts.topic_id', '=', 'topics.id')
                        ->where('topics.status', 'active');
                })
                ->where('posts.status', 'active')
                ->where('posts.channel_id', $channelData->id)
                ->when($subscribedLanguageIds->isNotEmpty(), function ($query) use ($subscribedLanguageIds) {
                    $query->whereIn('posts.news_language_id', $subscribedLanguageIds);
                })
                ->orderBy('posts.publish_date', 'desc')
                ->paginate($perPage);

            $post_count = Post::where('posts.channel_id', $channelData->id)
                ->when($subscribedLanguageIds->isNotEmpty(), function ($query) use ($subscribedLanguageIds) {
                    $query->whereIn('news_language_id', $subscribedLanguageIds);
                })
                ->count();

            foreach ($getChannelPosts as $post) {
                // For video/youtube posts, use video_thumb in the image field if image is empty
                if (in_array($post->type, ['video', 'youtube']) && empty($post->image) && ! empty($post->video_thumb)) {
                    $post->image = $post->video_thumb;
                }

                // Set default image if still empty
                $post->image = $post->image ?? $defaultImage;

                // Set default values for video fields
                $post->video_thumb = $post->video_thumb ?? $defaultImage;
                $post->video       = $post->video ?? $defaultImage;

                if ($post->publish_date) {
                    $post->publish_datee_news = Carbon::parse($post->publish_date)->format('Y-m-d H:i');
                    $post->publish_date       = Carbon::parse($post->publish_date)->diffForHumans();
                } elseif ($post->pubdate) {
                    $post->publish_datee_news = Carbon::parse($post->pubdate)->format('Y-m-d H:i');
                    $post->pubdate            = Carbon::parse($post->pubdate)->diffForHumans();
                }
            }

            $subscriber = Auth::check()
                ? ChannelSubscriber::where('channel_id', $channelData->id)->where('user_id', $userId)->first()
                : 'unauthorized';

            $title = $channelData->name;
            $data  = compact('title', 'channelData', 'getChannelPosts', 'subscriber', 'post_count', 'theme');

            return view('front_end/' . $theme . '/pages/channel-profile', $data);

        } else {
            $perPage = 12;
            $user    = Auth::user();

            $channelData = Channel::where('status', 'active')
                ->whereHas('posts', function ($query) use ($subscribedLanguageIds) {
                    if ($subscribedLanguageIds->isNotEmpty()) {
                        $query->whereIn('news_language_id', $subscribedLanguageIds);
                    }
                })
                ->withCount(['subscribers as is_followed' => function ($query) use ($user) {
                    $query->where('user_id', $user->id ?? '');
                }])
                ->paginate($perPage);

            $title = __('frontend-labels.channels.title');
            $data  = compact('title', 'channelData', 'theme');

            return view('front_end/' . $theme . '/pages/channels', $data);
        }
    }

    public function channelFollow(Channel $channel)
    {
        if (! Auth::check()) {
            return response()->json(['error' => true, 'message' => 'User not authenticated.'], 401);
        }

        $user         = Auth::user();
        $isSubscribed = $channel->subscribers()->where('user_id', $user->id)->exists();

        if ($isSubscribed) {
            $channel->subscribers()->detach($user->id);
            $channel->decrement('follow_count');
            $status  = "0";
            $message = __('frontend-labels.channels.unsubscribed_success');
        } else {
            $channel->subscribers()->attach($user->id);
            $channel->increment('follow_count');
            $status  = "1";
            $message = __('frontend-labels.channels.subscribed_success');
        }

        $updatedFollowCount = $channel->follow_count;
        return response()->json(['error' => false, 'status' => $status, 'count' => $updatedFollowCount, 'message' => $message]);
    }

}
