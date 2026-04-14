<?php
namespace App\Livewire\Admin\Businesses;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Tenant\Business;

class BusinessesIndex extends Component
{
    use WithPagination;
    public $search = '';

    public function updatedSearch() { $this->resetPage(); }

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
        Business::findOrFail($this->deletingBizId)->delete();
        $this->dispatch('modal-close', name: 'delete-business');
        session()->flash('status', "Tenant profoundly deleted.");
    }

    public function render()
    {
        $businesses = Business::query() // Removed with('users') since not loaded in the view!
            ->when($this->search, fn($q) => $q->where('name', 'like', '%'.$this->search.'%')->orWhere('join_code', 'like', '%'.$this->search.'%'))
            ->latest()
            ->paginate(15);

        return view('livewire.admin.businesses.businesses-index', ['businesses' => $businesses])->layout('layouts.app');
    }
}