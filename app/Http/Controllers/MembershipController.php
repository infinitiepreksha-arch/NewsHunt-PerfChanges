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
        
        $paymentSetting = \Illuminate\Support\Facades\Cache::rememberForever('active_payment_setting', function () {
            return PaymentSetting::where('status', true)->first();
        });
        $currency       = $paymentSetting->currency_symbol ?? '$';

        $freeTrialStatus = \App\Services\CachingService::getSystemSettings('free_trial_status') ?? '0';

        // Redirect if free trial is enabled
        if ($freeTrialStatus == '1') {
            return redirect()->route('home');
        }

        $user_data = $user = Auth::user();
        if ($user) {
            $user->load('subscription');
        }

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
            'free_trial_status' => $freeTrialStatus,
        ];
        return view("front_end/{$theme}/pages/membership_plan", $data);
    }
}
