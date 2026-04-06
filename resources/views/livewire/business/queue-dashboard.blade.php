<div class="space-y-10 max-w-6xl mx-auto" wire:poll.10s>

    {{-- ═══════════ Header ═══════════ --}}
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-6">

        <div class="space-y-1">
            <flux:subheading class="text-xs font-extrabold uppercase tracking-[0.2em] text-[#14B8A6]">
                {{ $business->name ?? 'Your Business' }}
            </flux:subheading>

            <flux:heading size="xl" class="text-4xl font-black tracking-tight text-gray-900 dark:text-white">
                Command Center
            </flux:heading>
        </div>

        <div class="flex items-center gap-3">

            {{-- Status --}}
            <div
                class="px-4 py-2.5 rounded-2xl bg-white dark:bg-zinc-900 shadow-sm flex items-center gap-2.5
                {{ $business->queue_status === 'open' ? 'ring-1 ring-emerald-200 dark:ring-emerald-900/40' : 'ring-1 ring-rose-200 dark:ring-rose-900/40' }}">

                @if($business->queue_status === 'open')
                    <span class="relative flex h-2.5 w-2.5">
                        <span class="animate-ping absolute h-full w-full rounded-full bg-emerald-400 opacity-70"></span>
                        <span class="relative rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                    </span>
                    <span class="text-sm font-bold text-emerald-600">Queue Open</span>
                @else
                    <span class="h-2.5 w-2.5 rounded-full bg-rose-500"></span>
                    <span class="text-sm font-bold text-rose-600">Queue Closed</span>
                @endif
            </div>

            {{-- Toggle --}}
            <flux:button wire:click="toggleQueue"
                class="px-6 py-2.5 rounded-2xl font-extrabold shadow-lg hover:scale-[1.03] active:scale-[0.97] transition-all"
                style="{{ $business->queue_status === 'open'
    ? ''
    : 'background: #14B8A6; border-color: #14B8A6; color: white;' }}"
                :variant="$business->queue_status === 'open' ? 'danger' : 'primary'">
                <flux:icon class="w-4 h-4 mr-2" :name="$business->queue_status === 'open' ? 'x-mark' : 'bolt'" />
                {{ $business->queue_status === 'open' ? 'Close Queue' : 'Open Queue' }}
            </flux:button>

        </div>
    </div>

    {{-- ═══════════ Stats ═══════════ --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">

        {{-- Waiting --}}
        <flux:card
            class="p-6 rounded-2xl border-0 shadow-sm hover:shadow-md transition-all relative overflow-hidden group">
            <div
                class="absolute inset-0 opacity-0 group-hover:opacity-100 transition bg-gradient-to-br from-[#14B8A6]/5">
            </div>

            <div class="flex items-center justify-between relative">
                <div>
                    <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-2">Waiting</p>
                    <p class="text-4xl font-black text-gray-900 dark:text-white">
                        {{ count($this->waitingEntries) }}
                    </p>
                </div>

                <div
                    class="w-14 h-14 rounded-2xl flex items-center justify-center bg-emerald-50 dark:bg-emerald-900/20">
                    <flux:icon.users class="w-7 h-7 text-[#14B8A6]" />
                </div>
            </div>

            <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-[#14B8A6] to-teal-300"></div>
        </flux:card>

        {{-- Served --}}
        <flux:card
            class="p-6 rounded-2xl border-0 shadow-sm hover:shadow-md transition-all relative overflow-hidden group">
            <div
                class="absolute inset-0 opacity-0 group-hover:opacity-100 transition bg-gradient-to-br from-indigo-500/5">
            </div>

            <div class="flex items-center justify-between relative">
                <div>
                    <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-2">Served Today</p>
                    <p class="text-4xl font-black text-gray-900 dark:text-white">
                        {{ max(0, ($business->entries_today ?? 0) - count($this->waitingEntries)) }}
                    </p>
                </div>

                <div class="w-14 h-14 rounded-2xl flex items-center justify-center bg-indigo-50 dark:bg-indigo-900/30">
                    <flux:icon.check-circle class="w-7 h-7 text-indigo-500" />
                </div>
            </div>

            <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-indigo-400 to-violet-500"></div>
        </flux:card>

        {{-- Call Next --}}
        <flux:button wire:click="callNext" :disabled="$business->queue_status !== 'open'"
            class="p-6 rounded-[1.5rem] text-left transition-all duration-300 shadow-lg hover:shadow-2xl active:scale-[0.97] group"
            style="{{ $business->queue_status === 'open'
    ? 'background: linear-gradient(135deg, #14B8A6, #0d9488); color:white;'
    : 'background:#f3f4f6; color:#9ca3af;' }}">
            <div class="flex items-center justify-between">
                <div>
                    <p
                        class="text-xs uppercase tracking-widest font-bold {{ $business->queue_status === 'open' ? 'text-white/70' : 'text-gray-400' }}">
                        Next Action
                    </p>
                    <p class="text-3xl font-black">Call Next</p>
                </div>

                <div class="w-14 h-14 rounded-2xl flex items-center justify-center
                    {{ $business->queue_status === 'open' ? 'bg-white/20' : 'bg-gray-200 dark:bg-zinc-800' }}">
                    <flux:icon.arrow-right class="w-7 h-7 group-hover:translate-x-1 transition" />
                </div>
            </div>
        </flux:button>

    </div>

    {{-- ═══════════ Lists ═══════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

        {{-- Currently Serving --}}
        <flux:card class="overflow-hidden border-0 shadow-sm rounded-2xl">

            <div class="px-6 py-5 border-b border-gray-100 dark:border-zinc-800 flex justify-between items-center">
                <flux:heading class="text-base font-extrabold">Currently Serving</flux:heading>
                <span class="text-xs font-bold text-[#14B8A6] bg-emerald-50 px-2.5 py-1 rounded-lg">
                    {{ count($this->activeEntries) }} active
                </span>
            </div>

            <div class="p-5 space-y-3">
                @forelse($this->activeEntries as $entry)

                    <div
                        class="flex justify-between items-center p-4 rounded-2xl bg-gray-50/70 dark:bg-zinc-800/40 hover:bg-white dark:hover:bg-zinc-800 transition group">

                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-white font-black shadow"
                                style="background: linear-gradient(135deg, #14B8A6, #0d9488);">
                                {{ $entry->ticket_code }}
                            </div>

                            <div>
                                <p class="text-sm font-bold">
                                    {{ $entry->wa_id ? '📱 ' . $entry->wa_id : '🏢 Walk-in' }}
                                </p>
                                <span class="text-xs font-semibold text-gray-400">
                                    {{ ucfirst($entry->status) }}
                                </span>
                            </div>
                        </div>

                        <div class="flex gap-2 opacity-60 group-hover:opacity-100 transition">
                            <flux:button wire:click="markDone({{ $entry->id }})" size="sm" variant="ghost"
                                class="p-2.5 rounded-xl bg-emerald-50 text-emerald-600">
                                <flux:icon.check class="w-4 h-4" />
                            </flux:button>

                            <flux:button wire:click="skip({{ $entry->id }})" size="sm" variant="ghost"
                                class="p-2.5 rounded-xl bg-rose-50 text-rose-600">
                                <flux:icon.forward class="w-4 h-4" />
                            </flux:button>
                        </div>

                    </div>

                @empty
                    <div class="text-center py-16 text-gray-400">
                        No active tickets
                    </div>
                @endforelse
            </div>
        </flux:card>

        {{-- Waiting --}}
        <flux:card class="overflow-hidden border-0 shadow-sm rounded-2xl">

            <div class="px-6 py-5 border-b border-gray-100 dark:border-zinc-800 flex justify-between items-center">
                <flux:heading class="text-base font-extrabold">Waiting Line</flux:heading>
                <span class="text-xs font-bold bg-gray-100 px-2.5 py-1 rounded-lg">
                    {{ count($this->waitingEntries) }} in queue
                </span>
            </div>

            <div class="p-5 space-y-2.5">
                @forelse($this->waitingEntries as $entry)

                    <div
                        class="flex items-center p-3.5 rounded-xl hover:bg-gray-50 dark:hover:bg-zinc-800/50 transition group">

                        <div class="w-9 h-9 flex items-center justify-center rounded-xl font-black text-xs
                                {{ $loop->first ? 'text-white' : 'bg-gray-100 text-gray-500' }}"
                            style="{{ $loop->first ? 'background: linear-gradient(135deg,#14B8A6,#0d9488)' : '' }}">
                            #{{ $entry->position }}
                        </div>

                        <div class="ml-4 flex-1 flex justify-between">
                            <div>
                                <p class="text-sm font-bold">{{ $entry->ticket_code }}</p>
                                <p class="text-xs text-gray-400">
                                    {{ $entry->source === 'whatsapp' ? 'WhatsApp' : 'Walk-in' }}
                                </p>
                            </div>

                            <span class="text-xs text-gray-400 opacity-0 group-hover:opacity-100">
                                {{ $entry->created_at->diffForHumans() }}
                            </span>
                        </div>

                    </div>

                @empty
                    <div class="text-center py-16 text-gray-400">
                        Queue is empty
                    </div>
                @endforelse
            </div>
        </flux:card>
    </div>
</div>