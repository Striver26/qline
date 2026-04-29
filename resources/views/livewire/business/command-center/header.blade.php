<div class="space-y-6">
    <div class="page-header">
        <div>
            <span class="page-kicker">{{ $businessState['name'] }}</span>
            <h1 class="page-title mt-4">Command Center</h1>
            <p class="page-description mt-3">
                Manage live flow from one shared queue snapshot, assign customers to counters or tables, and keep every screen in sync instantly.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <div class="badge-pill {{ $businessState['queue_status'] === 'open' ? 'badge-pill--brand' : ($businessState['queue_status'] === 'paused' ? 'border-amber-200 bg-amber-100 text-amber-700' : 'border-rose-200 bg-rose-100 text-rose-700') }}">
                <span class="h-2.5 w-2.5 rounded-full {{ $businessState['queue_status'] === 'open' ? 'bg-brand-500' : ($businessState['queue_status'] === 'paused' ? 'bg-amber-500' : 'bg-rose-500') }}"></span>
                {{ ucfirst($businessState['queue_status']) }}
            </div>

            @if($businessState['queue_status'] === 'open')
                <flux:button 
                    wire:click="$set('showPauseModal', true)"
                    variant="subtle" 
                    class="rounded-full px-5 py-2.5 font-semibold text-amber-600 hover:bg-amber-50"
                >
                    <flux:icon.pause class="mr-2 h-4 w-4" />
                    Pause
                </flux:button>
            @elseif($businessState['queue_status'] === 'paused')
                <flux:button wire:click="$dispatch('command-center.resume-queue')" variant="subtle" class="rounded-full px-5 py-2.5 font-semibold text-brand-600 hover:bg-brand-50">
                    <flux:icon.play class="mr-2 h-4 w-4" />
                    Resume
                </flux:button>
            @endif

            <flux:button
                href="{{ route('public.tv', ['slug' => $businessState['slug'], 'token' => $businessState['tv_token']]) }}"
                target="_blank"
                variant="subtle"
                class="rounded-full px-5 py-2.5 font-semibold text-slate-600 hover:bg-slate-100"
            >
                <flux:icon.tv class="mr-2 h-4 w-4" />
                TV Board
            </flux:button>

            <flux:button
                wire:click="$dispatch('command-center.quick-add')"
                variant="subtle"
                class="rounded-full px-5 py-2.5 font-semibold text-brand-600 hover:bg-brand-50"
                :disabled="$businessState['queue_status'] !== 'open'"
            >
                <flux:icon.plus class="mr-2 h-4 w-4" />
                Quick Add
            </flux:button>

            <flux:button
                wire:click="$dispatch('command-center.toggle-queue')"
                class="rounded-full px-5 py-2.5 font-semibold shadow-[0_24px_60px_-32px_rgba(15,23,42,0.4)]"
                :variant="$businessState['queue_status'] === 'closed' ? 'primary' : 'danger'"
            >
                <flux:icon class="mr-2 h-4 w-4" :name="$businessState['queue_status'] === 'closed' ? 'bolt' : 'x-mark'" />
                {{ $businessState['queue_status'] === 'closed' ? 'Open Queue' : 'Close Queue' }}
            </flux:button>
        </div>
    </div>

    <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
        <div class="metric-card">
            <p class="metric-label">Waiting Right Now</p>
            <div class="mt-5 flex items-end justify-between gap-4">
                <p class="metric-value mt-0">{{ $metrics['waiting_count'] }}</p>
                <div class="flex h-14 w-14 items-center justify-center rounded-[1.25rem] bg-brand-50 text-brand-700">
                    <flux:icon.users class="h-7 w-7" />
                </div>
            </div>
        </div>

        <div class="metric-card">
            <p class="metric-label">Active Tickets</p>
            <div class="mt-5 flex items-end justify-between gap-4">
                <p class="metric-value mt-0">{{ $metrics['active_count'] }}</p>
                <div class="flex h-14 w-14 items-center justify-center rounded-[1.25rem] bg-coral-50 text-coral-700">
                    <flux:icon.bolt class="h-7 w-7" />
                </div>
            </div>
        </div>

        <div class="metric-card">
            <p class="metric-label">Served Today</p>
            <div class="mt-5 flex items-end justify-between gap-4">
                <p class="metric-value mt-0">{{ $metrics['served_today'] }}</p>
                <div class="flex h-14 w-14 items-center justify-center rounded-[1.25rem] bg-emerald-50 text-emerald-700">
                    <flux:icon.check-circle class="h-7 w-7" />
                </div>
            </div>
        </div>

        <div class="metric-card">
            <p class="metric-label">Free Tables</p>
            <div class="mt-5 flex items-end justify-between gap-4">
                <p class="metric-value mt-0">{{ $metrics['free_table_count'] }}</p>
                <div class="flex h-14 w-14 items-center justify-center rounded-[1.25rem] bg-slate-100 text-slate-700">
                    <flux:icon.squares-2x2 class="h-7 w-7" />
                </div>
            </div>
        </div>
    </div>

    @if($businessState['queue_status'] === 'paused' && $businessState['pause_reason'])
        <div class="rounded-[1.5rem] border border-amber-200 bg-amber-50 px-5 py-4 text-sm font-semibold text-amber-700 shadow-sm">
            Queue paused: {{ $businessState['pause_reason'] }}
        </div>
    @endif

    <flux:modal wire:model="showPauseModal" name="command-center-pause-queue" class="md:max-w-md">
        <form wire:submit="submitPause" class="space-y-6">
            <div>
                <flux:heading size="lg">Pause Queue</flux:heading>
                <flux:subheading>Let customers know why new calls are temporarily on hold.</flux:subheading>
            </div>

            <flux:input
                wire:model="pauseReason"
                label="Pause Reason"
                placeholder="e.g. Short break, preparing service area..."
                required
            />

            <div class="flex items-center gap-2">
                <flux:modal.close>
                    <flux:button type="button" variant="ghost">Cancel</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary">Pause Queue</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
