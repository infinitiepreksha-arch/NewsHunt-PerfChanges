<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{
    public function handle(Request $request, Closure $next, $permission)
    {
        $user = Auth::user();
        if ($user) {
            $user->load(['permissions', 'roles.permissions']);
        }
        if (! $user) {
            return $this->deny($request);
        }

        if ($user->hasRole('Admin')) {
            return $next($request);
        }

        if (! $user->hasPermission($permission)) {
            return $this->deny($request);
        }

        return $next($request);
    }
    protected function deny(Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'status'  => false,
                'message' => 'You do not have permission to perform this action.',
            ], 403);
        }

        return redirect()->route('permission-restriction')
            ->with('message', "🚫 You do not have permission to access this page.");
    }
}
