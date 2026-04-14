<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use App\Enums\UserRole;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $role = auth()->user()->role;
        
        if ($role === UserRole::SUPERADMIN || $role === UserRole::PLATFORM_STAFF) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->intended(config('fortify.home'));
    }
}
