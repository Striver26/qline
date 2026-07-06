<?php
namespace App\Livewire\Admin\Subscriptions;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Tenant\Subscription;
use App\Services\Billing\SubscriptionService;

class SubscriptionsIndex extends Component
{
    use WithPagination;
    
    public $editingSubId = null;
    public $editType = '';
    public $editBillingCycle = 'free';
    public $editStatus = '';
    public $editExpiresAt = '';
    
    public $search = '';
    public $filterStatus = '';

    public function updatedSearch() { $this->resetPage(); }
    public function updatedFilterStatus() { $this->resetPage(); }

    public function editSub($id)
    {
        $sub = Subscription::findOrFail($id);
        $this->editingSubId = $sub->id;
        $this->editType = $sub->type->value;
        $this->editBillingCycle = app(SubscriptionService::class)->billingCycleFor($this->editType, $sub->billing_cycle);
        $this->editStatus = $sub->status;
        $this->editExpiresAt = $sub->expires_at ? $sub->expires_at->format('Y-m-d') : '';
        $this->dispatch('modal-show', name: 'edit-subscription');
    }

    public function updateSub()
    {
        $sub = Subscription::findOrFail($this->editingSubId);
        $cycle = app(SubscriptionService::class)->billingCycleFor($this->editType, $this->editBillingCycle);
        $status = $this->editType === 'free' ? 'active' : $this->editStatus;
        $expiresAt = $cycle === 'free' || ! $this->editExpiresAt
            ? null
            : \Carbon\Carbon::parse($this->editExpiresAt);

        $sub->update([
            'type' => $this->editType,
            'billing_cycle' => $cycle,
            'status' => $status,
            'expires_at' => $expiresAt,
        ]);

        if ($status === 'active') {
            app(SubscriptionService::class)->activateSubscription($sub);

            if ($expiresAt) {
                $sub->update(['expires_at' => $expiresAt]);
            }
        }

        $this->dispatch('modal-close', name: 'edit-subscription');
        session()->flash('status', "Subscription matrix actively re-routed.");
    }

    public function render()
    {
        $subscriptions = Subscription::with('business')
            ->when($this->search, function($q) {
                $q->whereHas('business', fn($b) => $b->where('name', 'like', '%'.$this->search.'%'));
            })
            ->when($this->filterStatus, function($q) {
                $q->where('status', $this->filterStatus);
            })
            ->latest()
            ->paginate(15);
            
        return view('livewire.admin.subscriptions.subscriptions-index', ['subscriptions' => $subscriptions])->layout('layouts.app');
    }
}