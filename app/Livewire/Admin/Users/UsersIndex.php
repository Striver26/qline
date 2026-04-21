<?php

namespace App\Livewire\Admin\Users;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UsersIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $filterRole = '';   // FIX: was missing — blade had wire:model.live="filterRole"

    public $inviteEmail = '';
    public $inviteRole = 'platform_staff';

    public function updatedSearch()
    {
        $this->resetPage();
    }
    public function updatedFilterRole()
    {
        $this->resetPage();
    } // FIX: was missing

    public function inviteStaff()
    {
        $this->validate([
            'inviteEmail' => ['required', 'email', Rule::unique('users', 'email')],
            'inviteRole' => ['required', 'string', Rule::enum(\App\Enums\UserRole::class)],
        ]);

        if ($this->inviteRole === \App\Enums\UserRole::SUPERADMIN->value && auth()->user()->role !== \App\Enums\UserRole::SUPERADMIN) {
            session()->flash('error', 'Only superadmins can create other superadmins.');
            return;
        }

        $password = Str::random(10);

        User::create([
            'name' => 'Invited Staff',
            'email' => $this->inviteEmail,
            'password' => Hash::make($password),
            'role' => $this->inviteRole,
            'email_verified_at' => now(),
            'profile_completed' => true,
        ]);

        \Illuminate\Support\Facades\Mail::to($this->inviteEmail)->send(
            new \App\Mail\PlatformStaffInvitationMail(role: $this->inviteRole, password: $password)
        );

        session()->flash('status', "Staff created successfully! An email containing the password ({$password}) has been sent.");

        $this->reset('inviteEmail');
        $this->dispatch('modal-close', name: 'invite-staff');
    }

    public $editingUserId = null;
    public $editRole = '';

    public function editUser($id)
    {
        $user = User::findOrFail($id);
        $this->editingUserId = $user->id;
        $this->editRole = $user->role;
        $this->dispatch('modal-show', name: 'edit-user');
    }

    public function updateUser()
    {
        $this->validate([
            'editRole' => ['required', 'string', Rule::enum(\App\Enums\UserRole::class)],
        ]);

        $user = User::findOrFail($this->editingUserId);

        if ($this->editRole === \App\Enums\UserRole::SUPERADMIN->value && auth()->user()->role !== \App\Enums\UserRole::SUPERADMIN) {
            session()->flash('error', 'Only superadmins can assign the superadmin role.');
            return;
        }

        if ($user->role === \App\Enums\UserRole::SUPERADMIN && $this->editRole !== \App\Enums\UserRole::SUPERADMIN->value && User::where('role', 'superadmin')->count() <= 1) {
            session()->flash('error', 'Cannot change the role of the last superadmin account.');
            return;
        }

        $user->update(['role' => $this->editRole]);
        $this->dispatch('modal-close', name: 'edit-user');
        session()->flash('status', "User role smoothly updated.");
    }

    public $deletingUserId = null;

    public function confirmDelete($id)
    {
        $this->deletingUserId = $id;
        $this->dispatch('modal-show', name: 'delete-user');
    }

    public function deleteUser()
    {
        if ($this->deletingUserId === auth()->id()) {
            session()->flash('error', 'You cannot delete your own account.');
            $this->dispatch('modal-close', name: 'delete-user');
            return;
        }

        $user = User::findOrFail($this->deletingUserId);

        if ($user->role === \App\Enums\UserRole::SUPERADMIN && User::where('role', 'superadmin')->count() <= 1) {
            session()->flash('error', 'Cannot delete the last superadmin account.');
            $this->dispatch('modal-close', name: 'delete-user');
            return;
        }

        $user->delete();
        $this->dispatch('modal-close', name: 'delete-user');
        session()->flash('status', "User permanently removed.");
    }

    public function render()
    {
        $users = User::query()
            ->when(
                $this->search,
                fn($q) =>
                $q->where(function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%')
                          ->orWhere('email', 'like', '%' . $this->search . '%');
                })
            )
            ->when(
                $this->filterRole,
                fn($q) =>   // FIX: filter was declared in blade but never applied
                $q->where('role', $this->filterRole)
            )
            ->latest()
            ->paginate(15);

        return view('livewire.admin.users.users-index', ['users' => $users])->layout('layouts.app');
    }
}