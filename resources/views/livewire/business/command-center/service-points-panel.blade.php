<section class="rounded-2xl border border-white/[0.06] bg-white/[0.02] overflow-hidden backdrop-blur-sm flex flex-col">
    <div class="flex items-center justify-between border-b border-white/[0.06] px-5 py-4 shrink-0">
        <h2 class="text-[11px] font-bold uppercase tracking-widest text-slate-500">Service Points</h2>
        <a href="{{ route('business.service-points') }}" class="rounded-md bg-white/5 ring-1 ring-white/[0.06] px-2.5 py-1 text-[9px] font-bold uppercase tracking-widest text-slate-400 hover:bg-white/10 hover:text-white transition">
            Manage
        </a>
    </div>

    <div class="divide-y divide-white/[0.04] flex-1 overflow-y-auto">
        @forelse($servicePoints as $servicePoint)
            <div
                data-queue-dropzone
                data-target-type="servicePoint"
                data-target-id="{{ $servicePoint['id'] }}"
                data-accepting="{{ $businessState['queue_status'] === 'open' && !$servicePoint['is_busy'] ? 'true' : 'false' }}"
                class="flex items-center justify-between gap-3 px-5 py-3.5 transition hover:bg-white/[0.02]"
            >
                <div class="flex items-center gap-2.5 min-w-0">
                    <span class="h-2 w-2 shrink-0 rounded-full {{ $servicePoint['is_busy'] ? 'bg-amber-500' : 'bg-teal-500 shadow-[0_0_8px_rgba(45,212,191,0.5)]' }}"></span>
                    <span class="text-[13px] font-bold text-white truncate">{{ $servicePoint['name'] }}</span>
                </div>

                <div class="flex items-center gap-3 shrink-0">
                    <span class="text-[10px] font-semibold uppercase tracking-widest {{ $servicePoint['is_busy'] ? 'text-amber-500' : 'text-teal-400' }}">
                        {{ $servicePoint['is_busy'] ? 'Busy' : 'Free' }}
                    </span>

                    <div class="text-right min-w-[50px]">
                        @if($servicePoint['is_busy'])
                            <span class="text-[12px] font-black text-amber-400">{{ $servicePoint['active_ticket_code'] }}</span>
                        @else
                            <span class="text-[12px] font-black text-slate-600">--</span>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="flex h-32 flex-col items-center justify-center text-slate-500">
                <flux:icon.squares-2x2 class="h-5 w-5 opacity-40 mb-2" />
                <p class="text-[12px] font-medium">No service points</p>
            </div>
        @endforelse
    </div>
</section>
