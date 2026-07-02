<?php
namespace App\Http\Controllers\Apis;

use App\Http\Controllers\Controller;
use App\Models\AppPostView;
use App\Models\Channel;
use App\Models\ChannelSubscriber;
use App\Models\Comment;
use App\Models\Favorite;
use App\Models\Post;
use App\Models\PostView;
use App\Models\User;
use App\Models\UserCredits;
use App\Models\UserFcm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use libphonenumber\PhoneNumberUtil;

class UserLoginController extends Controller
{
    const UNAUTHORIZE_USER = 'Unauthorized user';

    /* User Register Function */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'fcm_id'   => 'required',
            'platform' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error'   => true,
                'message' => $validator->errors()->first(),
                'data'    => null,
            ]);
        }
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'fcm_id'   => $request->fcm_id,
            'password' => Hash::make($request->password),
            'type'     => 'email',
        ]);

        UserFcm::where('fcm_id', $request->fcm_id)->delete();

        UserFcm::create([
            'user_id'          => $user->id,
            'fcm_id'           => $request->fcm_id,
            'platform'         => $request->platform,
            'news_language_id' => $request->news_language_id,
        ]);

        $user->assignRole('user');
        $token = $user->createToken('auth_token')->plainTextToken;

        UserCredits::create([
            'user_id'           => $user->id,
            'credits_purchased' => 0,
            'credits_consumed'  => 0,
            'total_credits'     => 0,
            'available_credits' => 0,
        ]);

        return response()->json([
            'error'   => false,
            'message' => 'User registered successfully!',
            'data'    => [
                'newsUser' => true,
                'token'    => $token,
                'user'     => $user,
            ],
        ]);
    }

    /* User Login function */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user     = Auth::user()->id;
            $userData = User::find($user);

            if ($userData->is_blocked && $userData->block_type == 'full') {
                Auth::logout();

                return response()->json([
                    'error'   => true,
                    'message' => 'Your account has been fully blocked. Please contact the administrator.',
                    'data'    => [],
                ], 403);
            }

            if ($userData->status == 'inactive') {
                Auth::logout();

                return response()->json([
                    'error'   => true,
                    'message' => 'Your account has been deactivated. Please contact the administrator.',
                    'data'    => [],
                ], 403);
            }
            $token = $userData->createToken('AuthToken')->plainTextToken;

            $userFcmId = UserFcm::where('fcm_id', $request->fcm_id)->delete();

            if (! empty($request->fcm_id)) {
                UserFcm::where('fcm_id', $request->fcm_id)->delete();

                UserFcm::create([
                    'user_id'          => Auth::user()->id,
                    'fcm_id'           => $request->fcm_id,
                    'platform'         => $request->platform,
                    'news_language_id' => $request->news_language_id,
                ]);
            } else {
                UserFcm::create([
                    'user_id'          => Auth::user()->id,
                    'fcm_id'           => $request->fcm_id,
                    'platform'         => $request->platform,
                    'news_language_id' => $request->news_language_id,
                ]);
            }
            $credits = UserCredits::firstOrCreate(
                ['user_id' => $user],
                [
                    'credits_purchased' => 0,
                    'credits_consumed'  => 0,
                    'total_credits'     => 0,
                    'available_credits' => 0,
                ]
            );

            return response()->json([
                'error'   => false,
                'message' => 'Login successful',
                'data'    => [
                    'newsUser' => true,
                    'token'    => $token,
                    'user'     => $userData,
                ],
            ]);
        } else {

            $userCheck = User::where('email', $credentials['email'])->first();

            if (! $userCheck) {
                return response()->json(['error' => true, 'message' => 'Please enter valid Email', 'data' => []], 401);
            } else {
                return response()->json(['error' => true, 'message' => 'Please enter valid Password', 'data' => []], 401);
            }
        }
    }

    /* User Forget function */
    public function forgetPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with(['status' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    }

    public function getProfile()
    {

        if (Auth::check()) {
            $user_id = Auth::user()->id;

            $user = User::select('*')
                ->where('id', $user_id)
                ->first();

            if ($user) {

                $user->name              = $user->name ?? "";
                $user->email             = $user->email ?? "";
                $user->mobile            = $user->mobile ?? "";
                $user->email_verified_at = $user->email_verified_at ?? "";
                $user->profile           = $user->profile ?? "";
                $user->type              = $user->type ?? "";
                $user->fcm_id            = $user->fcm_id ?? "";
                $user->notification      = $user->notification ?? "";
                $user->firebase_id       = $user->firebase_id ?? "";
                $user->status            = $user->status ?? "";
                $user->created_at        = strval($user->created_at) ?? "";
                $user->updated_at        = strval($user->updated_at) ?? "";
                $user->deleted_at        = strval($user->deleted_at) ?? "";
                $user->country_code      = strval($user->country_code) ?? "";
            }

            return response()->json([
                'error'   => false,
                'message' => 'User fetched successful',
                'data'    => ['token' => "", 'user' => $user],
            ]);
        } else {
            return response()->json([
                'error'   => false,
                'message' => self::UNAUTHORIZE_USER,
            ]);
        }
    }

    public function updateProfile(Request $request)
    {
        if (Auth::check()) {
            $user_id   = Auth::user()->id;
            $user      = User::find($user_id);
            $validator = Validator::make($request->all(), [
                'user_name' => 'nullable|string|max:255',
                'profile'   => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error'   => true,
                    'message' => 'Validation failed',
                    'errors'  => $validator->errors(),
                ], 422);
            }

            if ($request->has('user_name')) {
                $user->name = $request->user_name;
            }

            if ($request->hasFile('profile')) {
                if ($user->profile && Storage::exists('public/' . $user->profile)) {
                    Storage::delete('public/' . $user->profile);
                }

                $profilePath   = $request->file('profile')->store('profile_images', 'public');
                $user->profile = $profilePath;
            }

            $user->save();

            return response()->json([
                'error'   => false,
                'message' => 'Profile updated successfully',
                'data'    => $user,
            ]);
        } else {
            return response()->json([
                'error'   => true,
                'message' => self::UNAUTHORIZE_USER,
            ], 401);
        }
    }

    public function getChannelList()
    {
        if (Auth::check()) {
            $user_id = Auth::user()->id;
            $perPage = request()->get('per_page', 10);

            $channels = Channel::select('channels.id', 'channels.follow_count', 'channels.name', 'channels.slug', 'channels.logo', 'channels.description')
                ->selectRaw('CASE WHEN channel_subscribers.user_id IS NOT NULL THEN 1 ELSE 0 END as is_followed')
                ->join('channel_subscribers', function ($join) use ($user_id) {
                    $join->on('channels.id', '=', 'channel_subscribers.channel_id')
                        ->where('channel_subscribers.user_id', '=', $user_id);
                })
                ->where('channels.status', 'active')
                ->orderBy('channels.id', 'desc')
                ->paginate($perPage);

            $channels->through(function ($item) {
                $item->logo = $item->logo ? asset('storage/images/' . $item->logo) : null;
                return $item;
            });

            return response()->json([
                'error'   => false,
                'message' => 'Channel list fetched successfully',
                'data'    => [
                    'channels'   => $channels->items(),
                    'pagination' => [
                        'current_page'  => $channels->currentPage(),
                        'last_page'     => $channels->lastPage(),
                        'per_page'      => (int) $channels->perPage(),
                        'total'         => $channels->total(),
                        'next_page_url' => $channels->nextPageUrl() ?? '',
                        'prev_page_url' => $channels->previousPageUrl() ?? '',
                        'from'          => $channels->firstItem() ?? '',
                        'to'            => $channels->lastItem() ?? '',
                    ],
                ],
            ]);
        } else {
            return response()->json([
                'error'   => true,
                'message' => 'Unauthorized user',
            ], 401);
        }
    }

    public function updateProfileNew(Request $request)
    {
        if (Auth::check()) {
            $user_id   = Auth::user()->id;
            $user      = User::find($user_id);
            $validator = Validator::make($request->all(), [
                'user_name' => 'nullable|string|max:255',
                'profile'   => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'email'     => 'nullable',
                'mobile'    => 'nullable',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error'   => true,
                    'message' => 'Validation failed',
                    'errors'  => $validator->errors(),
                ], 422);
            }

            if (isset($request->email)) {
                $email_verify = User::where('email', $request->email)->where('id', '!=', $user_id)->first() ?? null;

                if ($email_verify == null) {
                    $user->email = $request->email;
                } else {
                    $message = "Email aleady exist.";
                }
            }
            if (isset($request->mobile)) {
                $phoneUtil      = PhoneNumberUtil::getInstance();
                $defaultRegion  = 'IN';
                $phoneNumber    = $phoneUtil->parse($request->mobile, $defaultRegion);
                $countryCode    = $phoneNumber->getCountryCode();
                $nationalNumber = $phoneNumber->getNationalNumber();
                $mobile_verify  = User::where('mobile', $nationalNumber)->where('country_code', $countryCode)->where('id', '!=', $user_id)->first() ?? null;

                if ($mobile_verify == null) {
                    $user->country_code = $countryCode;
                    $user->mobile       = $nationalNumber;
                } else {
                    $message = "Mobile number already exist.";
                }

            }
            if ($request->has('user_name')) {
                $user->name = $request->user_name;
            }

            if ($request->hasFile('profile')) {
                if ($user->profile && Storage::exists('public/' . $user->profile)) {
                    Storage::delete('public/' . $user->profile);
                }

                $profilePath   = $request->file('profile')->store('profile_images', 'public');
                $user->profile = $profilePath;
            }

            $user->save();
            if ($user) {
                $message = "Profile updated successfully";
            }

            return response()->json([
                'error'   => false,
                'message' => $message,
                'data'    => $user,
            ]);
        } else {
            return response()->json([
                'error'   => true,
                'message' => self::UNAUTHORIZE_USER,
            ], 401);
        }

    }

    public function deleteUser(Request $request)
    {
        $device_id = $request->device_id ?? null;

        if ($device_id !== null) {
            AppPostView::where('device_id', $device_id)->delete();
        }
        $user_id = Auth::user()->id ?? null;
        if ($user_id === null) {
            $message = 'User Not found.';
        } elseif ($user_id == 1) {
            $message = "You cannot delete Admin user.";
        } else {
            $user = User::find($user_id);
            $user->forceDelete();
            $this->deleteReleted($user_id);
            $message = 'User removed successfully.';
        }

        return response()->json([
            'error'   => false,
            'message' => $message,
        ]);
    }

    public function deleteReleted($userId)
    {

        PostView::where('user_id', $userId)->delete();

        /* Delete user favorite posts */
        $bookmarks = Favorite::where('user_id', $userId)->get();
        foreach ($bookmarks as $bookmark) {
            $post = Post::find($bookmark->post_id);
            $post->decrement('favorite');
            $bookmark->delete();
        }

        $user_fcm = UserFcm::where('user_id', $userId)->get();
        if (! empty($user_fcm)) {
            UserFcm::where('user_id', $userId)->delete();
        }

        /* Delete user followed channels */
        $channelList = ChannelSubscriber::where('user_id', $userId)->get();
        if ($channelList) {
            foreach ($channelList as $channel) {
                $channelProfile = Channel::find($channel->channel_id);
                $channelProfile->subscribers()->detach($userId);
                $channelProfile->decrement('follow_count');
            }
        }

        /* Delete user comments */
        $comments = Comment::where('user_id', $userId)->orderBy('id', 'desc')->get();
        if ($comments) {
            foreach ($comments as $comment) {
                $post = Post::find($comment->post_id);
                $comment->delete();
                if ($post->comment > 0) {
                    $post->decrement('comment');
                }
            }
        }
        return $userId;
    }
}
