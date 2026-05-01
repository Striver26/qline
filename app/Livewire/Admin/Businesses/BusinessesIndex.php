<?php

namespace App\Livewire\Admin\Businesses;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Tenant\Business;
use App\Models\AdminAuditLog;

class BusinessesIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $filterStatus = '';

    public function updatedSearch()
    {
        $this->resetPage();
    }
    public function updatedFilterStatus()
    {
        $this->resetPage();
    } // FIX: was missing

    public $managingSubBizId = null;
    public $editSubType = '';
    public $editSubStatus = '';
    public $editSubExpiresAt = '';

    public function manageSubscription($id)
    {
        $biz = Business::with('subscription')->findOrFail($id);
        $this->managingSubBizId = $biz->id;
        
        $sub = $biz->subscription;
        if ($sub) {
            $this->editSubType = $sub->type instanceof \App\Enums\SubTier ? $sub->type->value : $sub->type;
            $this->editSubStatus = $sub->status;
            $this->editSubExpiresAt = $sub->expires_at ? $sub->expires_at->format('Y-m-d\TH:i') : '';
        } else {
            $this->editSubType = \App\Enums\SubTier::DAILY->value;
            $this->editSubStatus = 'pending';
            $this->editSubExpiresAt = now()->addDays(1)->format('Y-m-d\TH:i');
        }
        
        $this->dispatch('modal-show', name: 'manage-subscription');
    }

    public function updateSubscription()
    {
        $this->validate([
            'editSubType' => ['required', 'string'],
            'editSubStatus' => ['required', 'string'],
            'editSubExpiresAt' => ['required', 'date'],
        ]);

        $biz = Business::findOrFail($this->managingSubBizId);
        
        $sub = $biz->subscription()->firstOrCreate(
            ['business_id' => $biz->id],
            ['starts_at' => now(), 'type' => $this->editSubType, 'status' => 'pending', 'expires_at' => now()]
        );
        
        $sub->update([
            'type' => $this->editSubType,
            'status' => $this->editSubStatus,
            'expires_at' => $this->editSubExpiresAt,
        ]);
        
        if ($this->editSubStatus === 'active') {
            app(\App\Services\Billing\SubscriptionService::class)->activateSubscription($sub);
            // Re-apply the manually chosen expiration date
            $sub->update(['expires_at' => $this->editSubExpiresAt]);
        }

        AdminAuditLog::record('business.subscription_update', $biz, [
            'new_type' => $this->editSubType,
            'new_status' => $this->editSubStatus,
            'new_expires_at' => $this->editSubExpiresAt,
        ]);

        $this->dispatch('modal-close', name: 'manage-subscription');
        session()->flash('status', "Business subscription successfully updated.");
    }

    public $editingBizId = null;
    public $editStatus = '';

    public function editStatus($id)
    {
        $biz = Business::findOrFail($id);
        $this->editingBizId = $biz->id;
        $this->editStatus = $biz->queue_status;
        $this->dispatch('modal-show', name: 'edit-business');
    }

    public function updateStatus()
    {
        $biz = Business::findOrFail($this->editingBizId);
        $biz->update(['queue_status' => $this->editStatus]);
        $this->dispatch('modal-close', name: 'edit-business');
        session()->flash('status', "Tenant status reliably enforced.");
    }

    public $deletingBizId = null;

    public function confirmDelete($id)
    {
        $this->deletingBizId = $id;
        $this->dispatch('modal-show', name: 'delete-business');
    }

    public function deleteBusiness()
    {
        $biz = Business::findOrFail($this->deletingBizId);

        AdminAuditLog::record('business.delete', $biz, [
            'name'     => $biz->name,
            'slug'     => $biz->slug,
            'join_code'=> $biz->join_code,
        ]);

        // Cleanup associated users (Staff) to prevent orphans
        \App\Models\User::where('business_id', $biz->id)->update([
            'is_active' => false,
            'profile_completed' => false,
            'business_id' => null
        ]);

        $biz->delete();
        $this->dispatch('modal-close', name: 'delete-business');
        session()->flash('status', "Tenant profoundly deleted.");
    }

    public function render()
    {
        $businesses = Business::query()
            ->when(
                $this->search,
                fn($q) =>
                $q->where(function ($inner) {
                    $inner->where('name', 'like', '%' . $this->search . '%')
                          ->orWhere('join_code', 'like', '%' . $this->search . '%');
                })
            )
            ->when(
                $this->filterStatus,
                fn($q) =>  // FIX: filter was declared in blade but never applied
                $q->where('queue_status', $this->filterStatus)
            )
            ->latest()
            ->paginate(15);

        return view('livewire.admin.businesses.businesses-index', ['businesses' => $businesses])->layout('layouts.app');
    }
}