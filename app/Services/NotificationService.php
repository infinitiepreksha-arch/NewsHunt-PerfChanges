<?php
namespace App\Services;

use App\Models\Notifications;
use App\Models\Setting;
use App\Models\UserFcm;
use App\Services\CachingService;
use Carbon\Carbon;
use Facebook\WebDriver\Exception\Internal\RuntimeException as InternalRuntimeException;
use Google\Client;
use Google\Exception;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Exception\RuntimeException as ExceptionRuntimeException;
use RuntimeException;
use Throwable;

class NotificationService
{
    public static function getFcmTokensByLanguage($newsLanguageId)
    {
        if (empty($newsLanguageId)) {
            return [];
        }

        return UserFcm::select(['fcm_id', 'platform'])
            ->where('news_language_id', $newsLanguageId)
            ->whereNotNull('fcm_id')
            ->where('fcm_id', '!=', '')
            ->get()
            ->toArray();
    }

    /**
     * Retrieve FCM tokens specifically for a post's language preference.
     */
    public static function getFcmTokensForPost($post)
    {
        $tokens = self::getFcmTokensByLanguage($post->news_language_id);

        if (empty($tokens)) {
            // Requirement: If language id not available in user_fcms table, don't send through firebase.
            // We return empty array here so the caller can skip the push event.
            \Illuminate\Support\Facades\Log::info("Post Notification: No users found for language ID " . $post->news_language_id);
        }

        return $tokens;
    }

    public static function isNotificationAllowed()
    {
        $settings   = CachingService::getSystemSettings();
        $dailyLimit = (int) ($settings['daily_notification_limit'] ?? 100);

        // Count notifications sent today
        $todayCount = Notifications::where('created_at', '>=', Carbon::today()->startOfDay())->count();

        if ($todayCount >= $dailyLimit) {
            return false;
        }

        return true;
    }
    /**
     * Sends FCM notification to specified registration IDs.
     *
     * @param array $registrationIDs
     * @param string|null $title
     * @param string|null $message
     * @param string $slug
     * @param string $image
     * @param string $type
     * @param array $customBodyFields
     * @return false|mixed
     */
    public static function sendFcmNotification(
        array $registrationIDs,
        string $title = '',
        string $message = '',
        string $slug = '',
        string $image = '',
        string $type = 'default',
        array $customBodyFields = []
    ) {
        try {
            $project_id = Setting::where('name', 'projectId')->pluck('value')->first();

            // Validate project ID
            if (empty($project_id)) {
                throw new InternalRuntimeException('Firebase project ID is not set in settings');
            }

            // Log::info('Using Firebase Project ID:', ['project_id' => $project_id]);

            $url          = "https://fcm.googleapis.com/v1/projects/{$project_id}/messages:send";
            $access_token = self::getAccessToken();

            if (! $access_token) {
                throw new InternalRuntimeException('Unable to retrieve access token');
            }

            $results = [];
            foreach ($registrationIDs as $registrationID) {
                // Convert all custom body fields to strings
                $stringCustomFields = [];
                foreach ($customBodyFields as $key => $value) {
                    $stringCustomFields[$key] = (string) $value;
                }

                $maxLength      = 150;
                $trimmedMessage = mb_strlen($message) > $maxLength
                    ? mb_substr($message, 0, $maxLength)
                    : $message;
                $data = [
                    'message' => [
                        'token'        => $registrationID,
                        'notification' => [
                            'title' => $title,
                            'body'  => $trimmedMessage,
                            'image' => $image,
                        ],
                        'data'         => array_merge($stringCustomFields, [
                            'title'             => (string) $title,
                            'body'              => (string) $trimmedMessage,
                            'slug'              => (string) $slug,
                            'image'             => (string) $image,
                            'type'              => (string) $type,
                            'show_notification' => 'true',
                            'force_popup'       => 'true',
                            'priority'          => 'high',
                            'click_action'      => 'FLUTTER_NOTIFICATION_CLICK',
                        ]),
                        'android'      => [
                            'priority'     => 'high',
                            'notification' => [
                                'title'        => $title,
                                'body'         => $trimmedMessage,
                                'image'        => $image,
                                'sound'        => 'default',
                                'channel_id'   => 'high_importance_channel',
                                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                                'tag'          => 'notification_' . time(),
                            ],
                        ],
                        'apns'         => [
                            'headers' => [
                                'apns-priority' => '10',
                            ],
                            'payload' => [
                                'aps'         => [
                                    'alert'             => [
                                        'title' => $title,
                                        'body'  => $trimmedMessage,
                                    ],
                                    'sound'             => 'default',
                                    'badge'             => 1,
                                    'mutable-content'   => 1,
                                    'content-available' => 1,
                                ],
                                // Custom data for iOS
                                'custom_data' => array_merge($stringCustomFields, [
                                    'title'             => (string) $title,
                                    'body'              => (string) $trimmedMessage,
                                    'slug'              => (string) $slug,
                                    'image'             => (string) $image,
                                    'type'              => (string) $type,
                                    'show_notification' => 'true',
                                    'force_popup'       => 'true',
                                    'priority'          => 'high',
                                ]),
                            ],
                        ],
                    ],
                ];

                // Log::info('FCM Request Data:', ['data' => $data]);
                $response = self::sendFcmRequest($url, $access_token, $data);
                // Log::info('FCM Response:', ['response' => $response]);

                $decodedResponse = json_decode($response, true);

                // Check if there was an error and log it
                if (isset($decodedResponse['error'])) {
                    Log::error('FCM Error Response:', $decodedResponse['error']);
                    // Don't throw exception, just log and continue with other tokens
                    $results[] = $decodedResponse;
                } else {
                    $results[] = $decodedResponse;
                }
            }
            return $results;
        } catch (Throwable $th) {
            Log::error('FCM Notification Error:', ['error' => $th->getMessage()]);
            throw new ExceptionRuntimeException($th->getMessage());
        }
    }

