<div class="max-w-md mx-auto space-y-8">
    {{-- Branding/Business Info --}}
    <div class="text-center space-y-2">
        <h1 class="text-4xl font-black text-slate-900 tracking-tight">{{ $business->name }}</h1>
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-teal-50 border border-teal-100">
            <span class="w-1.5 h-1.5 rounded-full bg-teal-500 animate-pulse"></span>
            <span class="text-[10px] font-bold uppercase tracking-widest text-teal-600">Join the Queue</span>
        </div>
    </div>

    {{-- Stats Row --}}
    <div class="grid grid-cols-2 gap-4">
        <div class="brand-card rounded-2xl p-5 text-center">
            <div class="text-3xl font-black text-slate-900">{{ $this->waitingCount }}</div>
            <div class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mt-1">Waiting Now</div>
        </div>
        <div class="brand-card rounded-2xl p-5 text-center">
            <div class="text-3xl font-black text-slate-900">~{{ $this->waitingCount * 5 }}m</div>
            <div class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mt-1">Est. Wait Time</div>
        </div>
    </div>

    <div class="brand-card rounded-3xl p-8 space-y-6">

        <form wire:submit.prevent="joinQueue" class="space-y-6">
            <div>
                <label class="text-[10px] font-black uppercase tracking-widest text-slate-500 block mb-2">
                    WhatsApp Number <span class="text-slate-400 font-medium">(Recommended)</span>
                </label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">📱</span>
                    <input type="tel" wire:model="phone" placeholder="e.g. 60123456789"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-11 pr-4 py-4 text-slate-900 placeholder-slate-400 focus:border-teal-500 focus:ring-4 focus:ring-teal-500/10 transition outline-none font-semibold">
                </div>
                <p class="text-[10px] text-slate-400 mt-3 leading-relaxed">
                    @if($activeReward)
                        Enter your number to earn points and get notified on WhatsApp when it's your turn.
                    @else
                        Get an instant WhatsApp notification when your turn is ready. Leave blank to join without one.
                    @endif
                </p>
            </div>

            @if($activeReward)
                <div class="p-4 bg-teal-50 border-2 border-dashed border-teal-200 rounded-2xl flex items-center gap-4">
                    <div class="text-3xl">🎁</div>
                    <div>
                        <div class="text-[10px] font-black uppercase tracking-widest text-teal-600">Loyalty Special</div>
                        <div class="text-sm font-bold text-slate-800">
                            {{ $activeReward->reward_value }} <span class="text-slate-500 font-medium">on your
                                {{ $activeReward->required_visits }}{{ $activeReward->required_visits == 1 ? 'st' : ($activeReward->required_visits == 2 ? 'nd' : ($activeReward->required_visits == 3 ? 'rd' : 'th')) }}
                                visit</span>
                        </div>
                    </div>
                </div>
            @endif

            @if($errorMessage)
                <div
                    class="p-4 bg-rose-50 border border-rose-100 rounded-xl text-rose-600 text-xs font-bold flex items-center gap-2">
                    <span>⚠️</span> {{ $errorMessage }}
                </div>
            @endif

            <button type="submit" wire:loading.attr="disabled"
                class="btn-teal w-full py-5 rounded-2xl font-black uppercase tracking-widest shadow-lg shadow-teal-500/20 disabled:opacity-50">
                <span wire:loading.remove>Take a Ticket</span>
                <span wire:loading>Joining...</span>
            </button>
        </form>
    </div>

    {{-- Browser persistence logic (Hidden) --}}
    <div x-data x-init="
        let tickets = JSON.parse(localStorage.getItem('qline_active_tickets') || '{}');
        if (tickets['{{ $business->slug }}']) {
            window.location.href = '/q/{{ $business->slug }}/status/' + tickets['{{ $business->slug }}'];
        }
    "></div>
</div>