<?php
namespace App\Http\Controllers;

use App\Models\Setting;
use Carbon\Carbon;

class AboutUsController extends Controller
{
    const TIME_FORMATE = 'Y-m-d H:i';
    public function index()
    {
        $title    = __('frontend-labels.aboutus.title');
        $settings = \Illuminate\Support\Facades\Cache::rememberForever('view_composer_settings_list', function () {
            return \Illuminate\Support\Facades\DB::table('settings')->select('name', 'value', 'updated_at')->get()->keyBy('name');
        });

        $about_us = $settings->get('about_us') ?? null;

        if (! $about_us) {
            $about_us = (object)[
                'value'      => 'About us not set',
                'updated_at' => Carbon::now()->toDateTimeString(),
            ];
        } else {
            // Clone the object to avoid mutating the shared cache instance
            $about_us = clone $about_us;
        }

        $about_us->updated_at = Carbon::parse($about_us->updated_at)->format(self::TIME_FORMATE);
        $theme                = getTheme();
        $data                 = compact('title', 'about_us', 'theme');
        return view('front_end/' . $theme . '/pages/about-us', $data);
    }
}