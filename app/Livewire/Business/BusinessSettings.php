<?php

namespace App\Livewire\Business;

use Livewire\Component;
use App\Models\Tenant\Business;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class BusinessSettings extends Component
{
    public $name;
    public $join_code;
    public $queue_prefix;
    public $address;
    public $phone;
    public $city;
    public $state;
    public $postcode;
    public $business_hours;

    public function mount()
    {
        $user = auth()->user();
        
        if ($user->business) {
            $this->name = $user->business->name;
            $this->join_code = $user->business->join_code;
            $this->queue_prefix = $user->business->queue_prefix;
            $this->address = $user->business->address;
            $this->phone = $user->business->phone;
            $this->city = $user->business->city;
            $this->state = $user->business->state;
            $this->postcode = $user->business->postcode;
            $this->business_hours = $user->business->business_hours;
        } else {
            $this->join_code = strtoupper(Str::random(6));
            $this->queue_prefix = 'A';
        }
    }

    public function save()
    {
        $user = auth()->user();

        $this->validate([
            'name' => 'required|string|max:255',
            'join_code' => ['required', 'string', 'max:10', Rule::unique('businesses', 'join_code')->ignore($user->business_id)],
            'queue_prefix' => 'nullable|string|max:3',
            'address' => 'nullable|string',
            'phone' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'postcode' => 'nullable|string|max:10',
            'business_hours' => 'nullable|string',
        ]);

        if (!$user->business) {
            $business = Business::create([
                'name' => $this->name,
                'slug' => Str::slug($this->name),
                'join_code' => strtoupper($this->join_code),
                'queue_prefix' => strtoupper($this->queue_prefix ?? 'A'),
                'address' => $this->address,
                'phone' => $this->phone,
                'city' => $this->city,
                'state' => $this->state,
                'postcode' => $this->postcode,
                'business_hours' => $this->business_hours,
                'queue_status' => 'closed',
            ]);

            $user->update([
                'business_id' => $business->id,
                'profile_completed' => true
            ]);
            
            session()->flash('success', 'Business settings successfully saved. You are ready to open your queue.');
            return redirect()->route('business.dashboard');
            
        } else {
            $user->business->update([
                'slug' => Str::slug($this->name),
                'join_code' => strtoupper($this->join_code),
                'queue_prefix' => strtoupper($this->queue_prefix ?? 'A'),
                'address' => $this->address,
                'phone' => $this->phone,
                'city' => $this->city,
                'state' => $this->state,
                'postcode' => $this->postcode,
                'business_hours' => $this->business_hours,
            ]);
            
            $this->dispatch('profile-updated', name: $user->name);
        }
    }

    public function render()
    {
        return view('livewire.business.business-settings')
            ->layout('layouts.app');
    }
}

