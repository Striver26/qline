<div class="space-y-8">

    {{-- ═══════════ Header ═══════════ --}}
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
        <div>
            <flux:subheading class="text-sm font-bold uppercase tracking-widest mb-1" style="color: #14B8A6;">Reviews</flux:subheading>
            <flux:heading size="xl" class="text-4xl font-black tracking-tight text-gray-900 dark:text-white leading-none">Customer Feedback</flux:heading>
        </div>
        <div>
            <flux:select wire:model.live="ratingFilter" class="w-44">
                <option value="">All Ratings</option>
                <option value="5">⭐⭐⭐⭐⭐ (5)</option>
                <option value="4">⭐⭐⭐⭐ (4)</option>
                <option value="3">⭐⭐⭐ (3)</option>
                <option value="2">⭐⭐ (2)</option>
                <option value="1">⭐ (1)</option>
            </flux:select>
        </div>
    </div>

    {{-- ═══════════ Feedback List ═══════════ --}}
    <flux:card class="p-0 overflow-hidden border-gray-100 dark:border-zinc-800">
        <div class="divide-y divide-gray-50 dark:divide-zinc-800/50">
            @forelse($feedbacks as $feedback)
                <div class="p-6 hover:bg-gray-50/50 dark:hover:bg-zinc-800/30 transition-colors group">
                    <div class="flex items-start space-x-5">
                        {{-- Rating Badge --}}
                        <div class="flex-shrink-0">
                            <div class="w-14 h-14 rounded-2xl flex items-center justify-center shadow-sm font-black text-lg text-white"
                                 style="background: linear-gradient(135deg,
                                    {{ $feedback->rating >= 4 ? '#14B8A6, #0d9488' : ($feedback->rating >= 3 ? '#f59e0b, #d97706' : '#ef4444, #dc2626') }});">
                                {{ $feedback->rating }}
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <flux:heading size="sm" class="font-bold text-gray-900 dark:text-white">
                                    {{ $feedback->wa_id ?: 'Anonymous Customer' }}
                                </flux:heading>
                                <flux:text class="text-[11px] text-gray-400 font-semibold">
                                    {{ $feedback->created_at->diffForHumans() }}
                                </flux:text>
                            </div>
                            {{-- Stars --}}
                            <div class="mt-1.5 flex items-center space-x-0.5">
                                @for($i = 1; $i <= 5; $i++)
                                    <flux:icon.star class="h-4 w-4 {{ $i <= $feedback->rating ? 'text-amber-400' : 'text-gray-200 dark:text-zinc-700' }}" variant="solid" />
                                @endfor
                            </div>

                            @if($feedback->comments)
                                <div class="mt-3 text-sm text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-zinc-800/50 p-4 rounded-xl border border-gray-100 dark:border-zinc-700/50 leading-relaxed font-medium">
                                    "{{ $feedback->comments }}"
                                </div>
                            @else
                                <flux:text class="mt-2 text-sm text-gray-400 italic">No comment provided.</flux:text>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="py-20 px-4 text-center">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-2xl flex items-center justify-center bg-gray-100 dark:bg-zinc-800">
                        <flux:icon.chat-bubble-bottom-center-text class="w-8 h-8 text-gray-300 dark:text-zinc-700" />
                    </div>
                    <flux:heading class="text-sm font-bold text-gray-600 dark:text-gray-400">No feedback yet</flux:heading>
                    <flux:subheading class="mt-1 text-sm text-gray-400 dark:text-gray-500">Ratings will appear here after customers are served.</flux:subheading>
                </div>
            @endforelse
        </div>

        @if($feedbacks->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 dark:border-zinc-800 bg-gray-50/50 dark:bg-zinc-900/50">
                {{ $feedbacks->links() }}
            </div>
        @endif
    </flux:card>
</div>