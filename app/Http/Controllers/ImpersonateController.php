<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Enums\UserRole;

class ImpersonateController extends Controller
{
    public function take(User $user)
    {
        if (!in_array(auth()->user()->role, [UserRole::SUPERADMIN, UserRole::PLATFORM_STAFF])) {
            abort(403, 'Unauthorized action.');
        }

        // Prevent impersonating yourself or other superadmins (optional, but good practice)
        if ($user->id === auth()->id()) {
            return back()->with('status', 'Cannot impersonate yourself.');
        }

        session(['impersonated_by' => auth()->id()]);
        Auth::login($user);

        return redirect()->route('dashboard')->with('status', 'Impersonating ' . $user->name);
    }

    public function leave()
    {
        if (!session()->has('impersonated_by')) {
            abort(403, 'Not impersonating anyone.');
        }

        $originalUserId = session('impersonated_by');
        session()->forget('impersonated_by');

        Auth::loginUsingId($originalUserId);

        return redirect()->route('admin.users')->with('status', 'Left impersonation.');
    }
}
