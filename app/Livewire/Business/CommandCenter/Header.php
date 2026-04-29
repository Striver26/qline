<?php

namespace App\Livewire\Business\CommandCenter;

use Livewire\Attributes\Reactive;
use Livewire\Component;

class Header extends Component
{
    #[Reactive]
    public array $businessState = [];

    #[Reactive]
    public array $metrics = [];

    public string $pauseReason = '';

    public bool $showPauseModal = false;

    #[\Livewire\Attributes\On('command-center-close-pause-modal')]
    public function closeModal(): void
    {
        $this->showPauseModal = false;
    }

    public function submitPause(): void
    {
        $this->validate([
            'pauseReason' => ['required', 'string', 'max:255'],
        ]);

        $this->dispatch('command-center.pause-queue', reason: $this->pauseReason);
        $this->pauseReason = '';
        $this->showPauseModal = false;
    }

    public function render()
    {
        return view('livewire.business.command-center.header');
    }
}
