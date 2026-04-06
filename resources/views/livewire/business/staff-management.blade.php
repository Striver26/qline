<div class="space-y-8">

    {{-- ═══════════ Header ═══════════ --}}
    <div>
        <flux:subheading class="text-sm font-bold uppercase tracking-widest mb-1" style="color: #14B8A6;">Team</flux:subheading>
        <flux:heading size="xl" class="text-4xl font-black tracking-tight text-gray-900 dark:text-white leading-none">Staff Management</flux:heading>
    </div>

    @if (session()->has('success'))
        <div class="rounded-2xl border p-4 shadow-sm flex items-center space-x-3 bg-emerald-50 dark:bg-emerald-900/10 border-emerald-200 dark:border-emerald-900/30">
            <div class="flex-shrink-0 w-8 h-8 rounded-xl flex items-center justify-center bg-[#14B8A6]">
                <flux:icon.check class="w-4 h-4 text-white" />
            </div>
            <flux:text class="text-sm font-bold text-emerald-700 dark:text-emerald-400">{{ session('success') }}</flux:text>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- ▸ Invite Form --}}
        @if(auth()->user()->isOwner())
            <div class="lg:col-span-1 space-y-6">
                <!-- Invite Form Card -->
                <flux:card class="p-0 overflow-hidden border-gray-100 dark:border-zinc-800">
                    <div class="px-7 py-5 border-b border-gray-100 dark:border-zinc-800 flex items-center space-x-3">
                        <div class="w-8 h-8 rounded-xl flex items-center justify-center bg-emerald-50 dark:bg-emerald-900/20">
                            <flux:icon.envelope class="w-4 h-4 text-[#14B8A6]" />
                        </div>
                        <flux:heading class="text-base font-extrabold text-gray-900 dark:text-white">Invite New Staff</flux:heading>
                    </div>
                    <div class="p-6">
                        <form wire:submit="generateInvite" class="space-y-5">
                            <flux:field>
                                <flux:label class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Email Address</flux:label>
                                <flux:input type="email" wire:model="email" placeholder="staff@example.com" class="font-medium" />
                                <flux:error name="email" />
                            </flux:field>

                            <flux:button type="submit" variant="primary" class="w-full font-bold shadow-md rounded-xl py-3" style="background: #14B8A6; border-color: #14B8A6;">
                                <flux:icon.link class="w-4 h-4 mr-2" />
                                Generate Invite Link
                            </flux:button>
                        </form>
                    </div>
                </flux:card>

                @if(count($invitations) > 0)
                    <flux:card class="p-0 overflow-hidden border-gray-100 dark:border-zinc-800">
                        <div class="px-7 py-4 border-b border-gray-100 dark:border-zinc-800 flex items-center justify-between">
                            <flux:subheading class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Pending Invites</flux:subheading>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-emerald-50 dark:bg-emerald-900/20 text-[#14B8A6]">{{ count($invitations) }}</span>
                        </div>
                        <div class="divide-y divide-gray-50 dark:divide-zinc-800/50 max-h-72 overflow-y-auto">
                            @foreach($invitations as $invite)
                                <div class="p-5">
                                    <div class="flex justify-between items-center mb-2.5">
                                        <flux:text class="text-sm font-bold text-gray-900 dark:text-white truncate">{{ $invite->email }}</flux:text>
                                        <flux:button wire:click="revokeInvite({{ $invite->id }})" variant="ghost" size="sm" class="text-[10px] text-rose-500 hover:text-rose-700 font-bold uppercase tracking-wider p-0 h-auto">Revoke</flux:button>
                                    </div>
                                    <div class="bg-gray-50 dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl p-2.5 flex items-center">
                                        <code class="text-[10px] text-gray-500 dark:text-gray-400 truncate flex-1 font-semibold">{{ route('invite.show', $invite->token) }}</code>
                                    </div>
                                    <flux:text class="text-[10px] text-gray-400 dark:text-gray-500 mt-2 font-semibold">Expires {{ $invite->expires_at->diffForHumans() }}</flux:text>
                                </div>
                            @endforeach
                        </div>
                    </flux:card>
                @endif
            </div>
        @endif

        {{-- ▸ Staff List --}}
        <div class="{{ auth()->user()->isOwner() ? 'lg:col-span-2' : 'lg:col-span-3' }}">
            <flux:card class="p-0 overflow-hidden border-gray-100 dark:border-zinc-800">
                <div class="px-7 py-5 border-b border-gray-100 dark:border-zinc-800 flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded-xl flex items-center justify-center bg-gray-100 dark:bg-zinc-800">
                            <flux:icon.users class="w-4 h-4 text-gray-500" />
                        </div>
                        <flux:heading class="text-base font-extrabold text-gray-900 dark:text-white">Active Team</flux:heading>
                    </div>
                    <span class="text-xs font-bold px-2.5 py-1 rounded-lg bg-gray-100 dark:bg-zinc-800 text-gray-500 dark:text-gray-400">{{ count($staff) }} members</span>
                </div>
                <div class="divide-y divide-gray-50 dark:divide-zinc-800/50">
                    @forelse($staff as $member)
                        <div class="px-7 py-5 flex items-center justify-between hover:bg-gray-50/50 dark:hover:bg-zinc-800/20 transition-colors group">
                            <div class="flex items-center">
                                <flux:avatar :name="$member->name" :initials="$member->initials()" class="w-11 h-11 rounded-2xl shadow-sm text-white font-black text-sm" style="background: linear-gradient(135deg, #14B8A6, #0d9488);" />
                                <div class="ml-4">
                                    <div class="text-sm font-bold text-gray-900 dark:text-white flex items-center">
                                        {{ $member->name }}
                                        @if($member->id === auth()->id())
                                            <flux:badge size="sm" class="ml-2 font-bold uppercase tracking-wider bg-emerald-50 dark:bg-emerald-900/20 text-[#14B8A6] border-0">You</flux:badge>
                                        @endif
                                    </div>
                                    <flux:text class="text-sm text-gray-500 dark:text-gray-400 font-medium">{{ $member->email }}</flux:text>
                                </div>
                            </div>
                            <div class="flex items-center space-x-3">
                                <flux:badge size="sm" class="font-bold uppercase tracking-wider border-0 {{ $member->isOwner() ? 'bg-emerald-50 dark:bg-emerald-900/20 text-[#14B8A6]' : 'bg-gray-100 dark:bg-zinc-800 text-gray-600 dark:text-gray-400' }}">
                                    {{ str_replace('_', ' ', Str::title($member->role->value ?? $member->role)) }}
                                </flux:badge>
                                @if($member->id !== auth()->id() && auth()->user()->isOwner())
                                    <flux:button wire:click="deleteStaff({{ $member->id }})" wire:confirm="Are you sure you want to remove this staff member?"
                                            variant="ghost" size="sm" class="p-2 text-rose-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/20 rounded-xl transition-colors opacity-0 group-hover:opacity-100">
                                        <flux:icon.trash class="h-4 w-4" />
                                    </flux:button>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="py-16 px-4 text-center">
                            <div class="w-14 h-14 mx-auto mb-4 rounded-2xl flex items-center justify-center bg-gray-100 dark:bg-zinc-800">
                                <flux:icon.user-plus class="w-7 h-7 text-gray-300 dark:text-zinc-700" />
                            </div>
                            <flux:heading class="text-sm font-bold text-gray-600 dark:text-gray-400">No staff members yet</flux:heading>
                            <flux:subheading class="text-sm text-gray-400 dark:text-gray-500 mt-1">Send an invite to get started.</flux:subheading>
                        </div>
                    @endforelse
                </div>
            </flux:card>
        </div>
    </div>
</div>