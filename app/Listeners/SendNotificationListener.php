<?php
namespace App\Listeners;

use App\Events\SendNotification;
use App\Services\SendNotification as FirebaseService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendNotificationListener implements ShouldQueue
{
    use InteractsWithQueue;
    /**
     * Create the event listener.
     */
    protected $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    /**
     * Handle the event.
     */
    public function handle(SendNotification $event): void
    {
        if (is_array($event->fcmIds) && ! empty($event->fcmIds)) {
            // Send to all FCM IDs at once instead of individual calls
            $this->firebaseService->sendPostNotification(
                $event->fcmIds,
                $event->title,
                $event->description,
                $event->slug,
                $event->image ?? "",
                'popup',
                $event->news_language_id
            );
        }
    }
}
