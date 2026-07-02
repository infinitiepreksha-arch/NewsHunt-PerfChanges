<?php
namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Models\Admin\Notifications as AdminNotifications;
use App\Models\User;
use App\Models\UserFcm;
use App\Services\CachingService;
use App\Services\FileService;
use App\Services\NotificationService;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Throwable;
use Yajra\DataTables\Facades\DataTables;

const SOMETHING_WENT_WRONG = 'Something Went Wrong';
class NotificationController extends Controller
{
    private string $uploadFolder;

    public function __construct()
    {
        $this->uploadFolder = "notification";
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        ResponseService::noAnyPermissionThenRedirect(['list-notification', 'create-notification', 'delete-notification', 'view-users-notification', 'upload-image-notification']);

        return view('admin.notification.index');
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
        ResponseService::noPermissionThenSendJson('create-notification');

        // Check if automatic notifications are disabled
        $settings = CachingService::getSystemSettings();
        if (($settings['automatic_notifications'] ?? 1) == 0) {
            ResponseService::errorResponse('Currently you can\'t send notification because notification setting disabled so enable this first');
        }

        if (! NotificationService::isNotificationAllowed()) {
            ResponseService::errorResponse('Daily notification limit reached. You cannot send more notifications today.');
        }

        $input = $request->all();

        if ($request->has('user_id') && ! empty($request->user_id)) {
            if ($request->user_id === 'all') {
                $input['user_id'] = 'all';
            } elseif (is_string($request->user_id)) {
                $input['user_id'] = json_decode($request->user_id, true) ?: [];
            }
        } else {
            $input['user_id'] = [];
        }

        $validator = Validator::make($input, [
            'file'    => 'nullable|image|mimes:jpeg,png,jpg',
            'send_to' => 'required|in:all,selected',
            'user_id' => 'required_if:send_to,selected',
            'title'   => 'required|string|max:500',
            'message' => 'required|string|max:500',
            'slug'    => 'nullable',
            'url'     => 'nullable|url|max:1500',
        ]);

        if ($validator->fails()) {
            ResponseService::validationError($validator->errors()->first());
        }

        try {
            $userIdValue = '';
            if ($input['send_to'] == "all") {
                $userIdValue = 'all';
            } elseif ($input['send_to'] == "selected" && is_array($input['user_id'])) {
                $userIdValue = json_encode($input['user_id']);
            }

            $notification = AdminNotifications::create([
                'send_to' => $input['send_to'],
                'title'   => $input['title'],
                'message' => $input['message'],
                'image'   => $request->hasFile('file') ? $request->file('file')->store('notifications', 'public') : '',
                'user_id' => $userIdValue,
                'url'     => $input['url'],
            ]);

            // Get the full image URL
            $imageUrl = '';
            if ($notification->image) {
                $imageUrl = url(Storage::url($notification->image));
            }

            // Get FCM IDs and send notification
            $fcm_ids = $this->retrieveAndValidateFcmIds($input['send_to'], $input['user_id']);

            if (! empty($fcm_ids)) {
                $registrationIDs = array_filter($fcm_ids);
                if (! empty($registrationIDs)) {

                    // Keep your existing method call but fix the parameters
                    $result = NotificationService::sendFcmNotification(
                        (array) $registrationIDs,         // array $registrationIDs
                        $input['title'],                  // string $title
                        $input['message'],                // string $message
                        $input['slug'] ?? "notification", // string $slug
                        $imageUrl,                        // string $image
                        "popup",                          // string $type (THIS WAS THE ISSUE - you were passing array here)
                        [                                 // array $customBodyFields
                            'item_id'         => (string) $notification->id,
                            'notification_id' => (string) $notification->id,
                            'timestamp'       => (string) time(),
                            'action'          => 'open_notification',
                            'deep_link'       => $input['slug'] ?? "notification",
                        ]
                    );

                    // Check if any notifications failed
                    $failedCount = 0;
                    if (is_array($result)) {
                        foreach ($result as $response) {
                            if (isset($response['error'])) {
                                $failedCount++;
                            }
                        }
                    }

                    if ($failedCount > 0) {
                        Log::warning('Some notifications failed', [
                            'total'  => is_array($result) ? count($result) : 1,
                            'failed' => $failedCount,
                        ]);
                    }
                }
            } else {
                Log::warning('No valid FCM IDs found for the selected users');
            }

            ResponseService::successResponse('Message Sent Successfully');
        } catch (Throwable $th) {
            ResponseService::logErrorResponse($th, 'NotificationController -> store');
            ResponseService::errorResponse(SOMETHING_WENT_WRONG);
        }
    }

