<?php
namespace App\Livewire\Admin\WaMessages;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Marketing\WhatsappMessage;

class WaMessagesIndex extends Component
{
    use WithPagination;
    
    public function pruneLogs()
    {
        $count = WhatsappMessage::where('created_at', '<', now()->subDays(30))->delete();
        session()->flash('status', "Successfully pruned {$count} legacy WhatsApp transmissions older than 30 days.");
        $this->dispatch('modal-close', name: 'prune-wa');
    }

    public function render()
    {
        $messages = WhatsappMessage::with('business')->latest()->paginate(20);
        return view('livewire.admin.wa-messages.wa-messages-index', ['messages' => $messages])->layout('layouts.app');
    }
}