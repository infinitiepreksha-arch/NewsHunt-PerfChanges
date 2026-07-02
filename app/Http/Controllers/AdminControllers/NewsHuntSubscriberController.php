<?php
namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Mail\RecentPostsMail;
use App\Models\EmailTemplate;
use App\Models\NewsHuntSubscriber;
use App\Models\Post;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Yajra\DataTables\Facades\DataTables;

class NewsHuntSubscriberController extends Controller
{
    public function index()
    {
        ResponseService::noAnyPermissionThenRedirect(['list-subscribers']);
        try {
            $theme = getTheme();
            $title = __('page.SUBSCRIBERS');
            return view('admin.subscriber.news-hunt-subscribers', compact('theme', 'title'));
        } catch (\Exception $e) {
            return "";
        }
    }

    public function show()
    {
        $data = NewsHuntSubscriber::select('id', 'email')->get();

        return DataTables::of($data)->make(true);
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:news_hunt_subscribers,email',
        ]);

        $emailTemplate = EmailTemplate::where('status', 'active')->first();
        $postCount     = $emailTemplate ? $emailTemplate->post_count : 5;
        $recentPosts   = Post::active()
            ->orderBy('publish_date', 'desc')
            ->take($postCount)
            ->get();
        $subscriber        = new NewsHuntSubscriber();
        $subscriber->email = $request->email;
        $subscriber->save();

        $cookie = cookie('subscriber_email_', $request->email, 21600);

        applyMailSettingsFromDb();

        // Send email
        Mail::to($request->email)->send(new RecentPostsMail($recentPosts));

        return response()->json([
            'status'  => 1,
            'message' => __('frontend-labels.home.success_message'),
        ])->withCookie($cookie);
    }
}