    private function retrieveAndValidateFcmIds($sendTo, $userId)
    {
        $fcm_ids = [];
        if ($sendTo == "all") {
            // Use distinct to get unique FCM IDs
            $fcm_ids = UserFcm::whereNotNull('fcm_id')
                ->where('fcm_id', '!=', '')
                ->distinct()
                ->pluck('fcm_id')
                ->toArray();
        } elseif ($sendTo == "selected" && ! empty($userId)) {
            if (is_array($userId)) {
                // Handle array of user IDs
                $fcm_ids = UserFcm::whereIn('user_id', $userId)
                    ->whereNotNull('fcm_id')
                    ->where('fcm_id', '!=', '')
                    ->pluck('fcm_id')
                    ->toArray();
            } else {
                // Handle single user ID
                $fcm_ids = UserFcm::where('user_id', $userId)
                    ->whereNotNull('fcm_id')
                    ->where('fcm_id', '!=', '')
                    ->pluck('fcm_id')
                    ->toArray();
            }
        }

        // Filter out invalid FCM tokens and remove duplicates
        $validFcmIds = array_filter($fcm_ids, function ($fcmId) {
            return ! empty($fcmId) && strlen($fcmId) > 50; // Basic FCM token validation
        });

        return array_unique($validFcmIds);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        ResponseService::noPermissionThenSendJson('list-notification');
        try {
            $getData = AdminNotifications::where('id', '!=', 0)->orderBy('id', 'DESC')->get();

            return DataTables::of($getData)
                ->addColumn('action', function ($getData) {
                    $actions = "<div class='d-flex flex-wrap gap-1'>";
                    if (auth()->user()->can('delete-notification')) {
                        $actions = "<a href='" . route('notification.destroy', $getData->id) . "' class='btn text-danger btn-sm delete-form delete-form-reload' data-bs-toggle='tooltip' title='Delete'> <i class='fa fa-trash'></i> </a>";
                    } else {
                        $actions .= "<span class='badge bg-danger text-white'>No permission for Delete</span>";
                    }
                    $actions .= "</div>";
                    return $actions;
                })
                ->make(true);

        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e, 'NotificationController -> show');
            ResponseService::errorResponse(SOMETHING_WENT_WRONG);
        }
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
    public function destroy($id)
    {
        try {
            ResponseService::noPermissionThenSendJson('delete-notification');
            $notification = AdminNotifications::findOrFail($id);
            $notification->delete();
            FileService::delete($notification->getRawOriginal('image'));
            return ResponseService::successResponse('Notification Deleted Successfully');
        } catch (Throwable $th) {
            ResponseService::logErrorResponse($th, 'NotificationController -> destroy');
            return ResponseService::errorResponse(SOMETHING_WENT_WRONG);
        }
    }

    public function sendTestNotification(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|array',
            'title'     => 'required|string',
            'message'   => 'required|string',
        ]);
        try {
            $fcmTokens = $request->input('fcm_token');
            $title     = $request->input('title');
            $message   = $request->input('message');
            // Prepare custom data fields if any
            $customData = [
                'extra_data' => 'This is a test notification',
            ];

            $result = NotificationService::sendFcmNotification(
                $fcmTokens,
                $title,
                $message,
                'notification',
                $customData
            );
            return response()->json([
                'error'   => 'false',
                'message' => 'Notification sent successfully',
                'result'  => $result,
            ]);
        } catch (Throwable $e) {
            Log::error('Error sending test FCM notification:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'true', 'message' => $e->getMessage()], 500);
        }
    }

    public function userListNofification()
    {

        try {
            ResponseService::noPermissionThenSendJson('view-users-notification');

            $users = User::get();

            $users->each(function ($channel) {
                $channel->mobile = $channel->country_code . ' ' . $channel->mobile;
            });
            return DataTables::of($users)->make(true);
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e, 'NotificationController -> userListNotification');
            ResponseService::errorResponse(SOMETHING_WENT_WRONG);
        }
    }
}
