<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class Authenticate extends Middleware {
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param Request $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        Log::info('Authenticate middleware hit');
        if (!$request->expectsJson()) {
            return route('admin.login');
        }
    }

    /**
     * Handle an unauthenticated user.
     *
     * @param Request $request
     * @param array $guards
     * @return \Illuminate\Http\JsonResponse
     */
    protected function unauthenticated($request, array $guards) {
        return response()->json(['error' => 'Unauthorized.'], 401);
    }
}
