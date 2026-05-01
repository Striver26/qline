<?php

namespace App\Livewire\Business\CommandCenter;

use App\Enums\BusinessQueueStatus;
use App\Models\Tenant\Business;
use App\Services\Queue\QueueService;
use Livewire\Attributes\On;
use Livewire\Component;
use Throwable;

class Index extends Component
{
    public int $businessId;

    public array $businessState = [];

    public array $metrics = [];

    public ?array $nextEntry = null;

    public array $waitingEntries = [];

    public array $activeEntries = [];

    public array $servicePoints = [];

    public ?int $selectedServicePointId = null;

    public function mount(QueueService $queueService): void
    {
        $business = auth()->user()?->getActiveBusiness();

        if (! $business) {
            session()->flash('warning', 'Please complete your business profile.');
            $this->redirectRoute('business.settings', navigate: true);

            return;
        }

        $this->businessId = $business->id;
        $this->hydrateSnapshot($queueService);
    }

    #[On('echo:business.{businessId},QueueUpdated')]
    public function syncRealtime(): void
    {
        $this->hydrateSnapshot(app(QueueService::class));
    }

    #[On('command-center.select-servicePoint')]
    public function selectServicePoint(int $servicePointId): void
    {
        $servicePoint = collect($this->servicePoints)
            ->first(fn (array $item): bool => $item['id'] === $servicePointId && ! $item['is_busy']);

        if ($servicePoint) {
            $this->selectedServicePointId = $servicePointId;
        }
    }

    #[On('command-center.call-next')]
    public function callNext(?int $servicePointId = null): void
    {
        $queueService = app(QueueService::class);

        try {
            $entry = $queueService->callNext($this->resolveBusiness(), $servicePointId ?? $this->selectedServicePointId);

            if (! $entry) {
                $this->dispatch('notify', type: 'info', message: 'No waiting tickets are ready to be called.');
                $this->hydrateSnapshot($queueService);

                return;
            }

            $destination = $entry->servicePoint?->name;
            $message = $destination
                ? "Called {$entry->ticket_code} to {$destination}."
                : "Called {$entry->ticket_code}.";

            $this->dispatch('notify', type: 'success', message: $message);
        } catch (Throwable $exception) {
            $this->dispatch('notify', type: 'error', message: $exception->getMessage());
        }

        $this->hydrateSnapshot($queueService);
    }

    #[On('command-center.call-entry')]
    public function callEntry(int $entryId, ?int $servicePointId = null): void
    {
        $queueService = app(QueueService::class);

        try {
            $entry = $queueService->callEntry($this->resolveBusiness(), $entryId, $servicePointId ?? $this->selectedServicePointId);
            $destination = $entry->servicePoint?->name ?? 'the selected service point';

            $this->dispatch('notify', type: 'success', message: "Moved {$entry->ticket_code} to {$destination}.");
        } catch (Throwable $exception) {
            $this->dispatch('notify', type: 'error', message: $exception->getMessage());
        }

        $this->hydrateSnapshot($queueService);
    }

    #[On('command-center.assign-to-servicePoint')]
    public function assignToServicePoint(int $entryId, int $servicePointId): void
    {
        $queueService = app(QueueService::class);

        try {
            $entry = $queueService->assignToServicePoint($entryId, $servicePointId);
            $destination = $entry->servicePoint?->name ?? 'the selected service point';

            $this->dispatch('notify', type: 'success', message: "Assigned {$entry->ticket_code} to {$destination}.");
        } catch (Throwable $exception) {
            $this->dispatch('notify', type: 'error', message: $exception->getMessage());
        }

        $this->hydrateSnapshot($queueService);
    }

    public function handleDrop(int $entryId, string $targetType, int $targetId): void
    {
        // Determine if this is a waiting entry or an active entry
        $isActive = collect($this->activeEntries)->contains(fn (array $e) => $e['id'] === $entryId);

        if ($isActive) {
            $this->reassignEntry($entryId, $targetId);
        } else {
            $this->callEntry($entryId, $targetId);
        }
    }

    #[On('command-center.reassign')]
    public function reassignEntry(int $entryId, int $servicePointId): void
    {
        $queueService = app(QueueService::class);

        try {
            $entry = $queueService->reassignEntry($entryId, $servicePointId);
            $destination = $entry->servicePoint?->name ?? 'the selected service point';

            $this->dispatch('notify', type: 'success', message: "Reassigned {$entry->ticket_code} to {$destination}.");
        } catch (Throwable $exception) {
            $this->dispatch('notify', type: 'error', message: $exception->getMessage());
        }

        $this->hydrateSnapshot($queueService);
    }

