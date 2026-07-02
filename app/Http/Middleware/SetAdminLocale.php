<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetAdminLocale
{
    public function handle(Request $request, Closure $next)
    {
        if (session()->has('admin_locale')) {
            App::setLocale(session('admin_locale'));
        }
        return $next($request);

    }
}
