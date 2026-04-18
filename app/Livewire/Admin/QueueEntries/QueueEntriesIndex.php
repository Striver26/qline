<?php
namespace App\Livewire\Admin\QueueEntries;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Queue\QueueEntry;

class QueueEntriesIndex extends Component
{
    use WithPagination;
    
    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function pruneLogs()
    {
        $count = QueueEntry::where('created_at', '<', now()->subDays(30))->delete();
        session()->flash('status', "Successfully pruned {$count} legacy queue entries older than 30 days.");
        $this->dispatch('modal-close', name: 'prune-queues');
    }

    public function render()
    {
        $query = QueueEntry::with('business');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('ticket_code', 'like', '%' . $this->search . '%')
                    ->orWhere('wa_id', 'like', '%' . $this->search . '%')
                    ->orWhereHas('business', function ($sub) {
                        $sub->where('name', 'like', '%' . $this->search . '%');
                    });

                if (strtolower($this->search) === 'anonymous') {
                    $q->orWhereNull('wa_id')
                        ->orWhere('source', 'anonymous');
                }
            });
        }

        $entries = $query->latest()->paginate(20);
        return view('livewire.admin.queue-entries.queue-entries-index', ['entries' => $entries])->layout('layouts.app');
    }
}