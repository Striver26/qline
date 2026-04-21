<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureActive
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if ($user) {
            if (!$user->is_active) {
                auth()->logout();
                return redirect()->route('login')->with('error', 'Your account has been deactivated.');
            }

            if ($user->business && !$user->business->is_active) {
                // If it's a business owner/staff, block access to business routes
                // We might want to allow them to access billing to reactivate, but for now block all.
                if ($request->is('business*')) {
                    return redirect()->route('dashboard')->with('error', 'Your business account is currently inactive.');
                }
            }
        }

        return $next($request);
    }
}
