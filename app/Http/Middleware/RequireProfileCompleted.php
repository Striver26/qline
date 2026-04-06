<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireProfileCompleted
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && ! $request->user()->profile_completed) {
            // Avoid redirect loops if they are already on the profile or logout pages
            if (! $request->routeIs('business.settings') && ! $request->routeIs('logout')) {
                return redirect()->route('business.settings')
                    ->with('warning', 'Please complete your profile before subscribing or accessing the business panel.');
            }
        }

        return $next($request);
    }
}
