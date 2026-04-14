<?php
namespace App\Livewire\Admin\QueueEntries;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Queue\QueueEntry;

class QueueEntriesIndex extends Component
{
    use WithPagination;
    
    public function pruneLogs()
    {
        $count = QueueEntry::where('created_at', '<', now()->subDays(30))->delete();
        session()->flash('status', "Successfully pruned {$count} legacy queue entries older than 30 days.");
        $this->dispatch('modal-close', name: 'prune-queues');
    }

    public function render()
    {
        $entries = QueueEntry::with('business')->latest()->paginate(20);
        return view('livewire.admin.queue-entries.queue-entries-index', ['entries' => $entries])->layout('layouts.app');
    }
}