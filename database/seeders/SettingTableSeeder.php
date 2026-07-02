<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingTableSeeder extends Seeder
{
   /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->insertBasicSettings();
        $this->insertAppLinks();
        $this->insertCompanyInfo();
        $this->insertUISettings();
        $this->insertSocialLinks();
        $this->insertOtherSettings();
        $this->insertSubscriptionSettings();
        $this->insertScriptSettings();
    }

    /**
     * Insert basic app settings.
     */
    private function insertBasicSettings()
    {
        DB::table('settings')->insert([
            [
                'name' => 'currency_symbol',
                'value' => '$',
                'type' => 'string',
            ],
            [
                'name' => 'ios_version',
                'value' => '1.0.0',
                'type' => 'string',
            ],
            [
                'name' => 'default_language',
                'value' => 'en',
                'type' => 'string',
            ],
            [
                'name' => 'force_update',
                'value' => '0',
                'type' => 'string',
            ],
            [
                'name' => 'android_version',
                'value' => '1.0.0',
                'type' => 'string',
            ],
            [
                'name' => 'number_with_suffix',
                'value' => '0',
                'type' => 'string',
            ],
            [
                'name' => 'maintenance_mode',
                'value' => '0',
                'type' => 'string',
            ],
        ]);
    }

    /**
     * Insert app store and play store links.
     */
    private function insertAppLinks()
    {
        DB::table('settings')->insert([
            [
                'name' => 'app_store_link',
                'value' => 'https://apps.apple.com/fr/app/microsoft-word/id462054704?l=en-GB&mt=12',
                'type' => 'string',
            ],
            [
                'name' => 'play_store_link',
                'value' => 'https://play.google.com/store/apps/details?id=eShop.multivendor.customer',
                'type' => 'string',
            ],
        ]);
    }

    /**
     * Insert company contact information.
     */
    private function insertCompanyInfo()
    {
        DB::table('settings')->insert([
            [
                'name' => 'company_tel1',
                'value' => '556',
                'type' => 'string',
            ],
            [
                'name' => 'company_email',
                'value' => 'admin@gmail.com',
                'type' => 'string',
            ],
            [
                'name' => 'company_name',
                'value' => 'News Hunt',
                'type' => 'string',
            ],
            [
                'name' => 'company_logo',
                'value' => 'settings/SVWBAcuJnIkmg72grqFmXm0fW88qJrmvEY10Vmuy.png',
                'type' => 'file',
            ],
            [
                'name' => 'favicon_icon',
                'value' => 'settings/6i5URs2NwQ4JiuaNMv1wFGeZpHqTSPusr0vopNcB.png',
                'type' => 'file',
            ],
        ]);
    }

    /**
     * Insert UI related settings (e.g., colors, images).
     */
    private function insertUISettings()
    {
        DB::table('settings')->insert([
            [
                'name' => 'login_image',
                'value' => 'assets/images/bg/login.jpg',
                'type' => 'file',
            ],
            [
                'name' => 'web_theme_color',
                'value' => '#cc0000',
                'type' => 'string',
            ],
            [
                'name' => 'web_logo',
                'value' => 'settings/sBHorJrsjaa43CnJQYZuionzn5rSDC4qVqCa8SEw.png',
                'type' => 'file',
            ],
            [
                'name' => 'placeholder_image',
                'value' => 'settings/DOsROWV1JzNzei0p6K2wKLSFZfQUp7tvxcwTEuge.png',
                'type' => 'file',
            ],
            [
                'name' => 'dark_logo',
                'value' => 'settings/LvR0Ze6lMJJwuxc6VPQ34Uk5Y0WcIHyK45A5zwby.png',
                'type' => 'file',
            ],
            [
                'name' => 'light_logo',
                'value' => 'settings/u58gjqVct13TMfAF0PRP5JKCVtMp2lAYpMXnupR3.png',
                'type' => 'file',
            ],
        ]);
    }

    /**
     * Insert social media links.
     */
    private function insertSocialLinks()
    {
        DB::table('settings')->insert([
            [
                'name' => 'instagram_link',
                'value' => 'https://www.instagram.com/',
                'type' => 'string',
            ],
            [
                'name' => 'x_link',
                'value' => 'https://x.com/?lang=en',
                'type' => 'string',
            ],
            [
                'name' => 'facebook_link',
                'value' => 'https://www.facebook.com/login/?next=https%3A%2F%2Fwww.facebook.com%2F',
                'type' => 'string',
            ],
            [
                'name' => 'linkedin_link',
                'value' => 'https://in.linkedin.com/',
                'type' => 'string',
            ],
            [
                'name' => 'pinterest_link',
                'value' => 'https://www.youtube.com/',
                'type' => 'string',
            ],
        ]);
    }

    /**
     * Insert other miscellaneous settings.
     */
    private function insertOtherSettings()
    {
        DB::table('settings')->insert([
            [
                'name' => 'gecode_xyz_api_key',
                'value' => null,
                'type' => 'string',
            ],
            [
                'name' => 'footer_description',
                'value' => null,
                'type' => 'string',
            ],
            [
                'name' => 'google_map_iframe_link',
                'value' => null,
                'type' => 'string',
            ],
            [
                'name' => 'news_label_place_holder',
                'value' => 'Blogs',
                'type' => 'string',
            ],
            [
                'name' => 'free_trial_post_limit',
                'value' => '10',
                'type' => 'string'
            ],
            [
                'name' => 'free_trial_story_limit',
                'value' => '10',
                'type' => 'string'
            ],
            [
                'name' => 'free_trial_status',
                'value' => '0',
                'type' => 'string'
            ]
        ]);
    }

    /**
     * Insert subscription-related settings.
     */
    private function insertSubscriptionSettings()
    {
        DB::table('settings')->insert([
            [
                'name' => 'subscribe_model_title',
                'value' => 'Subscribe to the News Hunt',
                'type' => 'string',
            ],
            [
                'name' => 'subscribe_model_sub_title',
                'value' => 'Join 10k+ people to get notified about new posts, news and tips.',
                'type' => 'string',
            ],
            [
                'name' => 'subscribe_model_status',
                'value' => '1',
                'type' => 'string',
            ],
            [
                'name' => 'subscribe_model_image',
                'value' => 'settings/NsrItHhAIKMMA9bLVDQMyGNuEL6bSY5sL2wjvdDk.png',
                'type' => 'file',
            ],
        ]);
    }

    /**
     * Insert script settings (header and footer scripts).
     */
    private function insertScriptSettings()
    {
        DB::table('settings')->insert([
            [
                'name' => 'header_script',
                'value' => null,
                'type' => 'string',
            ],
            [
                'name' => 'footer_script',
                'value' => null,
                'type' => 'string',
            ],
            [
                'name' => 'android_shceme',
                'value' => 'newshunt',
                'type' => 'string',
            ],
            [
                'name' => 'ios_shceme',
                'value' => 'newshunt',
                'type' => 'string',
            ],
            [
                'name' => 'app_name',
                'value' => 'News Hunt',
                'type' => 'string',
            ],
        ]);
    }
}
