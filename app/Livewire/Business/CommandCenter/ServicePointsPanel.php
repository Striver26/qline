<?php

namespace App\Livewire\Business\CommandCenter;

use Livewire\Attributes\Reactive;
use Livewire\Component;

class ServicePointsPanel extends Component
{
    #[Reactive]
    public array $businessState = [];

    #[Reactive]
    public array $servicePoints = [];

    #[Reactive]
    public ?int $selectedServicePointId = null;

    public function render()
    {
        return view('livewire.business.command-center.service-points-panel');
    }
}
