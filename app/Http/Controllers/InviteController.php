<?php

namespace App\Http\Controllers;

use App\Models\Platform\Invitation;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;

class InviteController extends Controller
{
    public function show($token)
    {
        $invitation = $this->getValidInvitation($token);

        return view('auth.invite', compact('invitation'));
    }

    public function process(Request $request, $token)
    {
        $invitation = $this->getValidInvitation($token);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $this->createUserSession($invitation, $validated);

        return redirect()->route('dashboard');
    }

    /**
     * Extract and validate invitation tokens against expiration rules.
     */
    private function getValidInvitation(string $token): Invitation
    {
        $invitation = Invitation::where('token', $token)->firstOrFail();

        if ($invitation->expires_at->isPast() || $invitation->accepted_at !== null) {
            abort(403, 'This invitation link is invalid or has expired.');
        }

        return $invitation;
    }

    /**
     * Map invitation payload into an active user entity securely.
     */
    private function createUserSession(Invitation $invitation, array $validatedData): void
    {
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $invitation->email,
            'password' => Hash::make($validatedData['password']),
            'role' => $invitation->role,
            'business_id' => $invitation->business_id,
            'profile_completed' => true,
            'email_verified_at' => now(),
        ]);

        $invitation->update(['accepted_at' => now()]);

        Auth::login($user);
    }
}
