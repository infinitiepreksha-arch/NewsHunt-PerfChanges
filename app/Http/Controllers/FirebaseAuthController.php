<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FirebaseAuthController extends Controller
{
    public function googleCallback(Request $request)
    {
        $user = User::firstOrCreate(
            ['email' => $request->email],
            ['name' => $request->name, 'password' => bcrypt(str_random(16))]
        );

        // Login user
        Auth::login($user);

        return response()->json(['redirect_url' => '/dashboard']);
    }

    public function phoneCallback(Request $request)
    {
        $user = User::firstOrCreate(
            ['phone' => $request->phone],
            ['uid' => $request->uid]
        );

        Auth::login($user);

        return response()->json(['redirect_url' => '/dashboard']);
    }
}
