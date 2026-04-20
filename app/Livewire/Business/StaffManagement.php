<?php

namespace App\Livewire\Business;

use Livewire\Component;
use App\Models\Platform\Invitation;
use App\Models\User;
use Illuminate\Support\Str;

class StaffManagement extends Component
{
    #[\Livewire\Attributes\Validate('required|email|unique:users,email')]
    public $email;

    public function generateInvite()
    {
        $this->validate();

        $invitation = $this->createInvitationRecord();
        $this->dispatchInvitationEmail($invitation);

        $this->reset('email');
        session()->flash('success', 'Invitation sent to ' . $invitation->email . '!');
    }

    private function createInvitationRecord(): Invitation
    {
        return Invitation::create([
            'email' => $this->email,
            'role' => \App\Enums\UserRole::BUSINESS_STAFF->value ?? 'business_staff',
            'business_id' => auth()->user()->business_id,
            'invited_by' => auth()->id(),
            'token' => Str::random(32),
            'expires_at' => now()->addDays(3),
        ]);
    }

    private function dispatchInvitationEmail(Invitation $invitation): void
    {
        \Illuminate\Support\Facades\Mail::to($this->email)->send(
            new \App\Mail\StaffInvitationMail(
                invitation: $invitation,
                businessName: auth()->user()->business->name ?? 'Your Business',
                inviterName: auth()->user()->name,
            )
        );
    }

    public function revokeInvite(int $inviteId)
    {
        $invite = Invitation::where('business_id', auth()->user()->business_id)
            ->where('id', $inviteId)
            ->first();
            
        if ($invite) {
            $invite->delete();
        }
    }

    public function deleteStaff(int $staffId)
    {
        $staff = User::where('business_id', auth()->user()->business_id)
            ->where('id', $staffId)
            ->where('id', '!=', auth()->id()) // Prevent self delete
            ->first();

        if ($staff) {
            $staff->delete();
            session()->flash('success', 'Staff member removed.');
        }
    }

    public function render()
    {
        $staff = User::where('business_id', auth()->user()->business_id)->get();
            
        $invitations = Invitation::where('business_id', auth()->user()->business_id)
            ->whereNull('accepted_at')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('livewire.business.staff-management', compact('staff', 'invitations'))
            ->layout('layouts.app');
    }
}
