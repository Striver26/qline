<?php

namespace App\Livewire\Business;

use App\Enums\BusinessQueueStatus;
use App\Enums\SubTier;
use App\Livewire\Forms\BusinessSettingsForm;
use App\Models\Tenant\Business;
use App\Models\User;
use App\Services\Billing\SubscriptionService;
use Illuminate\Support\Str;
use Livewire\Component;

class BusinessSettings extends Component
{
    public BusinessSettingsForm $form;

    public function mount()
    {
        $user = auth()->user();
        $this->form->loadFromBusiness($user->getActiveBusiness());
    }

    public function save()
    {
        $user = auth()->user();

        $this->form->validate();
        $business = $user->getActiveBusiness();

        if (! $business) {
            $this->createBusinessAccount($user);
            session()->flash('success', 'Business settings successfully saved. You are ready to open your queue.');

            return redirect()->route('business.dashboard');
        }

        $this->updateBusinessAccount($business);
        $this->dispatch('profile-updated', name: $user->name);
    }

    private function createBusinessAccount(User $user): void
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
            'queue_status' => BusinessQueueStatus::CLOSED->value,
            'daily_limit' => config('qline.tiers.free.daily_limit', 50),
            'is_active' => true,
        ]);

        $subscription = $business->subscription()->create([
            'type' => SubTier::FREE->value,
            'billing_cycle' => 'free',
            'status' => 'active',
        ]);

        app(SubscriptionService::class)->activateSubscription($subscription);

        $user->update([
            'business_id' => $business->id,
            'profile_completed' => true,
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

    public function copyToAll($sourceDay)
    {
        $this->form->copyToAll($sourceDay);
        $this->dispatch('profile-updated', name: 'Hours updated');
    }

    public function render()
    {
        return view('livewire.business.business-settings')
            ->layout('layouts.app');
    }
}
