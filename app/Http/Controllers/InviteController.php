<?php

namespace App\Http\Controllers;

use App\Models\Platform\Invitation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class InviteController extends Controller
{
    public function show($token)
    {
        $invitation = Invitation::where('token', $token)->firstOrFail();

        if ($invitation->expires_at->isPast() || $invitation->accepted_at !== null) {
            abort(403, 'This invitation link is invalid or has expired.');
        }

        return view('auth.invite', compact('invitation'));
    }

    public function process(Request $request, $token)
    {
        $invitation = Invitation::where('token', $token)->firstOrFail();

        if ($invitation->expires_at->isPast() || $invitation->accepted_at !== null) {
            abort(403, 'This invitation link is invalid or has expired.');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $invitation->email,
            'password' => Hash::make($request->password),
            'role' => $invitation->role,
            'business_id' => $invitation->business_id,
            'profile_completed' => true,
        ]);

        $invitation->update(['accepted_at' => now()]);

        auth()->login($user);

        // Redirect based on role
        if ($user->role->value === 'platform_staff') {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('business.dashboard');
    }
}
