<?php

namespace App\Livewire\Forms;

use Livewire\Form;
use Livewire\Attributes\Validate;
use App\Models\Tenant\Business;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class BusinessSettingsForm extends Form
{
    public $name = '';
    public $join_code = '';
    public $queue_prefix = '';
    public $address = '';
    public $phone = '';
    public $city = '';
    public $state = '';
    public $postcode = '';
    public $timezone = 'Asia/Kuala_Lumpur';
    public $business_hours = [];

    public function loadFromBusiness(?Business $business)
    {
        if ($business) {
            $this->name = $business->name;
            $this->join_code = $business->join_code;
            $this->queue_prefix = $business->queue_prefix;
            $this->address = $business->address;
            $this->phone = $business->phone;
            $this->city = $business->city;
            $this->state = $business->state;
            $this->postcode = $business->postcode;
            $this->timezone = $business->timezone ?? 'Asia/Kuala_Lumpur';
            $this->business_hours = $business->business_hours ?? $this->defaultHours();
        } else {
            $this->join_code = strtoupper(Str::random(6));
            $this->queue_prefix = 'A';
            $this->business_hours = $this->defaultHours();
        }
    }

    private function defaultHours()
    {
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        $hours = [];
        foreach ($days as $day) {
            $hours[$day] = ['09:00', '18:00'];
        }
        return $hours;
    }

    public function rules(): array
    {
        $businessId = auth()->user()->business_id;

        return [
            'name' => 'required|string|max:255',
            'join_code' => ['required', 'string', 'max:10', Rule::unique('businesses', 'join_code')->ignore($businessId)],
            'queue_prefix' => 'nullable|string|max:3',
            'address' => 'nullable|string',
            'phone' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'postcode' => 'nullable|string|max:10',
            'timezone' => 'required|string|timezone',
            'business_hours' => 'nullable|array',
        ];
    }
}
