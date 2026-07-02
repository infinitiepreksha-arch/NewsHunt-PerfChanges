<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetWebLocale
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->is('admin*') && session()->has('web_locale')) {
            App::setLocale(session('web_locale'));
        }

        return $next($request);
    }
}
