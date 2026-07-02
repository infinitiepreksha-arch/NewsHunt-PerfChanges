<?php
namespace App\Mail;

use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RecentPostsMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $posts;

    /**
     * Create a new message instance.
     */
    public function __construct($posts)
    {
        $this->posts = $posts;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $appName = Setting::where('name', 'app_name')->value('value') ?? 'News Portal';

        return $this->subject('Welcome to ' . $appName . ' – Your Top 5 Recent Posts')
            ->view('front_end.emails.recent-posts')
            ->with([
                'posts'   => $this->posts,
                'appName' => $appName,
            ]);

    }
}
