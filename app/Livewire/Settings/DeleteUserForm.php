<?php

namespace App\Livewire\Settings;

use App\Concerns\PasswordValidationRules;
use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class DeleteUserForm extends Component
{
    use PasswordValidationRules;

    public string $password = '';

    /**
     * Delete the currently authenticated user.
     */
    public function deleteUser(Logout $logout): void
    {
        $this->validate([
            'password' => $this->currentPasswordRules(),
        ]);

        $user = Auth::user();

        if ($user->role === \App\Enums\UserRole::SUPERADMIN && \App\Models\User::where('role', 'superadmin')->count() <= 1) {
            $this->addError('password', 'You cannot delete the last superadmin account.');
            return;
        }

        if ($user->role === \App\Enums\UserRole::BUSINESS_OWNER) {
            $this->addError('password', 'Business owners cannot delete their account without transferring ownership first.');
            return;
        }

        tap($user, $logout(...))->delete();

        $this->redirect('/', navigate: true);
    }
}
