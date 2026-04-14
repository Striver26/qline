<?php
namespace App\Livewire\Admin\Payments;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Tenant\Payment;

class PaymentsIndex extends Component
{
    use WithPagination;
    
    public $editingPayId = null;
    public $editStatus = '';
    
    public $search = '';
    public $filterStatus = '';

    public function updatedSearch() { $this->resetPage(); }
    public function updatedFilterStatus() { $this->resetPage(); }

    public function editPayment($id)
    {
        $payment = Payment::findOrFail($id);
        $this->editingPayId = $payment->id;
        $this->editStatus = $payment->status;
        $this->dispatch('modal-show', name: 'edit-payment');
    }

    public function updatePayment()
    {
        $payment = Payment::findOrFail($this->editingPayId);
        $payment->update(['status' => $this->editStatus]);
        $this->dispatch('modal-close', name: 'edit-payment');
        session()->flash('status', "Payment ledger successfully reconciled.");
    }

    public function render()
    {
        $payments = Payment::with('business')
            ->when($this->search, function($q) {
                $q->whereHas('business', fn($b) => $b->where('name', 'like', '%'.$this->search.'%'))
                  ->orWhere('reference', 'like', '%'.$this->search.'%');
            })
            ->when($this->filterStatus, function($q) {
                $q->where('status', $this->filterStatus);
            })
            ->latest()
            ->paginate(15);
            
        return view('livewire.admin.payments.payments-index', ['payments' => $payments])->layout('layouts.app');
    }
}