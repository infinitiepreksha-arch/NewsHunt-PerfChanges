<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DemoMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request): (Response|RedirectResponse) $next
     * @return RedirectResponse|JsonResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (! config('app.demo_mode')) {
            return $next($request);
        }

        $uri = $request->getRequestUri();

        // Check if the request should be allowed
        if ($this->shouldAllowRequest($uri, $request)) {
            return $next($request);
        }

        // Log and return error response for restricted actions
        return $this->restrictedResponse($uri, Auth::user(), $request);
    }

    /**
     * Determine if the request should be allowed.
     *
     * @param string $uri
     * @param Request $request
     * @return bool
     */
    private function shouldAllowRequest(string $uri, Request $request): bool
    {
        $excludedUris = [
            '/comments/store',
            '/comments/update',
            '/comments/delete',
            '/user-signup',
            '/api/user-signup',
            '/logout',
            '/api/manage-favourite',
            "/follow-unfollow-language",
            '/subscribe/store',
            '/set-web-language',
            '/my-account/favorites/toggle-pin',
            '/my-account/bookmarks',
            'posts/favorite',
            'posts/*/react',
            'posts/*/reactions',
            'posts/*/reactors',
            'change-password-via-email',
            'password/reset/*',
        ];

        $restrictedPatterns = [
            '*edit*',   // Block URIs containing "edit"
            '*update*', // Block URIs containing "update"
            '*delete*', // Block URIs containing "delete"
        ];

        // APIs should always be allowed
        if ($request->is('api/*')) {
            return true;
        }

        // Allow excluded URIs

        // ✅ Allow excluded URIs (CORRECT)
        if ($request->is($excludedUris)) {
            return true;
        } elseif (in_array($uri, $excludedUris)) {
            return true;
        }

        // Block URIs that match restricted patterns
        foreach ($restrictedPatterns as $pattern) {
            if (fnmatch($pattern, $uri)) {
                return false; // Deny the request if it matches any restricted pattern
            }
        }

        // Allow specific users or GET requests
        $user = Auth::user();
        return $this->isAllowedUser($user) || $request->isMethod('get');
    }

    /**
     * Determine if the user is allowed to perform actions.
     *
     * @param mixed $user
     * @return bool
     */
    private function isAllowedUser($user): bool
    {
        if (! $user) {
            return true; // Allow if no user is logged in
        }

        $demoMobile     = config('demo.demo_mobile', '9876598765');
        $demoAdminEmail = config('demo.demo_admin_email', 'user@gmail.com');

        if ($user->mobile !== $demoMobile && $user->hasRole('User')) {
            return true;
        }

        return $user->email === $demoAdminEmail && $user->hasRole('Admin');
    }

    /**
     * Build the restricted response for disallowed actions.
     *
     * @param string $uri
     * @param mixed $user
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    private function restrictedResponse(string $uri, $user, Request $request)
    {
        Log::warning('Blocked action in demo mode', [
            'uri'     => $uri,
            'user_id' => $user ? $user->id : null, // Log user ID only
        ]);

        $demoMessage = 'You do not have permission to perform this action in demo mode.';

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'error'   => true,
                'message' => $demoMessage,
            ], 403);
        }

        // For admin URLs, redirect back with error flash so iziToast can display it
        if ($request->is('admin/*') || $request->is('admin')) {
            return redirect()->back()->with('demo_restricted', $demoMessage);
        }

        return response()->view('admin.errors.demo_restricted', [
            'message' => "🚫 This action is not allowed in the Demo Version.",
            'code'    => 112,
        ], 403);
    }
}
