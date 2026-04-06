<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ __('Welcome') }} - {{ config('app.name', 'Laravel') }}</title>

    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
</head>

<body class="bg-slate-950 text-slate-200 antialiased">

    <!-- NAV -->
    <header class="fixed top-0 w-full z-50 backdrop-blur bg-slate-950/70 border-b border-white/5">
        <div class="max-w-6xl mx-auto flex items-center justify-between px-6 py-4">
            <x-app-logo />

            <nav class="hidden md:flex items-center gap-8 text-sm text-slate-400">
                <a href="#features" class="hover:text-white transition">Features</a>
                <a href="#benefits" class="hover:text-white transition">Benefits</a>
                <a href="#pricing" class="hover:text-white transition">Pricing</a>

            </nav>

            <a href="{{ route('register') }}"
                class="bg-teal-400 text-black text-sm font-semibold px-5 py-2 rounded-full hover:opacity-90 transition">
                Get Started
            </a>
        </div>
    </header>

    <!-- HERO -->
    <section class="relative pt-32 pb-24 px-6 text-center">

        <!-- Glow -->
        <div class="absolute inset-0 flex justify-center">
            <div class="w-[700px] h-[400px] bg-teal-500/10 blur-[120px] rounded-full"></div>
        </div>

        <div class="relative max-w-4xl mx-auto">

            <h1 class="text-5xl md:text-6xl font-bold tracking-tight leading-tight">
                Your Queue System <br>
                <span class="text-teal-400">Runs Itself</span>
            </h1>

            <p class="mt-6 text-lg text-slate-400 max-w-xl mx-auto">
                Let customers join, wait, and get called automatically.
                No shouting. No confusion. No lost customers.
            </p>

            <div class="mt-8 flex justify-center gap-4 flex-wrap">
                <a href="{{ route('register') }}"
                    class="px-8 py-3 bg-teal-400 text-black rounded-full font-semibold shadow-lg hover:scale-105 transition">
                    Start Free Trial
                </a>

                <a href="#" class="px-8 py-3 border border-white/10 rounded-full hover:bg-white/5 transition">
                    Watch Demo
                </a>
            </div>

            <!-- TRUST -->
            <div class="mt-12 text-sm text-slate-500">
                Trusted by clinics, salons & service businesses
            </div>

        </div>
    </section>

    <!-- PROBLEM -->
    <section class="py-20 px-6 text-center">
        <div class="max-w-3xl mx-auto">

            <h2 class="text-3xl font-semibold mb-6">
                Still managing queues like this?
            </h2>

            <div class="space-y-3 text-slate-400">
                <p>❌ Customers crowd your counter</p>
                <p>❌ Staff shouting numbers manually</p>
                <p>❌ People leave due to confusion</p>
            </div>

            <p class="mt-6 text-teal-400 font-semibold">
                Qline fixes all of this instantly.
            </p>
        </div>
    </section>

    <!-- FEATURES -->
    <section class="py-20 px-6" id="features">
        <div class="max-w-6xl mx-auto grid md:grid-cols-3 gap-6">

            <div class="p-6 rounded-2xl bg-white/5 border border-white/5 hover:border-teal-400/30 transition">
                <h3 class="font-semibold text-lg mb-2">Auto Queue Flow</h3>
                <p class="text-slate-400 text-sm">
                    Customers move through your queue automatically.
                </p>
            </div>

            <div class="p-6 rounded-2xl bg-white/5 border border-white/5 hover:border-teal-400/30 transition">
                <h3 class="font-semibold text-lg mb-2">WhatsApp Notifications</h3>
                <p class="text-slate-400 text-sm">
                    Notify customers instantly when it's their turn.
                </p>
            </div>

            <div class="p-6 rounded-2xl bg-white/5 border border-white/5 hover:border-teal-400/30 transition">
                <h3 class="font-semibold text-lg mb-2">QR Walk-In</h3>
                <p class="text-slate-400 text-sm">
                    No phone number required. Fast and simple.
                </p>
            </div>

        </div>
    </section>

    <!-- BENEFITS -->
    <section class="py-20 px-6 text-center bg-slate-900/40 border-y border-white/5" id="benefits">
        <div class="max-w-4xl mx-auto">

            <h2 class="text-3xl font-semibold mb-10">
                Why businesses choose Qline
            </h2>

            <div class="grid md:grid-cols-2 gap-6 text-slate-400 text-sm">
                <div>✔ No app required</div>
                <div>✔ Works with WhatsApp</div>
                <div>✔ Setup in minutes</div>
                <div>✔ Reduce waiting frustration</div>
            </div>

        </div>
    </section>

    <!-- PRICING -->
    <section class="py-24 px-6 text-center">
        <div class="max-w-5xl mx-auto">

            <h2 class="text-3xl font-semibold mb-12" id="pricing">
                Simple pricing
            </h2>

            <div class="grid md:grid-cols-2 gap-8">

                <!-- PLAN 1 -->
                <div class="p-8 rounded-2xl border border-white/10 bg-white/5">
                    <h3 class="text-lg font-semibold mb-2">Starter</h3>

                    <p class="text-slate-400 text-sm mb-6">
                        Perfect for small businesses
                    </p>

                    <div class="text-4xl font-bold text-teal-400 mb-6">
                        RM19 <span class="text-sm text-slate-500">/day</span>
                    </div>

                    <ul class="space-y-2 text-sm text-slate-400 mb-6">
                        <li>✔ Unlimited queue</li>
                        <li>✔ WhatsApp integration</li>
                        <li>✔ QR system</li>
                    </ul>

                    <a href="{{ route('register') }}"
                        class="block py-3 rounded-full border border-white/10 hover:bg-white/5">
                        Start Free Trial
                    </a>
                </div>

                <!-- PLAN 2 -->
                <div class="p-8 rounded-2xl border border-teal-400 bg-teal-400/5 relative">

                    <div
                        class="absolute -top-3 left-1/2 -translate-x-1/2 text-xs bg-teal-400 text-black px-3 py-1 rounded-full">
                        MOST POPULAR
                    </div>

                    <h3 class="text-lg font-semibold mb-2">Pro</h3>

                    <p class="text-slate-400 text-sm mb-6">
                        For growing businesses
                    </p>

                    <div class="text-4xl font-bold text-teal-400 mb-6">
                        RM400 <span class="text-sm text-slate-500">/month</span>
                    </div>

                    <ul class="space-y-2 text-sm text-slate-400 mb-6">
                        <li>✔ Everything in Starter</li>
                        <li>✔ Customer feedback</li>
                        <li>✔ Loyalty rewards</li>
                    </ul>

                    <a href="{{ route('register') }}"
                        class="block py-3 rounded-full bg-teal-400 text-black font-semibold hover:opacity-90">
                        Start Free Trial
                    </a>
                </div>

            </div>

        </div>
    </section>
    <section class="py-24 px-6 text-center">
        <h2 class="text-3xl font-bold mb-4">
            Stop managing crowds.
        </h2>

        <p class="text-slate-500 mb-6">
            Start managing queues the smart way.
        </p>

        <a href="{{ route('register') }}" class="px-8 py-3 bg-teal-400 text-black rounded-full">
            Start Now
        </a>
    </section>

    <!-- FOOTER -->
    <footer class="py-10 text-center text-sm text-slate-400">
        © 2026 Qline · Built in Malaysia 🇲🇾
    </footer>

</body>

</html>