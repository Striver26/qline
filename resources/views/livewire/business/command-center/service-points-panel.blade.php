<section class="glass-card !p-0 overflow-hidden">
    <div class="flex items-center justify-between border-b border-slate-200/70 px-6 py-5 dark:border-white/10">
        <div>
            <p class="metric-label">Service Points</p>
            <h2 class="mt-2 text-2xl font-bold tracking-[-0.05em] text-slate-950 dark:text-white">Active Service Points</h2>
        </div>

        <span class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">Drop targets</span>
    </div>

    <div class="space-y-6 p-5">
        @if($businessState['can_use_servicePoints'] && count($servicePoints))
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="metric-label">Service Points</p>
                        <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Select a default service point or drag a waiting ticket straight onto one.</p>
                    </div>
                    @if($selectedServicePointId)
                        <span class="badge-pill">Service Point selected</span>
                    @endif
                </div>

                <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-3">
                    @foreach($servicePoints as $servicePoint)
                        <div
                            data-queue-dropzone
                            data-target-type="servicePoint"
                            data-target-id="{{ $servicePoint['id'] }}"
                            data-accepting="{{ $businessState['queue_status'] === 'open' && !$servicePoint['is_busy'] ? 'true' : 'false' }}"
                            class="rounded-[1.4rem] border p-4 transition {{ $selectedServicePointId === $servicePoint['id'] ? 'border-brand-200 bg-brand-50/70' : 'border-slate-200 bg-white' }} {{ $servicePoint['is_busy'] ? 'opacity-60' : '' }}"
                        >
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-[0.68rem] font-semibold uppercase tracking-[0.22em] text-slate-400">Service Point</p>
                                    <p class="mt-2 text-lg font-bold tracking-[-0.04em] text-slate-950">{{ $servicePoint['name'] }}</p>
                                    <p class="mt-2 text-xs uppercase tracking-[0.2em] text-slate-400">
                                        {{ $servicePoint['is_busy'] ? "Busy with {$servicePoint['active_ticket_code']}" : 'Available for next call' }}
                                    </p>
                                </div>

                                <button
                                    type="button"
                                    wire:click="$dispatch('command-center.select-servicePoint', { servicePointId: {{ $servicePoint['id'] }} })"
                                    @disabled($servicePoint['is_busy'])
                                    class="rounded-full border border-slate-200 bg-white px-3 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-slate-600 transition hover:border-brand-200 hover:text-brand-700 disabled:cursor-not-allowed disabled:opacity-50"
                                >
                                    {{ $selectedServicePointId === $servicePoint['id'] ? 'Selected' : 'Select' }}
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="soft-card text-sm text-slate-500">
                No service points are configured yet. Once created, they will appear here as live drop targets.
            </div>
        @endif
    </div>
</section>
