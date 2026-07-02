<?php
namespace App\Http\Controllers;

use App\Models\Channel;
use App\Models\ChannelSubscriber;
use App\Models\Comment;
use App\Models\Favorite;
use App\Models\Post;
use App\Models\PostView;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberUtil;
use Throwable;

class UserLoginController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function showLoginForm()
    {
        $title   = __('frontend-labels.login.title');
        $appName = Setting::where('name', 'company_name')->value('value');

        $theme = getTheme();

        $data =
            [
            'theme'   => $theme,
            'title'   => $title,
            'appName' => $appName,
        ];
        return view('front_end/' . $theme . '/pages/user-login', $data);
    }

    /**
     * Handle Google OAuth login/registration
     */
    public function googleAuth(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'name'  => 'required|string',
            ]);

            $favicon_icon = Setting::where('name', 'favicon_icon')->value('value');
            $favicon_icon = $favicon_icon ?: 'front_end/classic/images/avatars/04.png'; // fallback path

            // Check if user exists with this email
            $user = User::where('email', $request->email)->first();

            if ($user) {
                if ($user->is_blocked && $user->block_type === 'full') {
                    return response()->json([
                        'error'   => true,
                        'message' => 'Your account has been fully blocked. Please contact the administrator.',
                    ], 403);
                }
                if ($user->status === 'inactive') {
                    return response()->json([
                        'error'   => true,
                        'message' => 'Your account has been deactivated. Please contact the administrator.',
                    ], 403);
                }
            } else {
                // Create new user with only the specified fields
                $user = User::create([
                    'name'              => $request->name,
                    'email'             => $request->email,
                    'password'          => bcrypt(Str::random(16)),
                    'email_verified_at' => now(),
                    'profile'           => $favicon_icon,
                ]);
            }

            // Log the user in
            Auth::login($user);

            return response()->json([
                'error'   => false,
                'message' => __('frontend-labels.validation.login_success'),
                'user'    => [
                    'id'    => $user->id,
                    'name'  => $user->name,
                    'email' => $user->email,
                ],
            ]);

        } catch (Throwable $th) {
            return response()->json([
                'error'   => true,
                'message' => 'Google authentication failed. Please try again.',
                'debug'   => config('app.debug') ? $th->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
    public function destroy(string $id)
    {
        //
    }
    public function changeProfileUpdate(Request $request)
    {
        if (config('app.demo_mode')) {
            return response()->json([
                'error'   => true,
                'message' => 'You do not have permission to perform this action in demo mode.',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ], [
            'name.required' => __('frontend-labels.Registration_validation.name_required') ?? 'The name field is required.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error'  => true,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $user_id = Auth::user()->id;
            $user    = User::find($user_id);

            $user->name = $request->name;

            // Handle password
            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }

            // Handle phone number with country code
            if ($request->filled('phone')) {
                $phoneData = $this->separatePhoneNumber($request->phone);

                if (! isset($phoneData['error'])) {
                    $user->country_code = $phoneData['country_code'];
                    $user->mobile       = $phoneData['mobile'];
                } else {
                    return response()->json([
                        'error'   => true,
                        'message' => $phoneData['error'],
                    ]);
                }
            }

            // Handle profile image
            if ($request->hasFile('profile')) {
                if ($user->profile && Storage::exists('public/' . $user->profile)) {
                    Storage::delete('public/' . $user->profile);
                }

                $logoPath      = $request->file('profile')->store('profile_images', 'public');
                $user->profile = $logoPath;
            }

            $user->update();

            return response()->json([
                'error'   => false,
                'message' => __('frontend-labels.validation.profile_updated'),
            ]);
        } catch (Throwable $th) {
            return response()->json([
                'error'   => true,
                'message' => 'Something went wrong. Please try again.',
                'debug'   => config('app.debug') ? $th->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Separate country code and mobile number using libphonenumber
     */
    protected function separatePhoneNumber($phoneNumber)
    {
        $phoneUtil = PhoneNumberUtil::getInstance();

        try {
            $numberProto = $phoneUtil->parse($phoneNumber, null);

            return [
                'country_code' => $numberProto->getCountryCode(),
                'mobile'       => (string) $numberProto->getNationalNumber(),
            ];
        } catch (NumberParseException $e) {
            return [
                'error' => 'Invalid phone number format',
            ];
        }
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required|min:8',
        ], [
            'email.required'    => __('frontend-labels.validation.email_required') ?? 'Email is required',
            'email.email'       => __('frontend-labels.validation.email_invalid') ?? 'Invalid email format',
            'password.required' => __('frontend-labels.validation.password_required') ?? 'Password is required',
            'password.min'      => __('frontend-labels.validation.password_min') ?? 'Password must be at least 8 characters',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error'  => true,
                'errors' => $validator->errors(),
            ], 422);
        }

        $userCheck = User::where('email', $request->email)->first();

        if (! $userCheck) {
            return response()->json([
                'error'   => true,
                'message' => __('frontend-labels.validation.invalid_email'),
                'data'    => 'email',
            ]);
        }

        // Check if user is inactive
        if ($userCheck->is_blocked && $userCheck->block_type === 'full') {
            return response()->json([
                'error'   => true,
                'message' => 'Your account has been fully blocked. Please contact the administrator.',
                'data'    => 'email',
            ]);
        }

        if ($userCheck->status === 'inactive') {
            return response()->json([
                'error'   => true,
                'message' => 'Your account has been deactivated. Please contact the administrator.',
                'data'    => 'email',
            ]);
        }

        // Attempt login
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            return response()->json([
                'status'   => 'success',
                'message'  => __('frontend-labels.validation.login_success'),
                'redirect' => route('home'),
            ]);
        }

        return response()->json([
            'error'   => true,
            'message' => __('frontend-labels.validation.invalid_password'),
            'data'    => 'password',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->back();
    }

    public function deleteAccount(Request $request)
    {
        $user_id = Auth::user()->id ?? null;

        if ($user_id != null) {

            PostView::where('user_id', $user_id)->delete();

            $bookmarks = Favorite::where('user_id', $user_id)->get();
            foreach ($bookmarks as $bookmark) {
                $post = Post::find($bookmark->post_id);

                $post->decrement('favorite');
                $bookmark->delete();
            }

            $channelList = ChannelSubscriber::where('user_id', $user_id)->get();
            if ($channelList) {
                foreach ($channelList as $channel) {
                    $channelProfile = Channel::find($channel->channel_id);
                    $channelProfile->subscribers()->detach($user_id);
                    $channelProfile->decrement('follow_count');
                }
            }

            $comments = Comment::where('user_id', $user_id)->orderBy('id', 'desc')->get();
            if ($comments) {
                foreach ($comments as $comment) {
                    $post = Post::find($comment->post_id);
                    $comment->delete();
                    if ($post->comment > 0) {
                        $post->decrement('comment');
                    }
                }
            }
        }
        if ($user_id != 1) {
            $user = User::find($user_id);
            $user->forceDelete();
        }

        return response()->json([
            'error'   => false,
            'message' => __('frontend-labels.validation.user_removed'),
        ]);
    }
}
