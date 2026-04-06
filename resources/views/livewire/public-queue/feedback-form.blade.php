<div class="max-w-md mx-auto space-y-6">

    {{-- Business info --}}
    <div class="text-center">
        <h1 class="text-2xl font-bold text-white">{{ $business->name }}</h1>
        <p class="text-xs text-slate-500 uppercase tracking-widest mt-1">Rate Your Experience</p>
    </div>

    @if($alreadySubmitted)
        {{-- Already submitted --}}
        <div class="glass-card rounded-3xl p-8 text-center">
            <div class="text-5xl mb-4">💬</div>
            <h2 class="text-xl font-bold text-teal-400">Already Reviewed</h2>
            <p class="text-sm text-slate-500 mt-2">You've already submitted feedback for this visit. Thank you!</p>
        </div>

    @elseif($submitted)
        {{-- Success --}}
        <div class="glass-card rounded-3xl p-8 text-center glow-teal">
            <div class="text-5xl mb-4">🙏</div>
            <h2 class="text-xl font-bold text-teal-400">Thank You!</h2>
            <p class="text-sm text-slate-400 mt-2">Your feedback helps {{ $business->name }} improve their service.</p>

            <div class="flex justify-center gap-1 mt-4">
                @for($i = 1; $i <= 5; $i++)
                    <span class="text-2xl {{ $i <= $rating ? '' : 'opacity-20' }}">⭐</span>
                @endfor
            </div>
        </div>

    @else
        {{-- Ticket reference --}}
        <div class="glass-card rounded-2xl p-4 flex items-center justify-between">
            <div>
                <div class="text-[10px] font-bold uppercase tracking-widest text-slate-500">Ticket</div>
                <div class="text-lg font-black text-white">{{ $entry->ticket_code }}</div>
            </div>
            <div class="text-right">
                <div class="text-[10px] font-bold uppercase tracking-widest text-slate-500">Visited</div>
                <div class="text-sm font-semibold text-slate-400">{{ $entry->created_at->format('d M Y') }}</div>
            </div>
        </div>

        {{-- Rating form --}}
        <div class="glass-card rounded-2xl p-6 space-y-6">

            {{-- Star rating --}}
            <div>
                <label class="text-[10px] font-black uppercase tracking-widest text-slate-500 block mb-4 text-center">
                    How was your experience?
                </label>
                <div class="flex justify-center gap-2">
                    @for($i = 1; $i <= 5; $i++)
                        <button wire:click="setRating({{ $i }})" type="button"
                                class="text-4xl transition-all duration-200 hover:scale-125 focus:outline-none {{ $i <= $rating ? 'scale-110' : 'opacity-30 hover:opacity-60' }}">
                            ⭐
                        </button>
                    @endfor
                </div>
                @if($rating > 0)
                    <div class="text-center mt-2">
                        <span class="text-sm font-bold {{ $rating >= 4 ? 'text-teal-400' : ($rating >= 3 ? 'text-amber-400' : 'text-red-400') }}">
                            @switch($rating)
                                @case(1) Terrible @break
                                @case(2) Poor @break
                                @case(3) Okay @break
                                @case(4) Great @break
                                @case(5) Amazing! @break
                            @endswitch
                        </span>
                    </div>
                @endif
            </div>

            {{-- Comment --}}
            <div>
                <label class="text-[10px] font-black uppercase tracking-widest text-slate-500 block mb-2">
                    Any comments? <span class="text-slate-600">(optional)</span>
                </label>
                <textarea wire:model="comment" rows="3" maxlength="500" placeholder="Tell us more about your experience..."
                    class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-slate-600 focus:border-teal-400/50 focus:outline-none focus:ring-1 focus:ring-teal-400/30 transition text-sm resize-none"></textarea>
                <div class="text-right text-[10px] text-slate-600 mt-1">{{ strlen($comment) }}/500</div>
            </div>

            {{-- Validation error --}}
            @error('rating')
                <p class="text-sm text-red-400">Please select a rating.</p>
            @enderror

            {{-- Submit --}}
            <button wire:click="submitFeedback"
                    wire:loading.attr="disabled"
                    @if($rating === 0) disabled @endif
                    class="w-full py-3.5 bg-teal-400 text-black font-bold rounded-xl hover:opacity-90 transition disabled:opacity-30 disabled:cursor-not-allowed">
                <span wire:loading.remove>Submit Feedback</span>
                <span wire:loading>Submitting...</span>
            </button>
        </div>
    @endif
</div>
