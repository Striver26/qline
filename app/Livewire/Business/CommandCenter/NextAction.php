<?php

namespace App\Livewire\Business\CommandCenter;

use Livewire\Attributes\Computed;
use Livewire\Attributes\Reactive;
use Livewire\Component;

class NextAction extends Component
{
    #[Reactive]
    public array $businessState = [];

    #[Reactive]
    public ?array $nextEntry = null;

    #[Reactive]
    public array $servicePoints = [];

    #[Reactive]
    public ?int $selectedServicePointId = null;

    #[Computed]
    public function availableServicePoints(): array
    {
        return array_values(array_filter(
            $this->servicePoints,
            fn (array $sp): bool => $sp['is_active'] && $sp['status'] !== 'occupied',
        ));
    }

    public function render()
    {
        return view('livewire.business.command-center.next-action');
    }
}