    /**
     * Send high priority notification that forces popup display
     */
    public static function sendPopupNotification(
        array $registrationIDs,
        string $title = '',
        string $message = '',
        string $slug = '',
        string $image = '',
        array $customBodyFields = []
    ) {
        return self::sendFcmNotification(
            $registrationIDs,
            $title,
            $message,
            $slug,
            $image,
            'popup',
            array_merge($customBodyFields, [
                'notification_type'    => 'popup',
                'requires_interaction' => 'true',
                'auto_cancel'          => 'false',
            ])
        );
    }

    private static function sendFcmRequest($url, $access_token, $data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $access_token,
            'Content-Type: application/json',
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($response === false) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new InternalRuntimeException('Curl error: ' . $error);
        }

        if ($httpCode !== 200) {
            Log::error('FCM HTTP Error:', [
                'code'     => $httpCode,
                'response' => $response,
                'url'      => $url,
            ]);
        }

        curl_close($ch);
        return $response;
    }

    /**
     * Retrieves access token for Firebase API.
     *
     * @return string|null
     * @throws RuntimeException
     */
    public static function getAccessToken()
    {
        try {
            $file_name = Setting::select('value')->where('name', 'service_file')->pluck('value')->first();
            $file_path = base_path('public/storage/' . $file_name);

            if (! file_exists($file_path)) {
                throw new InternalRuntimeException('Service account file not found: ' . $file_path);
            }

            $client = new Client();
            $client->setAuthConfig($file_path);
            $client->setScopes(['https://www.googleapis.com/auth/firebase.messaging']);
            $token = $client->fetchAccessTokenWithAssertion();

            if (! isset($token['access_token'])) {
                throw new InternalRuntimeException('Failed to retrieve access token');
            }

            return $token['access_token'];
        } catch (Exception $e) {
            Log::error('Access Token Error:', ['error' => $e->getMessage()]);
            throw new InternalRuntimeException($e->getMessage());
        }
    }
}
