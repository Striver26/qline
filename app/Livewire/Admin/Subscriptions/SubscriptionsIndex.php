<?php
namespace App\Livewire\Admin\Subscriptions;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Tenant\Subscription;

class SubscriptionsIndex extends Component
{
    use WithPagination;
    
    public $editingSubId = null;
    public $editType = '';
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
        $this->editStatus = $sub->status;
        $this->editExpiresAt = $sub->expires_at ? $sub->expires_at->format('Y-m-d') : '';
        $this->dispatch('modal-show', name: 'edit-subscription');
    }

    public function updateSub()
    {
        $sub = Subscription::findOrFail($this->editingSubId);
        $sub->update([
            'type' => $this->editType,
            'status' => $this->editStatus,
            'expires_at' => $this->editExpiresAt ? \Carbon\Carbon::parse($this->editExpiresAt) : null,
        ]);
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