<div class="space-y-6">
    <div class="page-header">
        <div>
            <span class="page-kicker">Customer Voice</span>
            <h1 class="page-title mt-4">Feedback Inbox</h1>
            <p class="page-description mt-3">
                Review ratings, spot friction points quickly, and keep a close pulse on what the service experience feels like.
            </p>
        </div>

        <div class="w-full md:w-52">
            <flux:select wire:model.live="ratingFilter" class="w-full">
                <option value="">All Ratings</option>
                <option value="5">5 stars</option>
                <option value="4">4 stars</option>
                <option value="3">3 stars</option>
                <option value="2">2 stars</option>
                <option value="1">1 star</option>
            </flux:select>
        </div>
    </div>

    <div class="glass-card !p-0 overflow-hidden">
        <div class="divide-y divide-slate-200/70 dark:divide-white/10">
            @forelse($feedbacks as $feedback)
                <div class="p-6 transition-colors hover:bg-white/40 dark:hover:bg-white/5">
                    <div class="flex items-start gap-5">
                        <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-[1.1rem] font-bold text-white shadow-lg
                            {{ $feedback->rating >= 4 ? 'mesh-accent' : ($feedback->rating >= 3 ? 'bg-amber-500' : 'bg-rose-500') }}">
                            {{ $feedback->rating }}
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                <div>
                                    <h2 class="text-lg font-bold tracking-[-0.04em] text-slate-950 dark:text-white">
                                        {{ $feedback->wa_id ?: 'Anonymous Customer' }}
                                    </h2>
                                    <div class="mt-2 flex items-center gap-1">
                                        @for($i = 1; $i <= 5; $i++)
                                            <flux:icon.star class="h-4 w-4 {{ $i <= $feedback->rating ? 'text-amber-400' : 'text-slate-200 dark:text-slate-700' }}" variant="solid" />
                                        @endfor
                                    </div>
                                </div>
                                <p class="text-sm text-slate-400">{{ $feedback->created_at->diffForHumans() }}</p>
                            </div>

                            @if($feedback->comment)
                                <div class="mt-4 rounded-[1.3rem] bg-slate-100/90 p-4 text-sm leading-relaxed text-slate-700 dark:bg-slate-900/70 dark:text-slate-300">
                                    "{{ $feedback->comment }}"
                                </div>
                            @else
                                <p class="mt-4 text-sm italic text-slate-400">No written comment provided.</p>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="px-6 py-16 text-center">
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-[1.25rem] bg-slate-100 dark:bg-slate-800">
                        <flux:icon.chat-bubble-bottom-center-text class="h-8 w-8 text-slate-300 dark:text-slate-600" />
                    </div>
                    <h3 class="mt-5 text-xl font-bold tracking-[-0.04em] text-slate-700 dark:text-slate-200">No feedback yet</h3>
                    <p class="mt-2 text-sm text-slate-400">Ratings will appear here after customers complete their visit.</p>
                </div>
            @endforelse
        </div>

        @if($feedbacks->hasPages())
            <div class="border-t border-slate-200/70 px-6 py-4 dark:border-white/10">
                {{ $feedbacks->links() }}
            </div>
        @endif
    </div>
</div>
