<section class="rounded-[1.5rem] bg-[#0A0F1C] border border-white/5 overflow-hidden flex flex-col h-full">
    <div class="flex items-center justify-between border-b border-white/5 px-6 py-5 shrink-0">
        <h2 class="text-[11px] font-bold uppercase tracking-widest text-slate-500">Service Points</h2>
        <a href="{{ route('business.service-points') }}" class="rounded-md bg-white/5 border border-white/5 px-2.5 py-1 text-[9px] font-bold uppercase tracking-widest text-slate-400 hover:bg-white/10 hover:text-white transition">
            Manage
        </a>
    </div>

    <div class="divide-y divide-white/5 flex-1 overflow-y-auto">
        @forelse($servicePoints as $servicePoint)
            <div
                data-queue-dropzone
                data-target-type="servicePoint"
                data-target-id="{{ $servicePoint['id'] }}"
                data-accepting="{{ $businessState['queue_status'] === 'open' && !$servicePoint['is_busy'] ? 'true' : 'false' }}"
                class="flex items-center justify-between gap-4 px-6 py-4 transition hover:bg-white/[0.02]"
            >
                <div class="flex items-center gap-3 min-w-[120px]">
                    <flux:icon.user class="h-4 w-4 text-slate-400" />
                    <span class="text-[13px] font-bold text-white">{{ $servicePoint['name'] }}</span>
                </div>

                <div class="flex items-center gap-2 min-w-[100px]">
                    <span class="h-2 w-2 rounded-full {{ $servicePoint['is_busy'] ? 'bg-amber-500' : 'bg-teal-500 shadow-[0_0_8px_rgba(45,212,191,0.5)]' }}"></span>
                    <span class="text-[11px] font-semibold uppercase tracking-widest {{ $servicePoint['is_busy'] ? 'text-amber-500' : 'text-teal-400' }}">
                        {{ $servicePoint['is_busy'] ? 'Busy' : 'Available' }}
                    </span>
                </div>

                <div class="flex-1 text-right flex flex-col items-end">
                    @if($servicePoint['is_busy'])
                        <span class="text-[10px] text-slate-500 uppercase tracking-widest mb-0.5">Currently serving</span>
                        <span class="text-[13px] font-black text-amber-400">{{ $servicePoint['active_ticket_code'] }}</span>
                    @else
                        <span class="text-[10px] text-slate-500 uppercase tracking-widest mb-0.5">Next ticket</span>
                        <span class="text-[13px] font-black text-teal-400">{{ $nextEntry['ticket_code'] ?? '--' }}</span>
                    @endif
                </div>
            </div>
        @empty
            <div class="flex h-40 flex-col items-center justify-center text-slate-500">
                <flux:icon.squares-2x2 class="h-6 w-6 opacity-40 mb-2" />
                <p class="text-[13px] font-medium">No service points</p>
            </div>
        @endforelse
    </div>
</section>
