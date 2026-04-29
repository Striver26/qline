<?php

namespace App\Livewire\Business\CommandCenter;

use Livewire\Attributes\Reactive;
use Livewire\Component;

class ActiveTickets extends Component
{
    #[Reactive]
    public array $activeEntries = [];

    public function render()
    {
        return view('livewire.business.command-center.active-tickets');
    }
}
