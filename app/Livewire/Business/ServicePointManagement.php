<?php

namespace App\Livewire\Business;

use App\Enums\QueueStatus;
use App\Models\Tenant\Business;
use Livewire\Component;

class ServicePointManagement extends Component
{
    public Business $business;

    public $servicePoints;

    public $tables;

    public $newName = '';

    public $newTableName = '';

    public $editingServicePointId = null;

    public $editingTableId = null;

    public $editName = '';

    public $editTableName = '';

    public int $servicePointLimit = 0;

    public function mount()
    {
        $this->business = auth()->user()->getActiveBusiness()->loadMissing('subscription');
        $this->servicePointLimit = $this->resolveServicePointLimit();
        $this->loadServicePoints();
    }

    public function loadServicePoints(): void
    {
        $this->servicePoints = $this->business->servicePoints()
            ->withCount('queueEntries')
            ->orderBy('name')
            ->get();
    }

    public function addServicePoint()
    {
        $this->validate(['newName' => 'required|string|max:255']);

        if ($this->servicePointLimit > 0 && $this->servicePoints->count() >= $this->servicePointLimit) {
            session()->flash('error', "Your current plan allows {$this->servicePointLimit} service point(s). Upgrade to add more.");

            return;
        }

        $this->business->servicePoints()->create([
            'name' => $this->newName,
            'type' => 'service_point',
            'is_active' => true,
        ]);

        $this->newName = '';
        $this->loadServicePoints();
        session()->flash('success', 'Service Point added successfully.');
    }

    public function toggleServicePoint($id)
    {
        $servicePoint = $this->business->servicePoints()->findOrFail($id);

        if (! $servicePoint->is_active && $this->servicePointLimit > 0) {
            $activeCount = $this->business->servicePoints()->where('is_active', true)->count();

            if ($activeCount >= $this->servicePointLimit) {
                session()->flash('error', "Your current plan allows {$this->servicePointLimit} active service point(s).");

                return;
            }
        }

        $servicePoint->update(['is_active' => ! $servicePoint->is_active]);
        $this->loadServicePoints();
    }

    public function editServicePoint($id)
    {
        $servicePoint = $this->business->servicePoints()->findOrFail($id);
        $this->editingServicePointId = $servicePoint->id;
        $this->editName = $servicePoint->name;
    }

    public function updateServicePoint()
    {
        $this->validate(['editName' => 'required|string|max:255']);

        $servicePoint = $this->business->servicePoints()->findOrFail($this->editingServicePointId);
        $servicePoint->update(['name' => $this->editName]);

        $this->editingServicePointId = null;
        $this->editName = '';
        $this->loadServicePoints();
    }

    public function deleteServicePoint($id)
    {
        $servicePoint = $this->business->servicePoints()->findOrFail($id);

        if ($servicePoint->queueEntries()->whereIn('status', [QueueStatus::CALLED->value, QueueStatus::SERVING->value])->exists()) {
            session()->flash('error', 'Cannot delete a service point that is currently serving customers.');

            return;
        }

        $servicePoint->delete();
        $this->loadServicePoints();
        session()->flash('success', 'Service Point removed successfully.');
    }

    public function render()
    {
        return view('livewire.business.service-point-management', [
            'servicePointLimit' => $this->servicePointLimit,
        ])
            ->layout('layouts.app');
    }

    private function resolveServicePointLimit(): int
    {
        $tier = $this->business->subscription?->type?->value ?? 'free';

        return (int) config("qline.tiers.{$tier}.service_point_limit", 0);
    }
}
