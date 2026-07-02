<?php

namespace App\Providers;

use App\Services\CachingService;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider {
    /**
     * Register services.
     */
    public function register(): void {
        /*** Header File ***/
        View::composer('admin.layouts.topbar', static function (\Illuminate\View\View $view) {
            $view->with('languages', CachingService::getLanguages());
        });

        View::composer('admin.layouts.sidebar', static function (\Illuminate\View\View $view) {
            $settings = CachingService::getSystemSettings('company_logo');
            $view->with('company_logo', $settings ?? '');
        });

        View::composer('admin.layouts.main', static function (\Illuminate\View\View $view) {
            $settings = CachingService::getSystemSettings('favicon_icon');
            $company_name = CachingService::getSystemSettings('company_name');
            $view->with('favicon', $settings ?? '');
            $view->with('app_name', $company_name ?? '');
            $view->with('lang', Session::get('language'));
        });

        View::composer('auth.login', static function (\Illuminate\View\View $view) {
            $favicon_icon = CachingService::getSystemSettings('favicon_icon');
            $company_logo = CachingService::getSystemSettings('company_logo');
            $login_image = CachingService::getSystemSettings('web_logo');
            $view->with('company_logo', $company_logo ?? '');
            $view->with('favicon', $favicon_icon ?? '');
            $view->with('login_bg_image', $login_image ?? '');
        });

        
        View::composer('auth.passwords.email', static function (\Illuminate\View\View $view) {
            $favicon_icon = CachingService::getSystemSettings('favicon_icon');
            $company_logo = CachingService::getSystemSettings('company_logo');
            $login_image = CachingService::getSystemSettings('web_logo');
            $view->with('company_logo', $company_logo ?? '');
            $view->with('favicon', $favicon_icon ?? '');
            $view->with('login_bg_image', $login_image ?? '');
        });
        
        View::composer('front_end.classic.layout.footer', static function (\Illuminate\View\View $view) {
            $view->with('languages', CachingService::getLanguages());
        });

        View::composer('front_end/classic/pages/webstory', static function (\Illuminate\View\View $view) {
            $favicon_icon = CachingService::getSystemSettings('favicon_icon');
            $view->with('favicon', $favicon_icon ?? '');
        });
        View::composer('front_end/classic/pages/webstory_slide', static function (\Illuminate\View\View $view) {
            $favicon_icon = CachingService::getSystemSettings('favicon_icon');
            $view->with('favicon', $favicon_icon ?? '');
        });
        
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void {
        //
    }
}
