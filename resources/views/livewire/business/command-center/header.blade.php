<div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
    <div>
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-teal-500/10 ring-1 ring-teal-500/20">
                <flux:icon.bolt class="h-5 w-5 text-teal-400" />
            </div>
            <div>
                <h1 class="text-xl font-bold tracking-tight text-white">Command Center</h1>
                <p class="text-[12px] text-slate-500 mt-0.5">Manage your queue and serve customers smoothly.</p>
            </div>
        </div>
    </div>

    <div class="flex flex-wrap items-center gap-2">
        @if($businessState['queue_status'] === 'open')
            <button
                wire:click="$dispatch('command-center.toggle-queue')"
                class="inline-flex items-center gap-2 rounded-full border border-teal-500/30 bg-teal-500/10 px-3.5 py-1.5 text-[13px] font-semibold text-teal-400 transition hover:bg-teal-500/20"
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
                class="inline-flex items-center gap-2 rounded-full border border-slate-700 bg-slate-800/50 px-3.5 py-1.5 text-[13px] font-semibold text-slate-400 transition hover:bg-slate-800"
            >
                <div class="h-2 w-2 rounded-full bg-slate-500"></div>
                Closed
            </button>
        @endif

        @if($businessState['queue_status'] === 'open')
            <button
                wire:click="$set('showPauseModal', true)"
                class="inline-flex items-center gap-1.5 rounded-full border border-white/[0.06] bg-white/[0.04] px-3.5 py-1.5 text-[13px] font-semibold text-slate-300 transition hover:bg-white/[0.08] hover:text-white"
            >
                <flux:icon.pause class="h-3.5 w-3.5 opacity-60" />
                Pause
            </button>
        @elseif($businessState['queue_status'] === 'paused')
            <button 
                wire:click="$dispatch('command-center.resume-queue')" 
                class="inline-flex items-center gap-1.5 rounded-full border border-amber-500/30 bg-amber-500/10 px-3.5 py-1.5 text-[13px] font-semibold text-amber-400 transition hover:bg-amber-500/20"
            >
                <flux:icon.play class="h-3.5 w-3.5" />
                Resume
            </button>
        @endif

        <a
            href="{{ route('public.tv', ['slug' => $businessState['slug'], 'token' => $businessState['tv_token']]) }}"
            target="_blank"
            class="inline-flex items-center gap-1.5 rounded-full border border-white/[0.06] bg-white/[0.04] px-3.5 py-1.5 text-[13px] font-semibold text-slate-300 transition hover:bg-white/[0.08] hover:text-white"
        >
            <flux:icon.tv class="h-3.5 w-3.5 opacity-60" />
            TV Board
        </a>

        <button
            wire:click="$dispatch('command-center.quick-add')"
            class="inline-flex items-center gap-1.5 rounded-full border border-white/[0.06] bg-white/[0.04] px-3.5 py-1.5 text-[13px] font-semibold text-slate-300 transition hover:bg-white/[0.08] hover:text-white disabled:opacity-40 disabled:cursor-not-allowed"
            @disabled($businessState['queue_status'] !== 'open')
        >
            <flux:icon.plus class="h-3.5 w-3.5 opacity-60" />
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
