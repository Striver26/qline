<?php

namespace App\Livewire\Business;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Queue\QueueEntry;

class QueueEntries extends Component
{
    use WithPagination;

    public $search = '';
    public $status = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }
    
    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function render()
    {
        $businessId = auth()->user()->business_id;
        
        $query = QueueEntry::where('business_id', $businessId);

        if ($this->search) {
            $query->where(function($q) {
                $q->where('ticket_code', 'like', '%' . $this->search . '%')
                  ->orWhere('wa_id', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }

        $entries = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('livewire.business.queue-entries', compact('entries'))
            ->layout('layouts.app');
    }
}

