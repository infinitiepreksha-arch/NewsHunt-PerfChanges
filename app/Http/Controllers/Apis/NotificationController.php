<?php
namespace App\Http\Controllers\Apis;

use App\Http\Controllers\Controller;
use App\Models\Admin\Notifications as AdminNotifications;
use App\Models\Notifications;
use App\Models\ReadNotification;
use App\Models\Setting;
use App\Models\UserFcm;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{

    const STORAGE_PATH      = 'storage/images/';
    const NOTIFICATION_PATH = 'storage/';

    public function getNotificationList(Request $request)
    {
        $perPage         = request()->get('per_page', 10);
        $fcmId           = request()->get('fcm_id', null);
        $userId          = auth()->id(); // Get the authenticated user's ID
        $newsLanguageIds = $request->news_language_id;

        $getReadNotificaiton = ReadNotification::select('notification_id')
            ->where('fcm_id', $fcmId)
            ->pluck('notification_id')
            ->toArray();

        // Fetch notifications with the necessary joins and user-specific conditions
        $data = AdminNotifications::select(
            'notifications.id',
            'notifications.slug',
            'channels.logo',
            'notifications.title',
            'notifications.message',
            'notifications.image',
            'notifications.created_at',
            'notifications.url',
        )
            ->leftJoin('posts', 'notifications.slug', '=', 'posts.slug')
            ->leftjoin('channels', function ($join) {
                $join->on('posts.channel_id', '=', 'channels.id')
                    ->where('channels.status', 'active');
            })
            ->when(! empty($newsLanguageIds), function ($query) use ($newsLanguageIds) {
                $query->where(function ($q) use ($newsLanguageIds) {

                    $q->whereNull('notifications.slug')
                        ->orWhere('notifications.slug', '')

                        ->orWhereExists(function ($sub) use ($newsLanguageIds) {
                            $sub->selectRaw(1)
                                ->from('posts')
                                ->whereColumn('posts.slug', 'notifications.slug')
                                ->whereIn('posts.news_language_id', (array) $newsLanguageIds);
                        });

                });
            })
            ->leftjoin('topics', function ($join) {
                $join->on('posts.topic_id', '=', 'topics.id')
                    ->where('topics.status', 'active');
            })
            ->where(function ($query) use ($userId) {
                $query->where('notifications.send_to', 'all') // Specify table for send_to
                    ->orWhere(function ($subQuery) use ($userId) {
                        $subQuery->where('notifications.send_to', 'selected')
                            ->where('notifications.user_id', 'like', '%"' . $userId . '"%'); // Specify table for user_id
                    });
            })
            ->orderBy('notifications.created_at', 'desc')
            ->paginate($perPage);

        $favicon = Setting::where('name', 'favicon_icon')->value('value');

        // Prepare the notifications array
        $notifications = [];
        foreach ($data as $notification) {
            if (! empty($notification->slug)) {

                $notifications[] = [
                    'id'           => $notification->id,
                    'isRead'       => in_array($notification->id, $getReadNotificaiton) ? 1 : 0,
                    'channel_logo' => $notification->logo ? url(self::STORAGE_PATH . $notification->logo) : "",
                    'slug'         => $notification->slug,
                    'title'        => $notification->title,
                    'url'          => $notification->url ?? "",
                    'message'      => $notification->message,
                    'image'        => $notification->image,
                    'created_at'   => Carbon::parse($notification->created_at)->diffForHumans(),
                ];
            } else {
                $notifications[] = [
                    'id'           => $notification->id,
                    'isRead'       => in_array($notification->id, $getReadNotificaiton) ? 1 : 0,
                    'channel_logo' => url(self::NOTIFICATION_PATH . $favicon),
                    'slug'         => $notification->slug,
                    'title'        => $notification->title,
                    'url'          => $notification->url ?? "",
                    'message'      => $notification->message,
                    'image'        => ! empty($notification->image)
                        ? url(self::NOTIFICATION_PATH . $notification->image)
                        : '',
                    'created_at'   => Carbon::parse($notification->created_at)->diffForHumans(),
                ];
            }
        }

        $dataCount = $data->count();
        return response()->json([
            'error'   => false,
            'message' => 'Data fetched successfully.',
            'data'    => [
                'isAllRead'    => count($getReadNotificaiton) == $dataCount ? false : true,
                'notification' => $notifications,
            ],
        ]);
    }

    /* Store Fcm id */
    public function storeOnlyFcm(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fcmId'            => 'required',
            'platform'         => 'required',
            'news_language_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error'   => true,
                'message' => 'Fcm id validation error',
                'data'    => "",
            ], 422);
        }

        $userFcmId = UserFcm::where('fcm_id', $request->fcmId)->first();
        if (empty($userFcmId)) {
            UserFcm::create([
                'user_id'          => "",
                'fcm_id'           => $request->fcmId,
                'platform'         => $request->platform,
                'news_language_id' => $request->news_language_id,
            ]);
            $message = 'Fcm id store successfully.';
        } else {
            $message = 'Already stored.';
        }

        return response()->json([
            'error'   => false,
            'message' => $message,
            'data'    => [
                'fcm_id' => $request->fcmId,
            ],

        ]);
    }

    public function handleNotificationRead(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fcm_id'            => 'required',
            'notification_id'   => 'required|integer',
            'notification_read' => 'required|in:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error'   => true,
                'message' => $validator->errors()->first(),
                'data'    => [],
            ], 422);
        }

        if ($request->notification_read == 1) {
            $result = $this->readCustomNotification($request->fcm_id, $request->notification_id);

            if (! $result) {
                return response()->json([
                    'error'   => true,
                    'message' => 'Notification not found',
                    'data'    => [],
                ], 404);
            }
        }

        return response()->json([
            'error'   => false,
            'message' => 'Notification read processed successfully',
            'data'    => [
                'fcm_id'          => $request->fcm_id,
                'notification_id' => $request->notification_id,
            ],
        ]);
    }

    public function readCustomNotification($fcm_id, $notification_id)
    {
        $notificationExists = Notifications::where('id', $notification_id)->exists();

        if (! $notificationExists) {
            return false;
        }

        ReadNotification::firstOrCreate([
            'notification_id' => $notification_id,
            'fcm_id'          => $fcm_id,
        ]);

        return true;
    }
}
