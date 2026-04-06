<div class="space-y-8" wire:poll.10s>

    {{-- ═══════════ Header ═══════════ --}}
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
        <div class="flex flex-col gap-1">
            <flux:subheading class="font-bold uppercase tracking-widest" style="color: #14B8A6;">
                {{ $business->name ?? 'Your Business' }}
            </flux:subheading>
            <flux:heading size="xl" class="font-black tracking-tight leading-none text-gray-900 dark:text-white">Command
                Center</flux:heading>
        </div>
        <div class="flex items-center space-x-3">
            {{-- Status Pill --}}
            <div
                class="px-4 py-2.5 rounded-2xl border bg-white dark:bg-zinc-900 shadow-sm flex items-center space-x-2.5 {{ $business->queue_status === 'open' ? 'border-emerald-200 dark:border-emerald-900/50' : 'border-rose-200 dark:border-rose-900/50' }}">
                @if($business->queue_status === 'open')
                    <span class="relative flex h-2.5 w-2.5">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                    </span>
                    <flux:text class="text-sm font-bold text-emerald-700 dark:text-emerald-400">Queue Open</flux:text>
                @else
                    <span class="relative flex h-2.5 w-2.5">
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-rose-500"></span>
                    </span>
                    <flux:text class="text-sm font-bold text-rose-700 dark:text-rose-400">Queue Closed</flux:text>
                @endif
            </div>

            {{-- Toggle Button --}}
            <flux:button wire:click="toggleQueue" :variant="$business->queue_status === 'open' ? 'danger' : 'primary'"
                class="font-extrabold shadow-lg px-6 py-2.5 rounded-2xl"
                style="{{ $business->queue_status === 'open' ? '' : 'background: #14B8A6; border-color: #14B8A6;' }}">
                @if($business->queue_status === 'open')
                    <flux:icon.x-mark class="w-4 h-4 mr-2" />
                    Close Queue
                @else
                    <flux:icon.bolt class="w-4 h-4 mr-2" />
                    Open Queue
                @endif
            </flux:button>
        </div>
    </div>

    {{-- ═══════════ Stats Cards ═══════════ --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">

        {{-- Waiting --}}
        <flux:card class="relative p-6 group overflow-hidden border-gray-100 dark:border-zinc-800">
            <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-500"
                style="background: linear-gradient(135deg, rgba(20,184,166,0.03), rgba(20,184,166,0.08));"></div>
            <div class="relative flex items-center justify-between">
                <div>
                    <flux:subheading class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-2">Waiting
                    </flux:subheading>
                    <flux:heading size="xl" class="text-4xl font-black text-gray-900 dark:text-white">
                        {{ count($this->waitingEntries) }}
                    </flux:heading>
                </div>
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center shadow-sm"
                    style="background-color: #f0fdfa;">
                    <flux:icon.users class="w-7 h-7" style="color: #14B8A6;" />
                </div>
            </div>
            <div class="absolute bottom-0 left-0 right-0 h-1"
                style="background: linear-gradient(90deg, #14B8A6, #2dd4bf);"></div>
        </flux:card>

        {{-- Served Today --}}
        <flux:card class="relative p-6 group overflow-hidden border-gray-100 dark:border-zinc-800">
            <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-500"
                style="background: linear-gradient(135deg, rgba(99,102,241,0.03), rgba(99,102,241,0.08));"></div>
            <div class="relative flex items-center justify-between">
                <div>
                    <flux:subheading class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-2">Served Today
                    </flux:subheading>
                    <flux:heading size="xl" class="text-4xl font-black text-gray-900 dark:text-white">
                        {{ max(0, ($business->entries_today ?? 0) - count($this->waitingEntries)) }}
                    </flux:heading>
                </div>
                <div
                    class="w-14 h-14 rounded-2xl flex items-center justify-center shadow-sm bg-indigo-50 dark:bg-indigo-900/30">
                    <flux:icon.check-circle class="w-7 h-7 text-indigo-500" />
                </div>
            </div>
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-indigo-400 to-violet-500"></div>
        </flux:card>

        {{-- Call Next (Big Action) --}}
        <flux:button wire:click="callNext" :disabled="$business->queue_status !== 'open'"
            class="relative rounded-[1.25rem] p-6 h-auto text-left transition-all duration-300 overflow-hidden group shadow-lg hover:shadow-2xl active:scale-[0.97] border-0"
            style="{{ $business->queue_status === 'open' ? 'background: linear-gradient(135deg, #14B8A6, #0d9488); box-shadow: 0 10px 25px -5px rgba(20,184,166,0.4); color: white;' : 'background: #f3f4f6; color: #9ca3af;' }}">
            <div
                class="absolute inset-0 bg-white opacity-0 group-hover:opacity-10 transition-opacity duration-300 rounded-[1.25rem]">
            </div>
            <div class="relative flex items-center justify-between w-full">
                <div>
                    <flux:subheading
                        class="text-xs font-bold uppercase tracking-widest {{ $business->queue_status === 'open' ? 'text-white/70' : 'text-gray-400' }} mb-2">
                        Next Action</flux:subheading>
                    <flux:heading size="xl"
                        class="text-3xl font-black {{ $business->queue_status === 'open' ? 'text-white' : 'text-gray-400' }}">
                        Call Next</flux:heading>
                </div>
                <div
                    class="w-14 h-14 rounded-2xl flex items-center justify-center {{ $business->queue_status === 'open' ? 'bg-white/20' : 'bg-gray-200 dark:bg-zinc-800' }}">
                    <flux:icon.arrow-right class="w-7 h-7 group-hover:translate-x-1 transition-transform" />
                </div>
            </div>
        </flux:button>
    </div>

    {{-- ═══════════ Two-Column Lists ═══════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- ▸ Currently Serving --}}
        <flux:card class="p-0 overflow-hidden border-gray-100 dark:border-zinc-800">
            <div class="px-7 py-5 border-b border-gray-100 dark:border-zinc-800 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 rounded-xl flex items-center justify-center" style="background-color: #f0fdfa;">
                        <flux:icon.bolt class="w-4 h-4" style="color: #14B8A6;" />
                    </div>
                    <flux:heading class="text-base font-extrabold text-gray-900 dark:text-white">Currently Serving
                    </flux:heading>
                </div>
                <span class="text-xs font-bold px-2.5 py-1 rounded-lg"
                    style="color: #14B8A6; background-color: #f0fdfa;">{{ count($this->activeEntries) }} active</span>
            </div>

            <div class="p-5 space-y-3">
                @forelse($this->activeEntries as $entry)
                    <div
                        class="flex items-center justify-between p-4 bg-gray-50/80 dark:bg-zinc-800/40 rounded-2xl border border-gray-100 dark:border-zinc-700/50 hover:bg-white dark:hover:bg-zinc-800 hover:shadow-sm transition-all group">
                        <div class="flex items-center space-x-4">
                            {{-- Ticket Badge --}}
                            <div class="w-14 h-14 rounded-2xl flex items-center justify-center font-black text-base text-white shadow-sm"
                                style="background: linear-gradient(135deg, #14B8A6, #0d9488);">
                                {{ $entry->ticket_code }}
                            </div>
                            <div>
                                <flux:text class="block text-sm font-bold text-gray-900 dark:text-white">
                                    {{ $entry->wa_id ? '📱 ' . $entry->wa_id : '🏢 Walk-in' }}
                                </flux:text>
                                <span
                                    class="inline-flex items-center mt-1 text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-md
                                            {{ $entry->status === 'called' ? 'bg-amber-50 text-amber-600 dark:bg-amber-900/20 dark:text-amber-400' : 'bg-emerald-50 text-emerald-600 dark:bg-emerald-900/20 dark:text-emerald-400' }}">
                                    <span
                                        class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $entry->status === 'called' ? 'bg-amber-400 animate-pulse' : 'bg-emerald-400' }}"></span>
                                    {{ $entry->status }}
                                </span>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex items-center space-x-2 opacity-60 group-hover:opacity-100 transition-opacity">
                            @if($entry->status === 'called')
                                <flux:button wire:click="markServing({{ $entry->id }})" size="sm" variant="ghost"
                                    class="p-2.5 rounded-xl bg-blue-50 text-blue-600 hover:bg-blue-100 dark:bg-blue-900/20 dark:text-blue-400"
                                    title="Start Serving">
                                    <flux:icon.play class="w-4 h-4" />
                                </flux:button>
                            @endif
                            <flux:button wire:click="markDone({{ $entry->id }})" size="sm" variant="ghost"
                                class="p-2.5 rounded-xl bg-emerald-50 text-emerald-600 hover:bg-emerald-100 dark:bg-emerald-900/20 dark:text-emerald-400 transition-colors"
                                title="Mark Done">
                                <flux:icon.check class="w-4 h-4" />
                            </flux:button>
                            <flux:button wire:click="skip({{ $entry->id }})" size="sm" variant="ghost"
                                class="p-2.5 rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-100 dark:bg-rose-900/20 dark:text-rose-400 transition-colors"
                                title="Skip">
                                <flux:icon.forward class="w-4 h-4" />
                            </flux:button>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-16 px-4">
                        <div
                            class="w-16 h-16 mx-auto mb-4 rounded-2xl flex items-center justify-center bg-gray-100 dark:bg-zinc-800">
                            <flux:icon.check class="w-8 h-8 text-gray-300 dark:text-zinc-700" />
                        </div>
                        <flux:heading class="text-sm font-bold text-gray-600 dark:text-gray-400">No active tickets
                        </flux:heading>
                        <flux:subheading class="mt-1 text-sm text-gray-400 dark:text-gray-500">Hit "Call Next" to start
                            serving.</flux:subheading>
                    </div>
                @endforelse
            </div>
        </flux:card>

        {{-- ▸ Waiting Line --}}
        <flux:card class="p-0 overflow-hidden border-gray-100 dark:border-zinc-800">
            <div class="px-7 py-5 border-b border-gray-100 dark:border-zinc-800 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 rounded-xl flex items-center justify-center bg-gray-100 dark:bg-zinc-800">
                        <flux:icon.clock class="w-4 h-4 text-gray-500" />
                    </div>
                    <flux:heading class="text-base font-extrabold text-gray-900 dark:text-white">Waiting Line
                    </flux:heading>
                </div>
                <span
                    class="text-xs font-bold px-2.5 py-1 rounded-lg bg-gray-100 dark:bg-zinc-800 text-gray-500 dark:text-gray-400">{{ count($this->waitingEntries) }}
                    in queue</span>
            </div>

            <div class="p-5 space-y-2.5">
                @forelse($this->waitingEntries as $entry)
                    <div
                        class="flex items-center p-3.5 rounded-xl hover:bg-gray-50 dark:hover:bg-zinc-800/50 transition-all group border border-transparent hover:border-gray-100 dark:hover:border-zinc-700/50">
                        {{-- Position Badge --}}
                        <div class="flex-shrink-0 w-9 h-9 rounded-xl flex items-center justify-center text-xs font-black {{ $loop->first ? 'text-white' : 'bg-gray-100 text-gray-500 border border-gray-200 dark:bg-zinc-800 dark:border-zinc-700 dark:text-gray-400' }}"
                            style="{{ $loop->first ? 'background: linear-gradient(135deg, #14B8A6, #0d9488);' : '' }}">
                            #{{ $entry->position }}
                        </div>
                        <div class="ml-4 flex-1 flex justify-between items-center">
                            <div>
                                <flux:text class="block text-sm font-bold text-gray-900 dark:text-white">
                                    {{ $entry->ticket_code }}
                                </flux:text>
                                <flux:text
                                    class="block text-[11px] font-semibold {{ $entry->source === 'whatsapp' ? 'text-emerald-500' : 'text-gray-400 dark:text-gray-500' }}">
                                    {{ $entry->source === 'whatsapp' ? '🟢 WhatsApp' : '⚪ Walk-in' }}
                                </flux:text>
                            </div>
                            <flux:text
                                class="text-[11px] text-gray-400 dark:text-gray-500 font-semibold opacity-0 group-hover:opacity-100 transition-opacity">
                                {{ $entry->created_at->diffForHumans() }}
                            </flux:text>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-16 px-4">
                        <div
                            class="w-16 h-16 mx-auto mb-4 rounded-2xl flex items-center justify-center bg-gray-100 dark:bg-zinc-800">
                            <flux:icon.ellipsis-horizontal class="w-8 h-8 text-gray-300 dark:text-zinc-700" />
                        </div>
                        <flux:heading class="text-sm font-bold text-gray-600 dark:text-gray-400">Empty queue</flux:heading>
                        <flux:subheading class="mt-1 text-sm text-gray-400 dark:text-gray-500">Customers will appear here
                            when they join.</flux:subheading>
                    </div>
                @endforelse
            </div>
        </flux:card>
    </div>
</div>