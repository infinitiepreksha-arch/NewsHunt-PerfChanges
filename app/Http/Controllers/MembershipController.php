<?php
namespace App\Http\Controllers;

use App\Models\PaymentSetting;
use App\Models\Plan;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class MembershipController extends Controller
{
    public function index()
    {
        $theme          = getTheme();
        $title          = __('frontend-labels.membership.title');
        $paymentSetting = PaymentSetting::where('status', true)->first();
        $currency       = $paymentSetting->currency_symbol ?? '$';

        $membershipSettings = Setting::whereIn('name', [
            'free_trial_status',
        ])->pluck('value', 'name');

        // Redirect if free trial is enabled
        if (($membershipSettings['free_trial_status'] ?? 0) == 1) {
            return redirect()->route('home'); // or redirect('/');
        }

        $user_data = $user = Auth::user();

        // Eager load the relationships to avoid N+1 query issues
        $membership_data = Plan::with(['features_plan', 'planTenures' => function ($query) {
            $query->orderBy('price', 'asc');
        }])
            ->where('status', 1)
            ->get();

        $data = [
            'theme'             => $theme,
            'title'             => $title,
            'membership_data'   => $membership_data,
            'currency'          => $currency,
            'user'              => $user_data,
            'payment'           => $paymentSetting,
            'free_trial_status' => $membershipSettings['free_trial_status'] ?? '',
        ];
        return view("front_end/{$theme}/pages/membership_plan", $data);
    }
}
