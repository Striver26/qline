<?php

namespace App\Livewire\Business;

use Livewire\Component;
use App\Models\Tenant\Business;
use App\Livewire\Forms\BusinessSettingsForm;
use Illuminate\Support\Str;

class BusinessSettings extends Component
{
    public BusinessSettingsForm $form;

    public function mount()
    {
        $user = auth()->user();
        $this->form->loadFromBusiness($user->business);
    }

    public function save()
    {
        $user = auth()->user();

        $this->form->validate();

        if (!$user->business) {
            $this->createBusinessAccount($user);
            session()->flash('success', 'Business settings successfully saved. You are ready to open your queue.');
            return redirect()->route('business.dashboard');
        }

        $this->updateBusinessAccount($user->business);
        $this->dispatch('profile-updated', name: $user->name);
    }

    private function createBusinessAccount(\App\Models\User $user): void
    {
        $business = Business::create([
            'name' => $this->form->name,
            'slug' => Str::slug($this->form->name),
            'join_code' => strtoupper($this->form->join_code),
            'queue_prefix' => strtoupper($this->form->queue_prefix ?? 'A'),
            'address' => $this->form->address,
            'phone' => $this->form->phone,
            'city' => $this->form->city,
            'state' => $this->form->state,
            'postcode' => $this->form->postcode,
            'business_hours' => $this->form->business_hours,
            'timezone' => $this->form->timezone,
            'queue_status' => \App\Enums\BusinessQueueStatus::CLOSED->value,
            'is_active' => true,
        ]);

        $user->update([
            'business_id' => $business->id,
            'profile_completed' => true
        ]);
    }

    private function updateBusinessAccount(Business $business): void
    {
        $business->update([
            'name' => $this->form->name,
            'slug' => $business->slug ?: Str::slug($this->form->name),
            'join_code' => strtoupper($this->form->join_code),
            'queue_prefix' => strtoupper($this->form->queue_prefix ?? 'A'),
            'address' => $this->form->address,
            'phone' => $this->form->phone,
            'city' => $this->form->city,
            'state' => $this->form->state,
            'postcode' => $this->form->postcode,
            'business_hours' => $this->form->business_hours,
            'timezone' => $this->form->timezone,
        ]);
    }

    public function render()
    {
        return view('livewire.business.business-settings')
            ->layout('layouts.app');
    }
}
