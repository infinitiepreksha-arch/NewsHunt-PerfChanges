<?php
namespace App\Console\Commands;

use App\Mail\RecentPostsMail;
use App\Models\EmailTemplate;
use App\Models\NewsHuntSubscriber;
use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendRecentPostsEmail extends Command
{
    protected $signature   = 'email:send-recent-posts';
    protected $description = 'Send recent posts to all subscribers';
    const TIME_FORMATE     = 'Y-m-d H:i:s';

    public function handle()
    {
        $subscribers   = NewsHuntSubscriber::all();
        $emailTemplate = EmailTemplate::where('status', 'active')->first();
        $postCount     = $emailTemplate ? $emailTemplate->post_count : 5;

        $subscribedLanguageIds = [];
        $top_posts_query       = Post::with(['channel', 'topic'])
            ->select('id', 'title', 'slug', 'channel_id', 'image', 'pubdate', 'view_count', 'comment', 'status')
            ->where('status', 'active') // ✅ Only active posts
            ->whereHas('channel', function ($query) {
                $query->where('status', 'active');
            })
            ->where('image', '!=', '');

        if (! empty($subscribedLanguageIds)) {
            $top_posts_query->whereIn('news_language_id', $subscribedLanguageIds);
        }

        $recentPosts = $top_posts_query
            ->orderBy('pubdate', 'desc')
            ->take($postCount)
            ->get()
            ->map(function ($post) {
                $post->publish_date_news = Carbon::parse($post->pubdate)->format(self::TIME_FORMATE);
                if ($post->publish_date) {
                    $post->publish_date = Carbon::parse($post->publish_date)->diffForHumans();
                } elseif ($post->pubdate) {
                    $post->pubdate = Carbon::parse($post->pubdate)->diffForHumans();
                }
                return $post;
            });

        applyMailSettingsFromDb();
            
        foreach ($subscribers as $subscriber) {
            Mail::to($subscriber->email)->queue(new RecentPostsMail($recentPosts));
        }
        $this->info('Recent posts email sent to all subscribers.');
    }
}
