<div class="max-w-md mx-auto">
    <div class="brand-card rounded-[2.5rem] p-10 text-center">
        @if($submitted || $alreadySubmitted)
            <div class="space-y-6 py-10">
                <div class="w-20 h-20 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center text-4xl mx-auto shadow-inner">
                    ✨
                </div>
                <div>
                    <h2 class="text-3xl font-black text-slate-900 tracking-tight text-center">Thank You!</h2>
                    <p class="text-slate-500 mt-2 font-medium">Your feedback helps us improve.</p>
                </div>
                <div class="pt-6">
                    <button @click="window.close()" class="text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-slate-600 transition underline underline-offset-4">
                        Close Window
                    </button>
                </div>
            </div>
        @else
            <div class="space-y-8">
                <div>
                    <h1 class="text-3xl font-black text-slate-900 tracking-tight">How was your visit?</h1>
                    <p class="text-sm text-slate-500 mt-2">Rate your experience at <span class="font-bold text-slate-900">{{ $business->name }}</span></p>
                </div>

                <form wire:submit.prevent="submitFeedback" class="space-y-8">
                    {{-- Star Rating --}}
                    <div class="flex flex-col items-center gap-4">
                        <div class="flex gap-2">
                            @for($i = 1; $i <= 5; $i++)
                                <button type="button" wire:click="$set('rating', {{ $i }})" 
                                        class="w-12 h-12 rounded-2xl flex items-center justify-center text-2xl transition-all duration-200 
                                               {{ $rating >= $i ? 'bg-amber-400 scale-110 shadow-lg shadow-amber-200' : 'bg-slate-100 text-slate-300 transform-none hover:bg-slate-200' }}">
                                    ⭐
                                </button>
                            @endfor
                        </div>
                        <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">
                            {{ match($rating) { 1 => 'Poor', 2 => 'Fair', 3 => 'Good', 4 => 'Very Good', 5 => 'Excellent', default => 'Tap a star' } }}
                        </p>
                    </div>

                    {{-- Comment --}}
                    <div class="space-y-2 text-left">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-500 ml-1">Comments (Optional)</label>
                        <textarea wire:model="comment" rows="4" placeholder="Tell us more about your visit..."
                                  class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 text-slate-900 placeholder-slate-400 focus:border-teal-500 focus:ring-4 focus:ring-teal-500/10 transition outline-none font-medium resize-none"></textarea>
                    </div>

                    @error('rating')
                        <p class="text-xs font-bold text-rose-500">Please select a star rating</p>
                    @enderror

                    <button type="submit" 
                            class="btn-teal w-full py-5 rounded-2xl font-black uppercase tracking-widest shadow-lg shadow-teal-500/20">
                        Submit Feedback
                    </button>
                </form>
            </div>
        @endif
    </div>

    <div class="text-center mt-10">
        <span class="qline-logo" style="font-size: 0.9rem;">Q<em>line</em></span>
    </div>
</div>
