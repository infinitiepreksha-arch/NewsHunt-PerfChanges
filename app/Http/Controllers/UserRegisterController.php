<?php
namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserRegisterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $title   = __('frontend-labels.register.title');
        $appName = Setting::where('name', 'company_name')->value('value');
        $theme   = getTheme();
        return view('front_end/' . $theme . '/pages/user-register', compact('theme', 'title', 'appName'));
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

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'         => 'required|string|max:255',
            'email'        => 'required|string|email|max:255|unique:users',
            'password'     => [
                'required',
                'string',
                'confirmed',
                'min:8',
                'regex:/[a-z]/',     // lowercase
                'regex:/[A-Z]/',     // uppercase
                'regex:/[0-9]/',     // number
                'regex:/[@$!%*?&]/', // special char
            ],
            'accept_terms' => 'required|accepted',
        ], [
            'name.required'         => __('frontend-labels.Registration_validation.name_required'),
            'email.required'        => __('frontend-labels.Registration_validation.email_required'),
            'email.email'           => __('frontend-labels.Registration_validation.email_invalid'),
            'email.unique'          => __('frontend-labels.Registration_validation.email_exists'),
            'password.required'     => __('frontend-labels.Registration_validation.password_required'),
            'password.confirmed'    => __('frontend-labels.Registration_validation.password_confirmed'),
            'password.min'          => __('frontend-labels.Registration_validation.password_min'),
            'password.regex'        => __('frontend-labels.Registration_validation.password_format'),
            'accept_terms.required' => __('frontend-labels.Registration_validation.accept_terms_required'),
            'accept_terms.accepted' => __('frontend-labels.Registration_validation.accept_terms_accepted'),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole('user');
        Auth::login($user);

        return response()->json([
            'status'   => 'success',
            'message'  => __('frontend-labels.Registration_validation.register_success') ?? 'Registration successful!',
            'redirect' => route('home'),
        ]);
    }

}
