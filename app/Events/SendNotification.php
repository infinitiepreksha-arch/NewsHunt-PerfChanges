<?php
namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendNotification
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public $title;
    public $description;
    public $image;
    public $slug;
    public $fcmIds;
    public $news_language_id;

    public function __construct($title, $description, $image, $slug, $fcmIds, $news_language_id = null)
    {
        $this->title            = $title;
        $this->description      = $description;
        $this->image            = $image;
        $this->slug             = $slug;
        $this->fcmIds           = $fcmIds;
        $this->news_language_id = $news_language_id;

        Log::info("From job" . $image);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
