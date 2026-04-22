<div class="space-y-8">
    <div class="page-header">
        <div>
            <span class="page-kicker">Service Terminals</span>
            <h1 class="page-title mt-4">Counter Management</h1>
            <p class="page-description mt-3">
                Define the physical or logical windows where customers are served.
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

    <div class="glass-card">
        <form wire:submit.prevent="addCounter" class="flex flex-col sm:flex-row gap-4 items-end">
            <div class="flex-1">
                <flux:input wire:model="newName" label="Counter Name" placeholder="e.g. Counter 1, Reception, Room A..." />
            </div>
            <flux:button type="submit" variant="primary" class="rounded-full px-6">
                <flux:icon.plus class="mr-2 h-4 w-4" />
                Add Counter
            </flux:button>
        </form>
    </div>

    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        @foreach($counters as $counter)
            <div class="soft-card flex flex-col justify-between">
                <div>
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl {{ $counter->is_active ? 'bg-brand-100 text-brand-700' : 'bg-slate-100 text-slate-400' }}">
                                <flux:icon.computer-desktop class="h-5 w-5" />
                            </div>
                            <div>
                                @if($editingCounterId === $counter->id)
                                    <flux:input wire:model="editName" wire:keydown.enter="updateCounter" dense />
                                @else
                                    <h3 class="font-bold text-slate-900 dark:text-white">{{ $counter->name }}</h3>
                                    <p class="text-xs text-slate-400 uppercase tracking-widest mt-0.5">
                                        {{ $counter->is_active ? 'Online' : 'Offline' }}
                                    </p>
                                @endif
                            </div>
                        </div>
                        <flux:switch wire:click="toggleCounter({{ $counter->id }})" :checked="$counter->is_active" />
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-between border-t border-slate-100 dark:border-white/5 pt-4">
                    <div class="flex items-center gap-2">
                        @if($editingCounterId === $counter->id)
                            <flux:button wire:click="updateCounter" size="sm" variant="ghost" class="text-brand-600">Save</flux:button>
                            <flux:button wire:click="$set('editingCounterId', null)" size="sm" variant="ghost">Cancel</flux:button>
                        @else
                            <flux:button wire:click="editCounter({{ $counter->id }})" size="sm" variant="ghost" icon="pencil-square" inset />
                            <flux:button wire:click="deleteCounter({{ $counter->id }})" wire:confirm="Are you sure you want to remove this counter?" size="sm" variant="ghost" class="text-rose-500" icon="trash" inset />
                        @endif
                    </div>
                    <span class="text-[10px] font-semibold uppercase tracking-widest text-slate-400">
                        {{ $counter->queueEntries()->count() }} tickets served
                    </span>
                </div>
            </div>
        @endforeach
    </div>

    @if($counters->isEmpty())
        <div class="glass-card flex flex-col items-center justify-center py-20 text-center">
            <div class="h-20 w-20 rounded-3xl bg-slate-50 flex items-center justify-center text-slate-200">
                <flux:icon.computer-desktop class="h-10 w-10" />
            </div>
            <h3 class="mt-6 text-xl font-bold text-slate-900">No counters defined yet.</h3>
            <p class="mt-2 text-slate-500 max-w-xs mx-auto">
                Add your first service counter to start assigning tickets to specific windows.
            </p>
        </div>
    @endif
</div>
