<?php
namespace App\Http\Controllers;

use App\Models\Setting;
use Carbon\Carbon;

class FooterController extends Controller
{
    /*
    *
    *Privacy & Policy
    */
    public function privacyEndPolicy()
    {
        $title = __('frontend-labels.privacy_policy.title');

        $settings = \Illuminate\Support\Facades\Cache::rememberForever('view_composer_settings_list', function () {
            return \Illuminate\Support\Facades\DB::table('settings')->select('name', 'value', 'updated_at')->get()->keyBy('name');
        });

        $privacyPolicy = $settings->get('privacy_policy') ?? null;

        // Attempt to read property "updated_at" on null
        if (! $privacyPolicy) {
            $privacyPolicy = (object)[
                'value'      => 'Privacy Policy not set',
                'updated_at' => Carbon::now()->toDateTimeString(),
            ];
        }

        $theme = getTheme();
        $data  = compact('title', 'privacyPolicy', 'theme');
        return view('front_end/' . $theme . '/pages/privacy', $data);
    }
    /*
     *
     *Term & Conditions
     *
     */
    public function termsAndCondition()
    {
        $title = __('frontend-labels.terms_and_conditions.title');

        $settings = \Illuminate\Support\Facades\Cache::rememberForever('view_composer_settings_list', function () {
            return \Illuminate\Support\Facades\DB::table('settings')->select('name', 'value', 'updated_at')->get()->keyBy('name');
        });

        $termsOfCondition = $settings->get('terms_conditions') ?? null;

        if (! $termsOfCondition) {
            $termsOfCondition = (object)[
                'value'      => 'Terms & Conditions not set',
                'updated_at' => Carbon::now()->toDateTimeString(),
            ];
        }

        $theme = getTheme();
        $data  = compact('title', 'termsOfCondition', 'theme');
        return view('front_end/' . $theme . '/pages/terms_and_condition', $data);
    }
}
