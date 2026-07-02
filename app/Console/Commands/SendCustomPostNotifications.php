<?php
namespace App\Console\Commands;

use App\Http\Controllers\PostController;
use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendCustomPostNotifications extends Command
{
    protected $signature   = 'posts:send-custom-notifications';
    protected $description = 'Send notifications for admin-created custom posts';

    public function handle()
    {
        $tenMinutesAgo = Carbon::now('UTC')->subMinutes(10);

        $customPosts = Post::where('is_custom_post', true)
            ->where('created_at', '>=', $tenMinutesAgo)
            ->get();

        Log::info($customPosts);

        $controller = app(PostController::class);

        foreach ($customPosts as $post) {
            try {
                $controller->sendPostNotification($post);
                $this->info("Notification sent for Post ID: {$post->id}");
            } catch (\Exception $e) {
                Log::error("Failed to send notification for Post ID {$post->id}: " . $e->getMessage());
            }
        }
    }
}
