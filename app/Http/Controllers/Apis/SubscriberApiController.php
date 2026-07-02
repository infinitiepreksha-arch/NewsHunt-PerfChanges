<?php
namespace App\Http\Controllers\Apis;

use App\Http\Controllers\Controller;
use App\Mail\RecentPostsMail;
use App\Models\EmailTemplate;
use App\Models\NewsHuntSubscriber;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SubscriberApiController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:news_hunt_subscribers,email',
        ]);

        $emailTemplate = EmailTemplate::where('status', 'active')->first();
        $postCount     = $emailTemplate ? $emailTemplate->post_count : 5;

        $recentPosts = Post::active()
            ->orderBy('publish_date', 'desc')
            ->take($postCount)
            ->get();

        $subscriber        = new NewsHuntSubscriber();
        $subscriber->email = $request->email;
        $subscriber->save();

        try {
            applyMailSettingsFromDb();
            Mail::to($request->email)->send(new RecentPostsMail($recentPosts));
        } catch (\Exception $e) {
            return response()->json([
                'error'   => true,
                'message' => 'Subscription saved, but failed to send email.',
                'error'   => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'error'   => false,
            'message' => 'Subscription successful. Email sent!',
            'data'    => $subscriber,
        ]);
    }
}
