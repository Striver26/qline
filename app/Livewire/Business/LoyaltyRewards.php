<?php

namespace App\Livewire\Business;

use Livewire\Component;
use App\Models\Marketing\LoyaltyReward;
use App\Models\Marketing\LoyaltyVisit;

class LoyaltyRewards extends Component
{
    // Form fields
    public $reward_value = '';
    public $required_visits = 3;
    public $reward_type = 'freebie';

    // Edit state
    public $editingId = null;
    public $showForm = false;

    protected function rules()
    {
        return [
            'reward_value' => 'required|string|max:255',
            'required_visits' => 'required|integer|min:1|max:999',
            'reward_type' => 'required|in:freebie,discount_percent,discount_fixed',
        ];
    }

    public function isOwner(): bool
    {
        return auth()->user()->isOwner();
    }

    public function openForm()
    {
        if (! $this->isOwner()) return;
        $this->reset(['reward_value', 'required_visits', 'reward_type', 'editingId']);
        $this->required_visits = 3;
        $this->reward_type = 'freebie';
        $this->showForm = true;
    }

    public function edit($id)
    {
        if (! $this->isOwner()) return;

        $reward = LoyaltyReward::where('business_id', auth()->user()->business_id)
            ->findOrFail($id);

        $this->editingId = $reward->id;
        $this->reward_value = $reward->reward_value;
        $this->required_visits = $reward->required_visits;
        $this->reward_type = $reward->reward_type;
        $this->showForm = true;
    }

    public function save()
    {
        if (! $this->isOwner()) return;

        $this->validate();

        $data = [
            'business_id' => auth()->user()->business_id,
            'reward_value' => $this->reward_value,
            'required_visits' => $this->required_visits,
            'reward_type' => $this->reward_type,
            'is_active' => true,
        ];

        if ($this->editingId) {
            LoyaltyReward::where('business_id', auth()->user()->business_id)
                ->where('id', $this->editingId)
                ->update($data);
        } else {
            LoyaltyReward::create($data);
        }

        $this->reset(['reward_value', 'required_visits', 'reward_type', 'editingId', 'showForm']);
        $this->dispatch('reward-saved');
    }


    public function removeReward($id)
    {
        if (! $this->isOwner()) return;

        LoyaltyReward::where('business_id', auth()->user()->business_id)
            ->where('id', $id)
            ->delete();
    }

    public function cancelForm()
    {
        $this->reset(['reward_value', 'required_visits', 'reward_type', 'editingId', 'showForm']);
    }

    public function render()
    {
        $businessId = auth()->user()->business_id;

        $rewards = LoyaltyReward::where('business_id', $businessId)
            ->orderBy('required_visits')
            ->get();

        // Top loyal customers (grouped by wa_id, ordered by total visits)
        $topCustomers = LoyaltyVisit::where('business_id', $businessId)
            ->selectRaw('wa_id, COUNT(*) as total_visits, MAX(created_at) as last_visit')
            ->groupBy('wa_id')
            ->orderByDesc('total_visits')
            ->limit(10)
            ->get();

        $isOwner = $this->isOwner();

        return view('livewire.business.loyalty-rewards', compact('rewards', 'topCustomers', 'isOwner'))
            ->layout('layouts.app');
    }
}
