<div class="space-y-8">
    <div class="page-header">
        <div>
            <span class="page-kicker">Service Terminals</span>
            <h1 class="page-title mt-4">Service Point Management</h1>
            <p class="page-description mt-3">
                Define Service Points that can receive live queue assignments.
            </p>
        </div>
    </div>

    @if (session()->has('error'))
        <div class="rounded-[1.5rem] border border-rose-200 bg-rose-50 px-5 py-4 text-sm font-semibold text-rose-700 shadow-sm">
            {{ session('error') }}
        </div>
    @endif

    @if (session()->has('success'))
        <div class="rounded-[1.5rem] border border-brand-200 bg-brand-50 px-5 py-4 text-sm font-semibold text-brand-700 shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="glass-card">
            <form wire:submit.prevent="addServicePoint" class="flex flex-col gap-4">
                <div>
                    <p class="metric-label">Service Points</p>
                    <h2 class="mt-2 text-2xl font-bold tracking-[-0.05em] text-slate-950 dark:text-white">Add a Service Point</h2>
                </div>

                <div class="flex flex-col items-end gap-4 sm:flex-row">
                    <div class="flex-1">
                        <flux:input wire:model="newName" label="Service Point Name" placeholder="e.g. Reception, Table 1, Room A..." />
                    </div>
                    <flux:button type="submit" variant="primary" class="rounded-full px-6">
                        <flux:icon.plus class="mr-2 h-4 w-4" />
                        Add Service Point
                    </flux:button>
                </div>
            </form>
        </div>
    </div>

    <div class="space-y-4">
        <div>
            <p class="metric-label">Service Points</p>
            <h2 class="mt-2 text-2xl font-bold tracking-[-0.05em] text-slate-950 dark:text-white">Configured targets</h2>
        </div>

        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach($servicePoints as $servicePoint)
                <div class="soft-card flex flex-col justify-between">
                    <div>
                        <div class="flex items-start justify-between">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-xl {{ $servicePoint->is_active ? 'bg-brand-100 text-brand-700' : 'bg-slate-100 text-slate-400' }}">
                                    <flux:icon.computer-desktop class="h-5 w-5" />
                                </div>
                                <div>
                                    @if($editingServicePointId === $servicePoint->id)
                                        <flux:input wire:model="editName" wire:keydown.enter="updateServicePoint" dense />
                                    @else
                                        <h3 class="font-bold text-slate-900 dark:text-white">{{ $servicePoint->name }}</h3>
                                        <p class="text-xs text-slate-400 uppercase tracking-widest mt-0.5">
                                            {{ $servicePoint->is_active ? 'Online' : 'Offline' }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                            <flux:switch wire:click="toggleServicePoint({{ $servicePoint->id }})" :checked="$servicePoint->is_active" />
                        </div>
                    </div>

                    <div class="mt-6 flex items-center justify-between border-t border-slate-100 dark:border-white/5 pt-4">
                        <div class="flex items-center gap-2">
                            @if($editingServicePointId === $servicePoint->id)
                                <flux:button wire:click="updateServicePoint" size="sm" variant="ghost" class="text-brand-600">Save</flux:button>
                                <flux:button wire:click="$set('editingServicePointId', null)" size="sm" variant="ghost">Cancel</flux:button>
                            @else
                                <flux:button wire:click="editServicePoint({{ $servicePoint->id }})" size="sm" variant="ghost" icon="pencil-square" inset />
                                <flux:button wire:click="deleteServicePoint({{ $servicePoint->id }})" wire:confirm="Are you sure you want to remove this service point?" size="sm" variant="ghost" class="text-rose-500" icon="trash" inset />
                            @endif
                        </div>
                        <span class="text-[10px] font-semibold uppercase tracking-widest text-slate-400">
                            {{ $servicePoint->queue_entries_count }} lifetime tickets
                        </span>
                    </div>
                </div>
            @endforeach
        </div>

        @if($servicePoints->isEmpty())
            <div class="glass-card flex flex-col items-center justify-center py-16 text-center">
                <h3 class="text-xl font-bold text-slate-900">No service points defined yet.</h3>
                <p class="mt-2 text-slate-500 max-w-xs mx-auto">
                    Add your first service point.
                </p>
            </div>
        @endif
    </div>
</div>
