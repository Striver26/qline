<?php

namespace App\Livewire\Business\CommandCenter;

use Livewire\Attributes\Reactive;
use Livewire\Component;

class WaitingList extends Component
{
    #[Reactive]
    public array $businessState = [];

    #[Reactive]
    public array $waitingEntries = [];

    #[Reactive]
    public int $hiddenCount = 0;

    public function render()
    {
        return view('livewire.business.command-center.waiting-list');
    }
}
