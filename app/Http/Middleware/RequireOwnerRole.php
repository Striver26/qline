<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireOwnerRole
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()?->isOwner()) {
            abort(403, 'Only business owners can access this page.');
        }

        return $next($request);
    }
}
