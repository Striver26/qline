<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Enums\UserRole;

class RequireAdminRole
{
    /**
     * Only allow superadmin and platform_staff roles to access admin routes.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $role = $request->user()?->role;

        if (!in_array($role, [UserRole::SUPERADMIN, UserRole::PLATFORM_STAFF], true)) {
            abort(403, 'Access denied. Platform admin privileges required.');
        }

        return $next($request);
    }
}
