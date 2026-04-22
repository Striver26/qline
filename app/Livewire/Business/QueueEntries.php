<?php

namespace App\Livewire\Business;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Queue\QueueEntry;
use Illuminate\Database\Eloquent\Builder;

class QueueEntries extends Component
{
    use WithPagination;

    public string $search = '';
    public string $status = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }
    
    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $businessId = auth()->user()->getActiveBusiness()?->id;
        
        $query = QueueEntry::where('business_id', $businessId);

        $this->applySearchFilters($query);
        $this->applyStatusFilters($query);

        $entries = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('livewire.business.queue-entries', compact('entries'))
            ->layout('layouts.app');
    }

    private function applySearchFilters(Builder $query): void
    {
        if (empty($this->search)) {
            return;
        }

        $query->where(function (Builder $q) {
            $q->where('ticket_code', 'like', '%' . $this->search . '%')
              ->orWhere('wa_id', 'like', '%' . $this->search . '%');

            if (strtolower($this->search) === 'anonymous') {
                $q->orWhereNull('wa_id')
                  ->orWhere('source', 'anonymous');
            }
        });
    }

    private function applyStatusFilters(Builder $query): void
    {
        if (!empty($this->status)) {
            $query->where('status', $this->status);
        }
    }
}
