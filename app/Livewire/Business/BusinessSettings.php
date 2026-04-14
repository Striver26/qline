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

    public function mount()
    {
        $user = auth()->user();
        
        if ($user->business) {
            $this->name = $user->business->name;
            $this->join_code = $user->business->join_code;
            $this->queue_prefix = $user->business->queue_prefix;
            $this->address = $user->business->address;
            $this->phone = $user->business->phone;
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
        ]);

        if (!$user->business) {
            $business = Business::create([
                'name' => $this->name,
                'slug' => Str::slug($this->name),
                'join_code' => strtoupper($this->join_code),
                'queue_prefix' => strtoupper($this->queue_prefix ?? 'A'),
                'address' => $this->address,
                'phone' => $this->phone,
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
                'name' => $this->name,
                'slug' => Str::slug($this->name),
                'join_code' => strtoupper($this->join_code),
                'queue_prefix' => strtoupper($this->queue_prefix ?? 'A'),
                'address' => $this->address,
                'phone' => $this->phone,
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

