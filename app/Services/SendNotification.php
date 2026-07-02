<?php
namespace App\Services;

use App\Models\Notifications;
use App\Models\Post;
use App\Models\Setting;
use Exception;
use Facebook\WebDriver\Exception\Internal\RuntimeException as InternalRuntimeException;
use Google\Client;
use Illuminate\Support\Facades\Log;
use Throwable;

class SendNotification
{
    /**
     * Sends FCM notification to specified registration IDs with platform support.
     *
     * @param array $fcmIDs Array containing fcm_id and platform for each device
     * @param string $title
     * @param string $description
     * @param string $slug
     * @param string $image_url
     * @param string $type
     * @return array|null
     */
    protected $messaging;

    public function sendPostNotification(array $fcmIDs, string $title = '', string $description = '', string $slug = '', string $image_url = '', string $type = 'popup', $news_language_id = null)
    {
        try {
            // Ensure registrationIDs is an array
            if (! is_array($fcmIDs)) {
                throw new \InvalidArgumentException('registrationIDs must be an array');
            }

            $projectId    = Setting::where('name', 'projectId')->pluck('value')->first();
            $url          = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";
            $access_token = self::getDefaultAccessToken();

            if (! $access_token) {
                throw new \InvalidArgumentException('Unable to retrieve access token');
            }

            $news         = Post::where('slug', $slug)->with('channel')->first();
            $channel_logo = asset('storage/images/' . $news->channel->logo) ?? asset('default-logo.png');
            $channel_name = $news->channel->name ?? "";
            $results      = [];

            foreach ($fcmIDs as $fcmData) {
                $fcmID = $fcmData['fcm_id'];

                $platform           = strtolower($fcmData['platform']); // Default to android if platform not specified
                $description        = strip_tags(html_entity_decode($description));
                $maxLength          = 150;
                $trimmedDescription = mb_strlen($description) > $maxLength
                    ? mb_substr($description, 0, $maxLength)
                    : $description;
                $data = [
                    'message' => [
                        'token' => $fcmID,
                        'data'  => [
                            'title'           => (string) $title,
                            'body'            => (string) $trimmedDescription,
                            'slug'            => (string) $slug,
                            'image'           => (string) $image_url,
                            'channel_logo'    => (string) $channel_logo,
                            'channel_name'    => (string) $channel_name,
                            'notification_id' => (string) uniqid('notif_', true),
                            'timestamp'       => (string) time(),
                            'type'            => (string) $type,
                            'click_action'    => 'FLUTTER_NOTIFICATION_CLICK',
                        ],
                    ],
                ];
                $customBodyFields = [
                    'slug'            => (string) $slug,
                    'image'           => (string) $image_url,
                    'channel_logo'    => (string) $channel_logo,
                    'channel_name'    => (string) $channel_name,
                    'notification_id' => (string) uniqid('notif_', true),
                    'timestamp'       => (string) time(),
                    'type'            => (string) $type,
                ];

                if ($platform === 'ios') {
                    $data['message'] = [
                        'token'        => $fcmID,
                        'notification' => [
                            'title' => $title,
                            'body'  => $trimmedDescription,
                            'image' => $image_url,
                        ],
                        'apns'         => [
                            'headers' => [
                                'apns-priority'  => '10',
                                'apns-push-type' => 'alert',
                            ],
                            'payload' => [
                                'aps' => [
                                    'alert'             => [
                                        'title' => $title,
                                        'body'  => $trimmedDescription,
                                    ],
                                    'sound'             => 'default',
                                    'badge'             => 1,
                                    'mutable-content'   => 1,
                                    'content-available' => 1,
                                ],
                            ],
                        ],
                        'data'         => $customBodyFields,
                    ];
                } elseif ($platform === 'android') {
                    $data = [
                        'message' => [
                            'token'        => $fcmID,
                            'notification' => [
                                'title' => $title,
                                'body'  => $trimmedDescription,

                                'image' => $image_url,
                            ],
                            'android'      => [
                                'priority'     => 'high',
                                'notification' => [
                                    'title'        => $title,
                                    'body'         => $trimmedDescription,
                                    'image'        => $image_url,
                                    'channel_id'   => 'default_channel',
                                    'sound'        => 'default',
                                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                                ],
                            ],
                            'data'         => $customBodyFields,
                        ],
                    ];
                }

                $response = self::sendDefaultFcmRequest($url, $access_token, $data);

                // Store notification in database (only once per slug)
                $exist = Notifications::where('slug', $slug)->first();
                if (! $exist) {
                    $notify_data = [
                        'title'            => $title,
                        'message'          => $description,
                        'slug'             => $slug,
                        'image'            => $image_url,
                        'news_language_id' => $news_language_id,
                    ];
                    Notifications::create($notify_data);
                }

                $results[] = [
                    'platform' => $platform,
                    'fcm_id'   => $fcmID,
                    'response' => $response,
                ];
            }

            return $results;
        } catch (Throwable $th) {
            Log::error('FCM Notification Error:', [
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
            ]);
            return null;
        }
    }

    private static function sendDefaultFcmRequest($url, $access_token, $data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $access_token,
            'Content-Type: application/json',
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Add this for development
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($response === false) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new InternalRuntimeException('Curl error: ' . $error);
        }

        curl_close($ch);

        // Log response for debugging
        // Log::info('FCM Response:', [
        //     'http_code' => $httpCode,
        //     'response'  => $response,
        // ]);

        return json_decode($response, true);
    }

    public static function getDefaultAccessToken()
    {
        try {
            $file_name = Setting::select('value')->where('name', 'service_file')->pluck('value')->first();
            $file_path = base_path('public/storage/' . $file_name);

            if (! file_exists($file_path)) {
                throw new Exception('Service account file not found: ' . $file_path);
            }

            $client = new Client();
            $client->setAuthConfig($file_path);
            $client->setScopes(['https://www.googleapis.com/auth/firebase.messaging']);

            $token = $client->fetchAccessTokenWithAssertion();

            if (isset($token['error'])) {
                throw new Exception('Error fetching access token: ' . $token['error_description']);
            }

            return $token['access_token'] ?? null;
        } catch (Exception $e) {
            Log::error('Access Token Error:', ['error' => $e->getMessage()]);
            throw new InternalRuntimeException($e->getMessage());
        }
    }
}
