<div class="mx-auto max-w-3xl">
    <div class="glass-card">
        @if($submitted || $alreadySubmitted)
            <div class="space-y-6 py-10 text-center">
                <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-[1.6rem] bg-emerald-100 text-4xl text-emerald-600 shadow-inner">
                    ✓
                </div>
                <div>
                    <span class="page-kicker mx-auto">{{ __('Feedback received') }}</span>
                    <h2 class="mt-4 text-4xl font-bold tracking-[-0.06em] text-slate-950">Thank you.</h2>
                    <p class="mt-3 text-sm text-slate-500 sm:text-base">
                        Your feedback helps {{ $business->name }} improve the experience for the next customer.
                    </p>
                </div>
                <div class="pt-2">
                    <button @click="window.close()" class="btn-link-secondary">
                        Close Window
                    </button>
                </div>
            </div>
        @else
            <div class="grid gap-8 lg:grid-cols-[0.9fr_1.1fr] lg:items-start">
                <div class="soft-card mesh-accent text-white">
                    <p class="text-[0.68rem] font-semibold uppercase tracking-[0.28em] text-white/75">Quick review</p>
                    <h2 class="mt-4 text-4xl font-bold tracking-[-0.06em]">How was your visit at {{ $business->name }}?</h2>
                    <p class="mt-4 text-sm text-white/82">
                        A short rating helps the team spot what feels smooth and what still creates friction for customers.
                    </p>
                </div>

                <form wire:submit.prevent="submitFeedback" class="space-y-8">
                    <div class="space-y-4 text-center">
                        <label class="text-[0.72rem] font-semibold uppercase tracking-[0.24em] text-slate-500">
                            Select a rating
                        </label>
                        <div class="flex flex-wrap justify-center gap-3">
                            @for($i = 1; $i <= 5; $i++)
                                <button
                                    type="button"
                                    wire:click="$set('rating', {{ $i }})"
                                    class="flex h-14 w-14 items-center justify-center rounded-[1.25rem] text-2xl transition duration-200 {{ $rating >= $i ? 'bg-amber-400 text-white shadow-lg shadow-amber-200/80' : 'bg-slate-100 text-slate-300 hover:bg-slate-200' }}"
                                >
                                    ★
                                </button>
                            @endfor
                        </div>
                        <p class="text-sm font-semibold text-slate-500">
                            {{ match($rating) { 1 => 'Poor', 2 => 'Fair', 3 => 'Good', 4 => 'Very Good', 5 => 'Excellent', default => 'Tap a star to rate the experience' } }}
                        </p>
                    </div>

                    <div class="space-y-3">
                        <label class="text-[0.72rem] font-semibold uppercase tracking-[0.24em] text-slate-500">
                            Comments (Optional)
                        </label>
                        <textarea
                            wire:model="comment"
                            rows="5"
                            placeholder="Tell us what stood out, what felt smooth, or what could be better..."
                            class="w-full rounded-[1.2rem] border border-white/75 bg-white/88 px-4 py-4 text-base text-slate-900 shadow-[0_24px_60px_-38px_rgba(15,23,42,0.38)] outline-none transition placeholder:text-slate-400 focus:border-brand-300 focus:ring-4 focus:ring-brand-200/50"
                        ></textarea>
                    </div>

                    @error('rating')
                        <p class="text-sm font-semibold text-rose-600">Please select a star rating before submitting.</p>
                    @enderror

                    <button type="submit" class="mesh-accent inline-flex w-full items-center justify-center rounded-[1.25rem] px-6 py-4 text-sm font-semibold uppercase tracking-[0.24em] text-white shadow-[0_24px_60px_-30px_rgba(15,159,124,0.65)] transition duration-300 hover:-translate-y-0.5">
                        Submit Feedback
                    </button>
                </form>
            </div>
        @endif
    </div>
</div>
