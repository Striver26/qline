<div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 border-b border-white/5 pb-6">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-white flex items-center gap-3">
            Command Center
        </h1>
        <p class="text-sm text-slate-500 mt-1">
            Manage your queue and serve customers smoothly.
        </p>
    </div>

    <div class="flex items-center gap-3">
        @if($businessState['queue_status'] === 'open')
            <button
                wire:click="$dispatch('command-center.toggle-queue')"
                class="flex items-center gap-2 rounded-full border border-teal-500/30 bg-teal-500/10 px-4 py-2 text-sm font-semibold text-teal-400 transition hover:bg-teal-500/20"
            >
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-teal-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-teal-500 shadow-[0_0_8px_rgba(45,212,191,1)]"></span>
                </span>
                Open
            </button>
        @else
            <button
                wire:click="$dispatch('command-center.toggle-queue')"
                class="flex items-center gap-2 rounded-full border border-slate-700 bg-slate-800/50 px-4 py-2 text-sm font-semibold text-slate-400 transition hover:bg-slate-800"
            >
                <div class="h-2 w-2 rounded-full bg-slate-500"></div>
                Closed
            </button>
        @endif

        @if($businessState['queue_status'] === 'open')
            <button
                wire:click="$set('showPauseModal', true)"
                class="flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-4 py-2 text-sm font-semibold text-white transition hover:bg-white/10"
            >
                <flux:icon.pause class="h-4 w-4 opacity-70" />
                Pause
            </button>
        @elseif($businessState['queue_status'] === 'paused')
            <button 
                wire:click="$dispatch('command-center.resume-queue')" 
                class="flex items-center gap-2 rounded-full border border-amber-500/30 bg-amber-500/10 px-4 py-2 text-sm font-semibold text-amber-400 transition hover:bg-amber-500/20"
            >
                <flux:icon.play class="h-4 w-4" />
                Resume
            </button>
        @endif

        <a
            href="{{ route('public.tv', ['slug' => $businessState['slug'], 'token' => $businessState['tv_token']]) }}"
            target="_blank"
            class="flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-4 py-2 text-sm font-semibold text-white transition hover:bg-white/10"
        >
            <flux:icon.tv class="h-4 w-4 opacity-70" />
            TV Board
        </a>

        <button
            wire:click="$dispatch('command-center.quick-add')"
            class="flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-4 py-2 text-sm font-semibold text-white transition hover:bg-white/10 disabled:opacity-50 disabled:cursor-not-allowed"
            @disabled($businessState['queue_status'] !== 'open')
        >
            <flux:icon.plus class="h-4 w-4 opacity-70" />
            Quick Add
        </button>
    </div>

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