    #[On('command-center.mark-done')]
    public function markDone(int $entryId): void
    {
        $queueService = app(QueueService::class);

        try {
            $entry = $queueService->markDone($entryId);
            $this->dispatch('notify', type: 'success', message: "{$entry->ticket_code} marked done.");
        } catch (Throwable $exception) {
            $this->dispatch('notify', type: 'error', message: $exception->getMessage());
        }

        $this->hydrateSnapshot($queueService);
    }

    #[On('command-center.recall')]
    public function recall(int $entryId): void
    {
        $queueService = app(QueueService::class);

        try {
            $entry = $queueService->recallEntry($entryId);
            $this->dispatch('notify', type: 'success', message: "Recalled {$entry->ticket_code}.");
        } catch (Throwable $exception) {
            $this->dispatch('notify', type: 'error', message: $exception->getMessage());
        }

        $this->hydrateSnapshot($queueService);
    }

    #[On('command-center.skip')]
    public function skip(int $entryId): void
    {
        $queueService = app(QueueService::class);

        try {
            $entry = $queueService->skip($entryId);
            $this->dispatch('notify', type: 'success', message: "{$entry->ticket_code} skipped.");
        } catch (Throwable $exception) {
            $this->dispatch('notify', type: 'error', message: $exception->getMessage());
        }

        $this->hydrateSnapshot($queueService);
    }

    #[On('command-center.toggle-queue')]
    public function toggleQueue(): void
    {
        $queueService = app(QueueService::class);
        $business = $this->resolveBusiness();

        try {
            if ($business->queue_status === BusinessQueueStatus::CLOSED->value) {
                $queueService->openQueue($business);
                $this->dispatch('notify', type: 'success', message: 'Queue opened.');
            } else {
                $queueService->closeQueue($business);
                $this->dispatch('notify', type: 'success', message: 'Queue closed and active tickets cleared.');
            }
        } catch (Throwable $exception) {
            $this->dispatch('notify', type: 'error', message: $exception->getMessage());
        }

        $this->hydrateSnapshot($queueService);
    }

    #[On('command-center.pause-queue')]
    public function pauseQueue(string $reason): void
    {
        $queueService = app(QueueService::class);

        try {
            $queueService->pauseQueue($this->resolveBusiness(), $reason);
            $this->dispatch('notify', type: 'success', message: 'Queue paused.');
            $this->dispatch('command-center-close-pause-modal');
        } catch (Throwable $exception) {
            $this->dispatch('notify', type: 'error', message: $exception->getMessage());
        }

        $this->hydrateSnapshot($queueService);
    }

    #[On('command-center.resume-queue')]
    public function resumeQueue(): void
    {
        $queueService = app(QueueService::class);

        try {
            $queueService->openQueue($this->resolveBusiness());
            $this->dispatch('notify', type: 'success', message: 'Queue resumed.');
        } catch (Throwable $exception) {
            $this->dispatch('notify', type: 'error', message: $exception->getMessage());
        }

        $this->hydrateSnapshot($queueService);
    }

    #[On('command-center.quick-add')]
    public function quickAdd(): void
    {
        $queueService = app(QueueService::class);

        try {
            $entry = $queueService->addManual($this->resolveBusiness());
            $this->dispatch('notify', type: 'success', message: "Added {$entry->ticket_code} to the queue.");
            
            // Trigger thermal print
            $this->dispatch('print-ticket', entryId: $entry->id);
        } catch (Throwable $exception) {
            $this->dispatch('notify', type: 'error', message: $exception->getMessage());
        }

        $this->hydrateSnapshot($queueService);
    }

    #[On('command-center.print-entry')]
    public function printEntry(int $entryId): void
    {
        $this->dispatch('print-ticket', entryId: $entryId);
    }

    public function render()
    {
        return view('livewire.business.command-center.index')
            ->layout('layouts.app');
    }

    private function hydrateSnapshot(QueueService $queueService): void
    {
        $snapshot = $queueService->getCommandCenterSnapshot($this->resolveBusiness());

        $this->businessState = $snapshot['business'];
        $this->metrics = $snapshot['metrics'];
        $this->nextEntry = $snapshot['next_entry'];
        $this->waitingEntries = $snapshot['waiting_entries'];
        $this->activeEntries = $snapshot['active_entries'];
        $this->servicePoints = $snapshot['servicePoints'];
        $this->selectedServicePointId = $this->resolveSelectedServicePointId($snapshot['servicePoints']);

        $this->dispatch('command-center-hydrate-dnd');
    }

    private function resolveSelectedServicePointId(array $servicePoints): ?int
    {
        $availableServicePointIds = collect($servicePoints)
            ->filter(fn (array $sp): bool => ! $sp['is_busy'])
            ->pluck('id');

        if ($this->selectedServicePointId && $availableServicePointIds->contains($this->selectedServicePointId)) {
            return $this->selectedServicePointId;
        }

        return $availableServicePointIds->first();
    }

    private function resolveBusiness(): Business
    {
        return Business::query()->findOrFail($this->businessId);
    }
}
