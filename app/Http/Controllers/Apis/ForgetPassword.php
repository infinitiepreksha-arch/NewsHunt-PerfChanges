<?php
namespace App\Http\Controllers\Apis;

use App\Http\Controllers\Controller;
use App\Models\PasswordReset;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\support\Str;

class ForgetPassword extends Controller
{
    /* Request for reset password */
    public function forgetPassword(Request $request)
    {
        try {
            $user = User::where('email', $request->email)->get();
            if ($user) {

                $token  = Str::random(40);
                $domain = url('/');
                $url    = $domain . '/reset-password?token=' . $token;

                $data['url']   = $url;
                $data['email'] = $request->email;
                $data['title'] = 'Pawword Reset';
                $data['body']  = 'Please check below link to foreget your password.';
                Mail::send('change_password.forgetPasswordMail', ['data' => $data], function ($message) use ($data) {
                    $message->to($data['email'])->subject($data['title']); // Ensure this is set correctly

                });

                $datetime = time();

                DB::table('password_resets')->updateOrInsert(
                    ['email' => $request->email],
                    [
                        'token'      => $token,
                        'created_at' => $datetime,
                    ]
                );
                return response()->json(['error' => false, 'message' => 'Please check your email to reset password']);
            } else {
                return response()->json(['error' => false, 'message' => 'Email does not exest.']);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => false, 'message' => $e->getMessage()]);

        }
    }

    /* Password Reset form */
    public function resetPasswordLoad(Request $request)
    {
        try {
            $resetData = PasswordReset::where('token', $request->token)->first();
            $response  = null;

            if ($resetData) {
                $user = User::where('email', $resetData->email)->first();

                if ($user) {

                    return view('front_end.classic.pages.forget-password', compact('user'));
                } else {

                    $response = response()->json(['error' => true, 'message' => 'User  associated with this token does not exist.'], 404);
                }
            } else {

                $response = response()->json(['error' => true, 'message' => 'Your token is expired or invalid.'], 400);
            }

            return $response ?: response()->json(['error' => false, 'message' => 'Unexpected error occurred.'], 500);
        } catch (\Exception $e) {
            return response()->json(['error' => true, 'message' => $e->getMessage()], 500);
        }
    }

    /* Password reset */
    public function resetPassword(Request $request)
    {

        try {
            $request->validate([
                'password' => 'required|string|min:8|confirmed',
            ]);
            $user           = User::find($request->id);
            $user->password = Hash::make($request->password);
            $user->save();

            $success = PasswordReset::where('email', $user->email)->delete();
            if ($success) {
                response()->json(['error' => true, 'message' => 'Password reset successfully.']);
            }
            return redirect()->route('home');
        } catch (\Exception $e) {
            return response()->json(['error' => false, 'message' => $e->getMessage()]);
        }

    }
}
