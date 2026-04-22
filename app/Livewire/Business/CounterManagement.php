<?php

namespace App\Livewire\Business;

use Livewire\Component;
use App\Models\Tenant\Counter;
use Exception;

class CounterManagement extends Component
{
    public \App\Models\Tenant\Business $business;
    public $counters;
    public $newName = '';
    public $editingCounterId = null;
    public $editName = '';

    public function mount()
    {
        $this->business = auth()->user()->getActiveBusiness();
        $this->loadCounters();
    }

    public function loadCounters()
    {
        $this->counters = $this->business->counters()->get();
    }

    public function addCounter()
    {
        $this->validate(['newName' => 'required|string|max:255']);

        // Enforce counter limit if any (optional, but good for SaaS)
        if ($this->counters->count() >= 10) {
            session()->flash('error', 'Maximum limit of 10 counters reached.');
            return;
        }

        $this->business->counters()->create([
            'name' => $this->newName,
            'is_active' => true,
        ]);

        $this->newName = '';
        $this->loadCounters();
        session()->flash('success', 'Counter added successfully.');
    }

    public function toggleCounter($id)
    {
        $counter = $this->business->counters()->findOrFail($id);
        $counter->update(['is_active' => !$counter->is_active]);
        $this->loadCounters();
    }

    public function editCounter($id)
    {
        $counter = $this->business->counters()->findOrFail($id);
        $this->editingCounterId = $counter->id;
        $this->editName = $counter->name;
    }

    public function updateCounter()
    {
        $this->validate(['editName' => 'required|string|max:255']);
        
        $counter = $this->business->counters()->findOrFail($this->editingCounterId);
        $counter->update(['name' => $this->editName]);
        
        $this->editingCounterId = null;
        $this->loadCounters();
    }

    public function deleteCounter($id)
    {
        $counter = $this->business->counters()->findOrFail($id);
        
        // Check if there are any active entries assigned to this counter
        if ($counter->queueEntries()->whereIn('status', ['called', 'serving'])->exists()) {
            session()->flash('error', 'Cannot delete a counter that is currently serving customers.');
            return;
        }

        $counter->delete();
        $this->loadCounters();
    }

    public function render()
    {
        return view('livewire.business.counter-management')
            ->layout('layouts.app');
    }
}
