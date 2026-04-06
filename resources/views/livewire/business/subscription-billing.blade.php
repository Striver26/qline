<div class="space-y-8 max-w-5xl mx-auto">

    {{-- ═══════════ Header ═══════════ --}}
    <div>
        <flux:subheading class="text-sm font-bold uppercase tracking-widest mb-1" style="color: #14B8A6;">Plans</flux:subheading>
        <flux:heading size="xl" class="text-4xl font-black tracking-tight text-gray-900 dark:text-white leading-none">Billing & Subscription</flux:heading>
    </div>

    @if (session()->has('error'))
        <div class="rounded-2xl bg-red-50 dark:bg-red-900/10 border border-red-200 dark:border-red-900/30 p-4 flex items-center space-x-3">
            <div class="flex-shrink-0 w-8 h-8 rounded-xl bg-red-500 flex items-center justify-center">
                <flux:icon.x-mark class="w-4 h-4 text-white" />
            </div>
            <flux:text class="text-sm font-bold text-red-700 dark:text-red-400">{{ session('error') }}</flux:text>
        </div>
    @endif

    {{-- ═══════════ Current Plan Banner ═══════════ --}}
    <flux:card class="relative p-8 overflow-hidden border-gray-100 dark:border-zinc-800">
        <div class="absolute -right-6 -top-6 w-32 h-32 rounded-full opacity-10 blur-2xl bg-[#14B8A6]"></div>
        <div class="relative flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <flux:subheading class="text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500 mb-2">Current Plan</flux:subheading>
                @if($subscription && $subscription->status === 'active')
                    <div class="flex items-center space-x-3">
                        <flux:heading size="xl" class="text-3xl font-black text-gray-900 dark:text-white">{{ ucfirst($subscription->tier) }} Pass</flux:heading>
                        <flux:badge size="sm" class="font-bold uppercase tracking-wider bg-emerald-50 dark:bg-emerald-900/20 text-[#14B8A6] border-0">
                            <span class="w-1.5 h-1.5 rounded-full mr-1.5 animate-pulse bg-[#14B8A6]"></span>
                            Active
                        </flux:badge>
                    </div>
                    <flux:text class="text-sm text-gray-500 dark:text-gray-400 mt-2 font-medium">Expires on <span class="font-bold text-gray-700 dark:text-gray-300">{{ $subscription->expires_at->format('M d, Y h:i A') }}</span></flux:text>
                @else
                    <div class="flex items-center space-x-3">
                        <flux:heading size="xl" class="text-3xl font-black text-gray-900 dark:text-white">No Active Plan</flux:heading>
                        <flux:badge size="sm" class="font-bold uppercase tracking-wider bg-amber-50 dark:bg-amber-900/20 text-amber-600 border-0">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-400 mr-1.5"></span>
                            Inactive
                        </flux:badge>
                    </div>
                    <flux:text class="text-sm text-gray-500 dark:text-gray-400 mt-2 font-medium">Purchase a pass below to activate your queue.</flux:text>
                @endif
            </div>
        </div>
    </flux:card>

    {{-- ═══════════ Pricing Cards ═══════════ --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- Daily Pass --}}
        <flux:card class="relative p-0 group overflow-hidden flex flex-col border-gray-100 dark:border-zinc-800 hover:shadow-md transition-all">
            <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-500" style="background: linear-gradient(135deg, rgba(20,184,166,0.02), rgba(20,184,166,0.06));"></div>
            <div class="p-8 relative flex-1">
                <div class="flex items-center space-x-3 mb-6">
                    <div class="w-10 h-10 rounded-2xl flex items-center justify-center bg-emerald-50 dark:bg-emerald-900/20">
                        <flux:icon.sun class="w-5 h-5 text-[#14B8A6]" />
                    </div>
                    <flux:heading class="text-lg font-extrabold text-gray-900 dark:text-white">Daily Pass</flux:heading>
                </div>
                <div class="flex items-baseline mb-6">
                    <flux:text class="text-5xl font-black text-gray-900 dark:text-white">RM 15</flux:text>
                    <flux:text class="ml-2 text-base font-semibold text-gray-400">/day</flux:text>
                </div>
                <ul class="space-y-4">
                    <li class="flex items-center text-sm font-medium text-gray-600 dark:text-gray-400">
                        <flux:icon.check class="flex-shrink-0 h-5 w-5 mr-3 text-[#14B8A6]" />
                        Standard Queue features
                    </li>
                    <li class="flex items-center text-sm font-medium text-gray-600 dark:text-gray-400">
                        <flux:icon.check class="flex-shrink-0 h-5 w-5 mr-3 text-[#14B8A6]" />
                        WhatsApp notifications
                    </li>
                    <li class="flex items-center text-sm font-medium text-gray-600 dark:text-gray-400">
                        <flux:icon.check class="flex-shrink-0 h-5 w-5 mr-3 text-[#14B8A6]" />
                        QR Code poster
                    </li>
                </ul>
            </div>
            <div class="p-8 pt-0 relative">
                <flux:button wire:click="subscribe('daily')" variant="outline" class="w-full py-3.5 h-auto rounded-2xl font-bold border-2 text-[#14B8A6] border-[#14B8A6] bg-emerald-50 dark:bg-teal-900/10 hover:bg-emerald-100 transition-all">
                    Buy Daily Pass
                </flux:button>
            </div>
        </flux:card>

        {{-- Monthly Ultimate --}}
        <div class="relative rounded-[1.5rem] shadow-xl overflow-hidden flex flex-col group hover:shadow-2xl transition-all text-white border-0" style="background: linear-gradient(135deg, #0f766e, #0d9488, #14B8A6);">
            <div class="absolute -right-8 -top-8 w-40 h-40 rounded-full opacity-20 bg-white blur-3xl"></div>
            <div class="absolute -left-10 -bottom-10 w-48 h-48 rounded-full opacity-10 bg-white blur-3xl"></div>

            <div class="p-8 relative flex-1">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-2xl bg-white/20 flex items-center justify-center backdrop-blur-sm">
                            <flux:icon.sparkles class="w-5 h-5 text-white" />
                        </div>
                        <flux:heading class="text-lg font-extrabold text-white">Monthly Ultimate</flux:heading>
                    </div>
                    <flux:badge size="sm" class="font-black uppercase tracking-wider bg-white/20 backdrop-blur-sm text-white border-0">
                        Best Value
                    </flux:badge>
                </div>
                <div class="flex items-baseline mb-6">
                    <span class="text-5xl font-black">RM 400</span>
                    <span class="ml-2 text-base font-semibold text-white/60">/month</span>
                </div>
                <ul class="space-y-4">
                    <li class="flex items-center text-sm font-medium text-white/90">
                        <flux:icon.check class="flex-shrink-0 h-5 w-5 mr-3 text-white/70" />
                        All Daily features included
                    </li>
                    <li class="flex items-center text-sm font-bold">
                        <flux:icon.check class="flex-shrink-0 h-5 w-5 mr-3 text-white/70" />
                        <span class="bg-white/15 px-2.5 py-0.5 rounded-md backdrop-blur-sm">Multi-Counter Access</span>
                    </li>
                    <li class="flex items-center text-sm font-bold">
                        <flux:icon.check class="flex-shrink-0 h-5 w-5 mr-3 text-white/70" />
                        <span class="bg-white/15 px-2.5 py-0.5 rounded-md backdrop-blur-sm">Loyalty Campaign Engine</span>
                    </li>
                    <li class="flex items-center text-sm font-bold">
                        <flux:icon.check class="flex-shrink-0 h-5 w-5 mr-3 text-white/70" />
                        <span class="bg-white/15 px-2.5 py-0.5 rounded-md backdrop-blur-sm">Customer Feedback Module</span>
                    </li>
                </ul>
            </div>
            <div class="p-8 pt-0 relative">
                <flux:button wire:click="subscribe('monthly')" class="w-full py-3.5 h-auto rounded-2xl font-black bg-white hover:bg-gray-100 text-[#0d9488] shadow-lg hover:shadow-xl transition-all border-0">
                    Upgrade to Monthly
                </flux:button>
            </div>
        </div>
    </div>
</div>