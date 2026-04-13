<div class="space-y-6">
    <div class="page-header">
        <div>
            <span class="page-kicker">Team Setup</span>
            <h1 class="page-title mt-4">Staff Management</h1>
            <p class="page-description mt-3">
                Invite teammates, keep roles clear, and make sure the right people can keep the queue moving.
            </p>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="rounded-[1.5rem] border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-700 shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid gap-6 xl:grid-cols-3">
        @if(auth()->user()->isOwner())
            <div class="space-y-6 xl:col-span-1">
                <div class="glass-card">
                    <span class="page-kicker">Invite Staff</span>
                    <h2 class="mt-4 text-2xl font-bold tracking-[-0.05em] text-slate-950 dark:text-white">Add a new teammate</h2>

                    <form wire:submit="generateInvite" class="mt-6 space-y-5">
                        <flux:field>
                            <flux:label>{{ __('Email Address') }}</flux:label>
                            <flux:input type="email" wire:model="email" placeholder="staff@example.com" class="font-medium" />
                            <flux:error name="email" />
                        </flux:field>

                        <flux:button type="submit" variant="primary" class="mesh-accent w-full rounded-[1.2rem] py-3 font-semibold text-white">
                            <flux:icon.link class="mr-2 h-4 w-4" />
                            Generate Invite Link
                        </flux:button>
                    </form>
                </div>

                @if(count($invitations) > 0)
                    <div class="glass-card !p-0 overflow-hidden">
                        <div class="flex items-center justify-between border-b border-slate-200/70 px-6 py-5 dark:border-white/10">
                            <div>
                                <p class="metric-label">Pending</p>
                                <h2 class="mt-2 text-2xl font-bold tracking-[-0.05em] text-slate-950 dark:text-white">Invitations</h2>
                            </div>
                            <span class="badge-pill badge-pill--brand">{{ count($invitations) }}</span>
                        </div>

                        <div class="divide-y divide-slate-200/70 dark:divide-white/10">
                            @foreach($invitations as $invite)
                                <div class="space-y-4 px-6 py-5">
                                    <div class="flex items-start justify-between gap-4">
                                        <div>
                                            <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $invite->email }}</p>
                                            <p class="mt-1 text-xs uppercase tracking-[0.24em] text-slate-400">
                                                Expires {{ $invite->expires_at->diffForHumans() }}
                                            </p>
                                        </div>

                                        <flux:button wire:click="revokeInvite({{ $invite->id }})" variant="ghost" size="sm" class="rounded-full px-3 text-rose-600">
                                            Revoke
                                        </flux:button>
                                    </div>

                                    <div class="rounded-[1.2rem] bg-slate-100/90 px-4 py-3 text-xs font-medium text-slate-600 dark:bg-slate-900/70 dark:text-slate-300">
                                        {{ route('invite.show', $invite->token) }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @endif

        <div class="{{ auth()->user()->isOwner() ? 'xl:col-span-2' : 'xl:col-span-3' }}">
            <div class="glass-card !p-0 overflow-hidden">
                <div class="flex items-center justify-between border-b border-slate-200/70 px-6 py-5 dark:border-white/10">
                    <div>
                        <p class="metric-label">Team Roster</p>
                        <h2 class="mt-2 text-2xl font-bold tracking-[-0.05em] text-slate-950 dark:text-white">Active Team</h2>
                    </div>
                    <span class="badge-pill">{{ count($staff) }} members</span>
                </div>

                <div class="divide-y divide-slate-200/70 dark:divide-white/10">
                    @forelse($staff as $member)
                        <div class="flex items-center justify-between gap-4 px-6 py-5 transition-colors hover:bg-white/35 dark:hover:bg-white/5">
                            <div class="flex min-w-0 items-center gap-4">
                                <flux:avatar :name="$member->name" :initials="$member->initials()" class="h-12 w-12 rounded-[1rem] bg-brand-500 text-white" />
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <p class="truncate text-sm font-semibold text-slate-900 dark:text-white">{{ $member->name }}</p>
                                        @if($member->id === auth()->id())
                                            <span class="badge-pill badge-pill--brand">You</span>
                                        @endif
                                    </div>
                                    <p class="truncate text-sm text-slate-500 dark:text-slate-400">{{ $member->email }}</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3">
                                <span class="badge-pill {{ $member->isOwner() ? 'badge-pill--brand' : '' }}">
                                    {{ str_replace('_', ' ', Str::title($member->role->value ?? $member->role)) }}
                                </span>

                                @if($member->id !== auth()->id() && auth()->user()->isOwner())
                                    <flux:button
                                        wire:click="deleteStaff({{ $member->id }})"
                                        wire:confirm="Are you sure you want to remove this staff member?"
                                        variant="ghost"
                                        size="sm"
                                        class="rounded-full bg-rose-50 px-3 text-rose-600"
                                    >
                                        <flux:icon.trash class="h-4 w-4" />
                                    </flux:button>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-16 text-center">
                            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-[1.25rem] bg-slate-100 dark:bg-slate-800">
                                <flux:icon.user-plus class="h-8 w-8 text-slate-300 dark:text-slate-600" />
                            </div>
                            <h3 class="mt-5 text-xl font-bold tracking-[-0.04em] text-slate-700 dark:text-slate-200">No staff members yet</h3>
                            <p class="mt-2 text-sm text-slate-400">Send an invite to bring the rest of the team in.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
