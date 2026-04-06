<div class="space-y-10 max-w-5xl mx-auto">

    {{-- ═══════════ Header ═══════════ --}}
    <div class="flex flex-col gap-2">
        <flux:subheading class="text-xs font-extrabold uppercase tracking-[0.2em] text-[#14B8A6]">
            Plans
        </flux:subheading>

        <flux:heading size="xl" class="text-4xl font-black tracking-tight text-gray-900 dark:text-white">
            Billing & Subscription
        </flux:heading>

        <p class="text-sm text-gray-500 dark:text-gray-400 max-w-md">
            Manage your subscription and unlock premium queue features.
        </p>
    </div>

    {{-- Error --}}
    @if (session()->has('error'))
        <div class="rounded-2xl border border-red-200 dark:border-red-900/40 bg-red-50/80 dark:bg-red-900/10 backdrop-blur p-4 flex items-center gap-3 shadow-sm">
            <div class="w-9 h-9 rounded-xl bg-red-500 flex items-center justify-center shadow">
                <flux:icon.x-mark class="w-4 h-4 text-white" />
            </div>
            <flux:text class="text-sm font-semibold text-red-700 dark:text-red-400">
                {{ session('error') }}
            </flux:text>
        </div>
    @endif

    {{-- ═══════════ Current Plan ═══════════ --}}
    <flux:card class="relative p-8 overflow-hidden border-gray-100 dark:border-zinc-800 shadow-sm">

        {{-- glow --}}
        <div class="absolute -right-10 -top-10 w-40 h-40 rounded-full opacity-10 blur-3xl bg-[#14B8A6]"></div>

        <div class="relative flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6">

            <div>
                <flux:subheading class="text-[11px] font-black uppercase tracking-widest text-gray-400 mb-2">
                    Current Plan
                </flux:subheading>

                @if($subscription && $subscription->status === 'active')
                    <div class="flex items-center gap-3 flex-wrap">
                        <flux:heading size="xl" class="text-3xl font-black text-gray-900 dark:text-white">
                            {{ ucfirst($subscription->tier) }} Pass
                        </flux:heading>

                        <flux:badge size="sm" class="font-bold uppercase tracking-wider bg-emerald-50 dark:bg-emerald-900/20 text-[#14B8A6] border-0 px-2.5 py-1">
                            <span class="w-1.5 h-1.5 rounded-full mr-1.5 animate-pulse bg-[#14B8A6]"></span>
                            Active
                        </flux:badge>
                    </div>

                    <flux:text class="text-sm text-gray-500 mt-2">
                        Expires on 
                        <span class="font-semibold text-gray-700 dark:text-gray-300">
                            {{ $subscription->expires_at->format('M d, Y h:i A') }}
                        </span>
                    </flux:text>
                @else
                    <div class="flex items-center gap-3 flex-wrap">
                        <flux:heading size="xl" class="text-3xl font-black text-gray-900 dark:text-white">
                            No Active Plan
                        </flux:heading>

                        <flux:badge size="sm" class="font-bold uppercase tracking-wider bg-amber-50 dark:bg-amber-900/20 text-amber-600 border-0 px-2.5 py-1">
                            Inactive
                        </flux:badge>
                    </div>

                    <flux:text class="text-sm text-gray-500 mt-2">
                        Choose a plan below to activate your queue system.
                    </flux:text>
                @endif
            </div>

        </div>
    </flux:card>

    {{-- ═══════════ Pricing ═══════════ --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

        {{-- Daily --}}
        <flux:card class="relative overflow-hidden flex flex-col group border border-gray-100 dark:border-zinc-800 rounded-2xl hover:shadow-lg transition-all duration-300">

            <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition duration-500 
                bg-gradient-to-br from-[#14B8A6]/5 to-transparent"></div>

            <div class="p-8 flex-1 relative">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-11 h-11 rounded-2xl flex items-center justify-center bg-emerald-50 dark:bg-emerald-900/20">
                        <flux:icon.sun class="w-5 h-5 text-[#14B8A6]" />
                    </div>

                    <flux:heading class="text-lg font-extrabold text-gray-900 dark:text-white">
                        Daily Pass
                    </flux:heading>
                </div>

                <div class="flex items-baseline mb-6">
                    <span class="text-5xl font-black text-gray-900 dark:text-white">RM 15</span>
                    <span class="ml-2 text-sm text-gray-400 font-semibold">/day</span>
                </div>

                <ul class="space-y-3 text-sm">
                    @foreach([
                        'Standard Queue features',
                        'WhatsApp notifications',
                        'QR Code poster'
                    ] as $feature)
                        <li class="flex items-center text-gray-600 dark:text-gray-400 font-medium">
                            <flux:icon.check class="w-5 h-5 mr-3 text-[#14B8A6]" />
                            {{ $feature }}
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="p-8 pt-0">
                <flux:button 
                    wire:click="subscribe('daily')" 
                    class="w-full py-3.5 rounded-xl font-bold border-2 border-[#14B8A6] text-[#14B8A6] 
                    bg-emerald-50 dark:bg-teal-900/10 hover:bg-emerald-100 hover:scale-[1.02] transition-all">
                    Buy Daily Pass
                </flux:button>
            </div>
        </flux:card>

        {{-- Monthly --}}
        <div class="relative rounded-[1.75rem] overflow-hidden flex flex-col group shadow-xl hover:shadow-2xl transition-all text-white">

            {{-- gradient --}}
            <div class="absolute inset-0 bg-gradient-to-br from-teal-700 via-teal-600 to-[#14B8A6]"></div>

            {{-- glow --}}
            <div class="absolute -right-10 -top-10 w-44 h-44 bg-white/20 blur-3xl rounded-full"></div>
            <div class="absolute -left-10 -bottom-10 w-52 h-52 bg-white/10 blur-3xl rounded-full"></div>

            <div class="relative p-8 flex-1">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-2xl bg-white/20 flex items-center justify-center backdrop-blur">
                            <flux:icon.sparkles class="w-5 h-5 text-white" />
                        </div>

                        <flux:heading class="text-lg font-extrabold text-white">
                            Monthly Ultimate
                        </flux:heading>
                    </div>

                    <flux:badge size="sm" class="bg-white/20 text-white font-black uppercase tracking-wider px-3 py-1 backdrop-blur">
                        Best Value
                    </flux:badge>
                </div>

                <div class="flex items-baseline mb-6">
                    <span class="text-5xl font-black">RM 400</span>
                    <span class="ml-2 text-sm text-white/70 font-semibold">/month</span>
                </div>

                <ul class="space-y-3 text-sm">
                    @foreach([
                        'All Daily features included',
                        'Multi-Counter Access',
                        'Loyalty Campaign Engine',
                        'Customer Feedback Module'
                    ] as $feature)
                        <li class="flex items-center text-white/90 font-medium">
                            <flux:icon.check class="w-5 h-5 mr-3 text-white/70" />
                            {{ $feature }}
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="relative p-8 pt-0">
                <flux:button 
                    wire:click="subscribe('monthly')" 
                    class="w-full py-3.5 rounded-xl font-black bg-white text-[#0d9488] 
                    hover:bg-gray-100 hover:scale-[1.02] transition-all shadow-lg">
                    Upgrade to Monthly
                </flux:button>
            </div>
        </div>
    </div>
</div>