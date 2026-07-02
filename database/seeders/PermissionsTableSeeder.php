<?php
namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'list-post', 'create-post', 'update-post', 'delete-post', 'select-topic-for-post', 'select-channel-for-post', 'select-newslanguage-for-post', 'send-notification-any-post', 'view-comment-any-post',
            'list-VideoPost', 'create-custom-VideoPost', 'create-youtube-VideoPost', 'update-custom-VideoPost', 'update-youtube-VideoPost', 'delete-custom-VideoPost', 'delete-youtube-VideoPost', 'select-channel-for-VideoPost', '', 'select-newslanguage-for-VideoPost', 'send-notification-any-VideoPost', 'view-comment-any-VideoPost',
            'list-AudioPost', 'create-AudioPost', 'update-AudioPost', 'delete-AudioPost', 'select-topic-for-AudioPost', 'select-channel-for-AudioPost', 'select-newslanguage-for-AudioPost', 'send-notification-any-AudioPost', 'view-comment-any-AudioPost',
            'list-story', 'create-story', 'update-story', 'delete-story', 'select-newslanguage-for-story', 'select-topic-for-story',
            'list-newslanguage', 'create-newslanguage', 'update-newslanguage', 'delete-newslanguage', 'reorder-newslanguage', 'status-newslanguage',
            'list-channel', 'create-channel', 'update-channel', 'delete-channel', 'update-status-channel', 'select-newslanguage-for-channel',
            'list-topic', 'create-topic', 'update-topic', 'delete-topic', 'status-topic', 'select-newslanguage-for-topic',
            'list-rssfeed', 'create-rssfeed', 'update-rssfeed', 'delete-rssfeed', 'update-status-rssfeed', 'select-newslanguage-for-rssfeed', 'select-topic-for-rssfeed', 'select-channel-for-rssfeed', 'sync-all-rssfeed', 'sync-single-rssfeed',
            'list-enewspapaer', 'create-enewspapaer', 'update-enewspapaer', 'delete-enewspapaer', 'select-newslanguage-for-enewspapaer', 'select-channel-for-enewspapaer',
            'list-CustomAds', 'view-details-CustomAds', 'change-status-CustomAds',
            'list-user', 'create-user', 'update-user', 'delete-user', 'update-status-user',
            'list-plan', 'create-plan', 'update-plan', 'delete-plan',
            'list-subscription',
            'list-transaction', 'show-transaction',
            'list-emailtemplate', 'create-emailtemplate', 'view-detail-emailtemplate', 'delete-emailtemplate', 'change-status-emailtemplate',
            'list-SponsorEmailtemplate', 'create-SponsorEmailtemplate', 'view-detail-SponsorEmailtemplate', 'delete-SponsorEmailtemplate', 'change-status-SponsorEmailtemplate',
            'list-subscribers',
            'list-notification', 'create-notification', 'delete-notification', 'view-users-notification', 'upload-image-notification',
            'list-reported-comment', 'delete-reported-comment', 'list-blocked-comment',
            'list-contactus', 'delete-contactus', 'view-contactus',
            'list-role', 'create-role', 'delete-role', 'update-role',
            'list-adminuser', 'create-adminuser', 'delete-adminuser', 'edit-password-adminuser',
            'payment-gateway-settings', 'custom-advertising-settings', 'credit-packs-settings', 'about-us-settings', 'terms-conditions-settings', 'newslanguage-settings', 'basic-company-setup-settings', 'logo-management-and-web-settings', 'subscription-model-and-header/footer-script-settings', 'social-link-and-other-settings', 'smtp-mail-configuration-settings', 'privacy-policy-settings', 'language-translation-settings', 'error-logs-view-settings', 'system-update-settings', 'firebase-settings',
            'cronjob/info-in-settings', 'app-admob-and-weather-settings', 'system-health-settings', 'google-adsense-configuration', 'notification-settings',
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->updateOrInsert(
                ['name' => $permission], // condition to check existing
                [
                    'guard_name' => 'web',
                    'updated_at' => Carbon::now(),
                    'created_at' => DB::raw('COALESCE(created_at, NOW())'),
                ]
            );
        }
        DB::table('permissions')->whereNotIn('name', $permissions)->delete();
    }
}
