<?php
namespace App\Livewire\Admin\WaMessages;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Marketing\WhatsappMessage;

class WaMessagesIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterStatus = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function pruneLogs()
    {
        $count = WhatsappMessage::where('created_at', '<', now()->subDays(30))->delete();
        session()->flash('status', "Successfully pruned {$count} legacy WhatsApp transmissions older than 30 days.");
        $this->dispatch('modal-close', name: 'prune-wa');
    }

    public function render()
    {
        $messages = WhatsappMessage::with('business')
            ->when($this->search, function ($q) {
                $q->where(function ($inner) {
                    $inner->where('wa_id', 'like', '%' . $this->search . '%')
                          ->orWhere('body', 'like', '%' . $this->search . '%')
                          ->orWhere('message_id', 'like', '%' . $this->search . '%')
                          ->orWhereHas('business', function ($bq) {
                              $bq->where('name', 'like', '%' . $this->search . '%');
                          });
                });
            })
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->latest()
            ->paginate(20);

        return view('livewire.admin.wa-messages.wa-messages-index', ['messages' => $messages])->layout('layouts.app');
    }
}