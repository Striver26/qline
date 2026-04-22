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