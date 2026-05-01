<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    @php($title = __('welcome.title') ?? 'Qline - Modern Queue Management')
    @include('partials.head')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #060913; /* Even darker, almost pure black navy */
        }
        
        body {
            font-family: 'Inter', ui-sans-serif, system-ui, sans-serif;
            background-color: var(--bg-color);
            color: #e2e8f0;
        }

        /* Subtle premium grain overlay */
        .bg-noise {
            position: fixed;
            top: 0; left: 0; width: 100vw; height: 100vh;
            pointer-events: none;
            z-index: 50;
            opacity: 0.025;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.75' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)'/%3E%3C/svg%3E");
        }

        /* Ambient glowing orbs */
        .glow-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(120px);
            pointer-events: none;
            z-index: 0;
            opacity: 0.5;
        }

        .hero-text-gradient {
            background: linear-gradient(180deg, #FFFFFF 0%, rgba(255, 255, 255, 0.6) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .accent-text-gradient {
            background: linear-gradient(135deg, #2dd4bf 0%, #059669 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .premium-border {
            position: relative;
            background: rgba(15, 23, 42, 0.4);
        }
        .premium-border::before {
            content: "";
            position: absolute;
            inset: 0;
            border-radius: inherit;
            padding: 1px;
            background: linear-gradient(to bottom right, rgba(255,255,255,0.15), rgba(255,255,255,0.02));
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            pointer-events: none;
        }

        .bento-card {
            background: radial-gradient(120% 120% at 50% -20%, rgba(255,255,255,0.03) 0%, rgba(255,255,255,0) 100%), rgba(15, 23, 42, 0.3);
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .bento-card:hover {
            transform: translateY(-2px);
            background: radial-gradient(120% 120% at 50% -20%, rgba(255,255,255,0.06) 0%, rgba(255,255,255,0) 100%), rgba(15, 23, 42, 0.5);
            box-shadow: 0 20px 40px -15px rgba(0,0,0,0.5), 0 0 40px -10px rgba(45, 212, 191, 0.1);
        }

        .btn-glow {
            box-shadow: 0 0 0 1px rgba(255,255,255,0.1), 0 2px 4px rgba(0,0,0,0.2), inset 0 1px 0 rgba(255,255,255,0.1);
            transition: all 0.2s ease;
        }
        .btn-glow:hover {
            box-shadow: 0 0 0 1px rgba(255,255,255,0.2), 0 4px 12px rgba(0,0,0,0.3), inset 0 1px 0 rgba(255,255,255,0.2);
        }
        
        .mockup-container {
            transform: perspective(1200px) rotateX(4deg) rotateY(-8deg) rotateZ(1deg);
            transform-style: preserve-3d;
            transition: transform 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .mockup-container:hover {
            transform: perspective(1200px) rotateX(2deg) rotateY(-4deg) rotateZ(0deg);
        }

        /* Divider line */
        .divider-gradient {
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
        }
    </style>
</head>

<body class="antialiased selection:bg-teal-500/30 selection:text-teal-200">

    <div class="bg-noise"></div>

    {{-- Background Ambient Lights --}}
    <div class="fixed inset-0 z-0 pointer-events-none overflow-hidden">
        <div class="glow-orb w-[600px] h-[600px] bg-teal-500/10 top-[-200px] left-[-100px]"></div>
        <div class="glow-orb w-[800px] h-[800px] bg-emerald-500/5 top-[10%] right-[-200px]"></div>
        <div class="glow-orb w-[500px] h-[500px] bg-blue-500/10 bottom-[-100px] left-[20%]"></div>
    </div>

    {{-- Navigation --}}
    <header class="fixed top-0 left-0 right-0 z-50 w-full border-b border-white/[0.04] bg-[#060913]/70 backdrop-blur-2xl">
        <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-6">
            <div class="flex items-center gap-2">
                <x-app-logo href="{{ route('home') }}" />
                <span class="rounded bg-white/5 px-2 py-0.5 text-[0.65rem] font-semibold text-slate-400 border border-white/10 hidden sm:block">Beta</span>
            </div>

            <nav class="hidden md:flex items-center gap-8 text-[13px] font-medium text-slate-400">
                <a href="#features" class="transition-colors hover:text-white">Features</a>
                <a href="#workflow" class="transition-colors hover:text-white">Workflow</a>
                <a href="#pricing" class="transition-colors hover:text-white">Pricing</a>
                <div class="group relative cursor-pointer py-2">
                    <span class="flex items-center gap-1 transition-colors hover:text-white">Resources <flux:icon.chevron-down class="h-3 w-3 opacity-50" /></span>
                </div>
            </nav>

            <div class="flex items-center gap-5">
                <div class="hidden items-center rounded-full bg-white/[0.03] p-1 sm:flex border border-white/[0.05]">
                    <a href="{{ route('lang.switch', ['locale' => 'en']) }}"
                        class="rounded-full px-2.5 py-1 text-[11px] font-semibold transition {{ app()->getLocale() === 'en' ? 'bg-white/10 text-white shadow-sm' : 'text-slate-500 hover:text-white' }}">EN</a>
                    <a href="{{ route('lang.switch', ['locale' => 'ms']) }}"
                        class="rounded-full px-2.5 py-1 text-[11px] font-semibold transition {{ app()->getLocale() === 'ms' ? 'bg-white/10 text-white shadow-sm' : 'text-slate-500 hover:text-white' }}">BM</a>
                </div>
                <a href="{{ route('login') }}" class="text-[13px] font-medium text-slate-400 transition hover:text-white hidden sm:block">
                    Log in
                </a>
                <a href="{{ route('register') }}"
                    class="group relative inline-flex h-8 items-center justify-center rounded-full bg-white px-4 text-[13px] font-semibold text-slate-950 transition-all hover:bg-slate-200">
                    Get Started Free
                </a>
            </div>
        </div>
    </header>

    <main class="relative z-10 pt-16">

        {{-- Hero Section --}}
        <section class="relative mx-auto max-w-7xl px-6 pb-32 pt-28 lg:pt-40 overflow-visible">
            <div class="grid items-center gap-16 lg:grid-cols-[1.2fr_0.8fr]">
                {{-- Left Content --}}
                <div class="relative z-20 space-y-8">
                    <a href="#workflow" class="inline-flex items-center gap-2 rounded-full border border-teal-500/20 bg-teal-500/5 px-3 py-1 text-[11px] font-semibold uppercase tracking-widest text-teal-300 backdrop-blur-sm transition hover:bg-teal-500/10">
                        <span class="flex h-2 w-2 rounded-full bg-teal-400 shadow-[0_0_8px_rgba(45,212,191,1)]"></span>
                        Introducing Qline 2.0
                    </a>
                    
                    <h1 class="text-6xl md:text-7xl lg:text-[5.5rem] font-semibold tracking-tighter leading-[1.05]">
                        <span class="hero-text-gradient">Calmer queues.<br>Happier people.</span><br>
                        <span class="accent-text-gradient">Better service.</span>
                    </h1>
                    
                    <p class="max-w-xl text-[17px] text-slate-400/90 leading-relaxed font-medium">
                        Turn chaotic waiting lines into smooth, trackable, and stress-free experiences. The operating system for modern service fronts.
                    </p>
                    
                    <div class="flex flex-wrap items-center gap-4 pt-4">
                        <a href="{{ route('register') }}"
                            class="group relative inline-flex h-12 items-center justify-center rounded-full bg-teal-500 px-8 text-[15px] font-semibold text-teal-950 transition-all hover:scale-105 active:scale-95 shadow-[0_0_30px_rgba(45,212,191,0.2)]">
                            <div class="absolute -inset-0.5 -z-10 rounded-full bg-teal-400 opacity-0 blur-md transition group-hover:opacity-60"></div>
                            Start Free Trial
                        </a>
                        <a href="#workflow"
                            class="btn-glow inline-flex h-12 items-center justify-center rounded-full bg-white/5 px-8 text-[15px] font-semibold text-white backdrop-blur-md transition-all hover:bg-white/10">
                            Book a Demo
                        </a>
                    </div>
                    
                    <div class="pt-8 flex flex-col gap-5">
                        <div class="flex items-center gap-4">
                            <div class="flex -space-x-2">
                                <img class="h-8 w-8 rounded-full border-2 border-[#060913] object-cover" src="https://i.pravatar.cc/100?img=1" alt="User">
                                <img class="h-8 w-8 rounded-full border-2 border-[#060913] object-cover" src="https://i.pravatar.cc/100?img=2" alt="User">
                                <img class="h-8 w-8 rounded-full border-2 border-[#060913] object-cover" src="https://i.pravatar.cc/100?img=3" alt="User">
                                <img class="h-8 w-8 rounded-full border-2 border-[#060913] object-cover" src="https://i.pravatar.cc/100?img=4" alt="User">
                            </div>
                            <div class="flex flex-col justify-center">
                                <div class="flex text-amber-400/90 gap-0.5">
                                    <flux:icon.star class="h-3.5 w-3.5 fill-current" />
                                    <flux:icon.star class="h-3.5 w-3.5 fill-current" />
                                    <flux:icon.star class="h-3.5 w-3.5 fill-current" />
                                    <flux:icon.star class="h-3.5 w-3.5 fill-current" />
                                    <flux:icon.star class="h-3.5 w-3.5 fill-current" />
                                </div>
                                <span class="text-[13px] font-medium text-slate-400 mt-0.5">Trusted by 1,000+ modern teams</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right 3D Mockup --}}
                <div class="relative z-10 hidden lg:block">
                    <div class="mockup-container w-full max-w-[600px] ml-auto">
                        <div class="absolute -inset-4 bg-teal-500/20 blur-[80px] rounded-full"></div>
                        <div class="premium-border rounded-[24px] p-2 shadow-2xl backdrop-blur-xl bg-slate-900/40">
                            <div class="rounded-[18px] bg-[#0A0F1C] border border-white/5 overflow-hidden shadow-inner">
                                {{-- Fake Browser/App Header --}}
                                <div class="flex items-center px-4 py-3 border-b border-white/5 bg-white/[0.01]">
                                    <div class="flex gap-1.5">
                                        <div class="w-2.5 h-2.5 rounded-full bg-slate-700"></div>
                                        <div class="w-2.5 h-2.5 rounded-full bg-slate-700"></div>
                                        <div class="w-2.5 h-2.5 rounded-full bg-slate-700"></div>
                                    </div>
                                    <div class="mx-auto flex items-center gap-2 rounded-md bg-white/5 px-2 py-0.5 border border-white/5">
                                        <flux:icon.lock-closed class="h-3 w-3 text-slate-500" />
                                        <span class="text-[10px] font-medium text-slate-400">qline.app/dashboard</span>
                                    </div>
                                    <div class="w-10"></div>
                                </div>
                                
                                {{-- Mockup App Content --}}
                                <div class="p-6">
                                    <div class="flex items-center justify-between mb-8">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-teal-500/20 text-teal-400 border border-teal-500/20">
                                                <flux:icon.queue-list class="h-4 w-4" />
                                            </div>
                                            <div>
                                                <h3 class="text-sm font-semibold text-white leading-tight">Live Dashboard</h3>
                                                <p class="text-[10px] text-slate-500">Main Branch</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2 rounded-full border border-teal-500/20 bg-teal-500/10 px-2.5 py-1">
                                            <span class="h-1.5 w-1.5 rounded-full bg-teal-400 animate-pulse"></span>
                                            <span class="text-[10px] font-bold text-teal-400 uppercase tracking-widest">Active</span>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-4 mb-4">
                                        <div class="premium-border rounded-xl p-4 bg-slate-900/50">
                                            <p class="text-[10px] font-semibold uppercase tracking-widest text-slate-500 mb-1">Now Serving</p>
                                            <p class="text-4xl font-black text-teal-400 drop-shadow-[0_0_15px_rgba(45,212,191,0.2)]">A042</p>
                                        </div>
                                        <div class="premium-border rounded-xl p-4 bg-slate-900/50 flex flex-col justify-between">
                                            <p class="text-[10px] font-semibold uppercase tracking-widest text-slate-500 mb-1">Waiting</p>
                                            <div class="flex items-end justify-between">
                                                <p class="text-4xl font-black text-white">18</p>
                                                <div class="flex gap-0.5 items-end h-6 opacity-60">
                                                    <div class="w-1.5 bg-slate-600 rounded-t-sm h-[40%]"></div>
                                                    <div class="w-1.5 bg-slate-600 rounded-t-sm h-[60%]"></div>
                                                    <div class="w-1.5 bg-slate-600 rounded-t-sm h-[50%]"></div>
                                                    <div class="w-1.5 bg-teal-500 rounded-t-sm h-[90%] shadow-[0_0_5px_rgba(45,212,191,0.5)]"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="premium-border rounded-xl p-4 bg-slate-900/50 mb-2">
                                        <p class="text-[10px] font-semibold uppercase tracking-widest text-slate-500 mb-3">Up Next in Queue</p>
                                        <div class="flex gap-2">
                                            <div class="flex-1 bg-white/5 rounded-lg py-2 text-center border border-white/5 text-sm font-bold text-slate-300">A043</div>
                                            <div class="flex-1 bg-white/5 rounded-lg py-2 text-center border border-white/5 text-sm font-bold text-slate-400">A044</div>
                                            <div class="flex-1 bg-white/5 rounded-lg py-2 text-center border border-white/5 text-sm font-bold text-slate-500">A045</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="divider-gradient w-full max-w-7xl mx-auto"></div>

        {{-- Social Proof --}}
        <section class="mx-auto max-w-7xl px-6 py-16 text-center">
            <p class="text-[11px] font-bold uppercase tracking-widest text-slate-500 mb-10">Trusted by forward-thinking teams</p>
            <div class="flex flex-wrap justify-center items-center gap-10 sm:gap-16 opacity-40 grayscale hover:grayscale-0 hover:opacity-100 transition-all duration-700">
                <div class="text-xl font-black tracking-tighter text-white flex items-center gap-1.5">
                    <div class="w-6 h-6 rounded-md bg-white flex items-center justify-center text-slate-900"><span class="block -mt-0.5 text-sm">M</span></div> Maybank
                </div>
                <div class="text-lg font-bold text-white flex items-center gap-1.5">
                    <flux:icon.plus-circle class="w-6 h-6" /> KLINIK PERMATA
                </div>
                <div class="text-xl font-black italic text-white flex items-center gap-1">
                    <span class="text-red-500">POS</span> Laju
                </div>
                <div class="text-2xl font-black tracking-tight text-white">
                    Giant
                </div>
                <div class="text-lg font-bold text-white flex items-center gap-1.5">
                    <flux:icon.bolt class="w-5 h-5" /> ZUS <span class="font-light">COFFEE</span>
                </div>
            </div>
        </section>

        <div class="divider-gradient w-full max-w-7xl mx-auto"></div>

        {{-- Features Bento Grid --}}
        <section id="features" class="mx-auto max-w-7xl px-6 py-32 relative">
            <div class="absolute top-[20%] left-[10%] w-[400px] h-[400px] bg-teal-500/5 rounded-full blur-[100px] pointer-events-none"></div>

            <div class="mb-20 text-center max-w-2xl mx-auto">
                <span class="inline-flex items-center gap-2 rounded-full border border-white/5 bg-white/[0.02] px-3 py-1 text-[11px] font-semibold uppercase tracking-widest text-slate-400 mb-4">
                    Built for scale
                </span>
                <h2 class="text-4xl md:text-5xl font-bold tracking-tight hero-text-gradient">
                    Everything you need to manage queues without the chaos.
                </h2>
            </div>

            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3 auto-rows-[minmax(240px,auto)]">
                
                {{-- Feature 1: Large Card --}}
                <div class="premium-border bento-card rounded-3xl p-8 lg:col-span-2 relative overflow-hidden group">
                    <div class="absolute -right-10 -top-10 h-48 w-48 bg-teal-500/10 blur-[50px] rounded-full group-hover:bg-teal-500/20 transition-colors"></div>
                    <div class="relative z-10 flex flex-col h-full">
                        <div class="mb-auto inline-flex h-10 w-10 items-center justify-center rounded-xl bg-teal-500/10 text-teal-400 border border-teal-500/20 shadow-inner">
                            <flux:icon.adjustments-horizontal class="h-5 w-5" />
                        </div>
                        <div class="mt-8">
                            <h3 class="text-xl font-bold text-white mb-2">Smart Queue Management</h3>
                            <p class="text-sm text-slate-400 leading-relaxed max-w-md">Organize queues, assign tickets to specific counters, and manage the flow across your entire floor plan from a single pane of glass.</p>
                        </div>
                    </div>
                </div>

                {{-- Feature 2 --}}
                <div class="premium-border bento-card rounded-3xl p-8 relative overflow-hidden group">
                    <div class="absolute -right-10 -bottom-10 h-32 w-32 bg-blue-500/10 blur-[40px] rounded-full group-hover:bg-blue-500/20 transition-colors"></div>
                    <div class="relative z-10 flex flex-col h-full">
                        <div class="mb-auto inline-flex h-10 w-10 items-center justify-center rounded-xl bg-blue-500/10 text-blue-400 border border-blue-500/20 shadow-inner">
                            <flux:icon.device-phone-mobile class="h-5 w-5" />
                        </div>
                        <div class="mt-8">
                            <h3 class="text-lg font-bold text-white mb-2">Real-time Updates</h3>
                            <p class="text-sm text-slate-400 leading-relaxed">Customers track their exact position via live URLs or WhatsApp notifications.</p>
                        </div>
                    </div>
                </div>

                {{-- Feature 3 --}}
                <div class="premium-border bento-card rounded-3xl p-8 relative overflow-hidden group">
                    <div class="relative z-10 flex flex-col h-full">
                        <div class="mb-auto inline-flex h-10 w-10 items-center justify-center rounded-xl bg-purple-500/10 text-purple-400 border border-purple-500/20 shadow-inner">
                            <flux:icon.bolt class="h-5 w-5" />
                        </div>
                        <div class="mt-8">
                            <h3 class="text-lg font-bold text-white mb-2">Workflow Automation</h3>
                            <p class="text-sm text-slate-400 leading-relaxed">Automate repetitive tasks and let the system handle routing intelligently.</p>
                        </div>
                    </div>
                </div>

                {{-- Feature 4 --}}
                <div class="premium-border bento-card rounded-3xl p-8 relative overflow-hidden group">
                    <div class="relative z-10 flex flex-col h-full">
                        <div class="mb-auto inline-flex h-10 w-10 items-center justify-center rounded-xl bg-amber-500/10 text-amber-400 border border-amber-500/20 shadow-inner">
                            <flux:icon.chart-bar class="h-5 w-5" />
                        </div>
                        <div class="mt-8">
                            <h3 class="text-lg font-bold text-white mb-2">Deep Analytics</h3>
                            <p class="text-sm text-slate-400 leading-relaxed">Track staff performance and wait times with beautiful, actionable reports.</p>
                        </div>
                    </div>
                </div>

                {{-- Feature 5 --}}
                <div class="premium-border bento-card rounded-3xl p-8 relative overflow-hidden group">
                    <div class="relative z-10 flex flex-col h-full">
                        <div class="mb-auto inline-flex h-10 w-10 items-center justify-center rounded-xl bg-rose-500/10 text-rose-400 border border-rose-500/20 shadow-inner">
                            <flux:icon.sparkles class="h-5 w-5" />
                        </div>
                        <div class="mt-8">
                            <h3 class="text-lg font-bold text-white mb-2">Incredibly Simple</h3>
                            <p class="text-sm text-slate-400 leading-relaxed">Designed to be completely obvious for both your staff and customers.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Value Section --}}
        <section class="mx-auto max-w-7xl px-6 py-10">
            <div class="premium-border rounded-[2.5rem] p-12 lg:p-20 text-center relative overflow-hidden bg-slate-900/30">
                <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-[0.03] mix-blend-overlay"></div>
                <h2 class="relative z-10 text-3xl md:text-4xl font-bold tracking-tight text-white mb-6">
                    Designed to be obvious for everyone.
                </h2>
                <p class="relative z-10 text-slate-400 max-w-2xl mx-auto text-lg">
                    No training manuals required. Qline is built so your staff intuitively know what to click, and your customers instantly know where to wait. We eliminated the friction.
                </p>
            </div>
        </section>

        {{-- How It Works --}}
        <section id="workflow" class="mx-auto max-w-7xl px-6 py-32">
            <div class="mb-16 text-center">
                <span class="text-[11px] font-bold uppercase tracking-widest text-slate-500 mb-4 block">How It Works</span>
                <h2 class="text-4xl font-bold tracking-tight text-white">
                    Three steps to smoother handoffs
                </h2>
            </div>

            <div class="grid gap-8 md:grid-cols-3 relative mt-20">
                {{-- Line connecting --}}
                <div class="hidden md:block absolute top-6 left-[16%] right-[16%] h-[1px] bg-gradient-to-r from-transparent via-slate-700 to-transparent"></div>

                {{-- Step 1 --}}
                <div class="relative text-center group">
                    <div class="mx-auto mb-8 flex h-12 w-12 items-center justify-center rounded-full bg-[#060913] border border-white/10 text-lg font-bold text-slate-300 relative z-10 shadow-xl transition-all group-hover:border-teal-500/50 group-hover:text-teal-400 group-hover:shadow-[0_0_20px_rgba(45,212,191,0.2)]">
                        1
                    </div>
                    <h3 class="text-lg font-bold text-white mb-3">Take a Number</h3>
                    <p class="text-[13px] text-slate-400 max-w-xs mx-auto leading-relaxed">Customers scan a QR code or message via WhatsApp to instantly join the queue from anywhere.</p>
                </div>

                {{-- Step 2 --}}
                <div class="relative text-center group">
                    <div class="mx-auto mb-8 flex h-12 w-12 items-center justify-center rounded-full bg-[#060913] border border-white/10 text-lg font-bold text-slate-300 relative z-10 shadow-xl transition-all group-hover:border-teal-500/50 group-hover:text-teal-400 group-hover:shadow-[0_0_20px_rgba(45,212,191,0.2)]">
                        2
                    </div>
                    <h3 class="text-lg font-bold text-white mb-3">Qline Manages</h3>
                    <p class="text-[13px] text-slate-400 max-w-xs mx-auto leading-relaxed">We automatically update their live position and send notifications so they never miss their turn.</p>
                </div>

                {{-- Step 3 --}}
                <div class="relative text-center group">
                    <div class="mx-auto mb-8 flex h-12 w-12 items-center justify-center rounded-full bg-[#060913] border border-white/10 text-lg font-bold text-slate-300 relative z-10 shadow-xl transition-all group-hover:border-teal-500/50 group-hover:text-teal-400 group-hover:shadow-[0_0_20px_rgba(45,212,191,0.2)]">
                        3
                    </div>
                    <h3 class="text-lg font-bold text-white mb-3">Serve Smoothly</h3>
                    <p class="text-[13px] text-slate-400 max-w-xs mx-auto leading-relaxed">Your staff simply clicks "Call Next". The TV display chimes, and the workflow continues seamlessly.</p>
                </div>
            </div>
        </section>

        {{-- Pricing --}}
        <section id="pricing" class="mx-auto max-w-7xl px-6 py-32 relative">
            <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-teal-500/5 rounded-full blur-[120px] pointer-events-none"></div>

            <div class="mb-20 text-center">
                <h2 class="text-4xl md:text-5xl font-bold tracking-tight hero-text-gradient">
                    Pick the plan that fits your business
                </h2>
                <div class="mt-8 flex items-center justify-center gap-2">
                    <span class="text-sm font-medium text-white">Monthly</span>
                    <button class="relative inline-flex h-5 w-10 items-center rounded-full bg-teal-500 transition-colors">
                        <span class="inline-block h-3 w-3 translate-x-1 rounded-full bg-teal-950 transition-transform"></span>
                    </button>
                    <span class="text-sm font-medium text-slate-500">Yearly <span class="text-teal-400 text-xs ml-1 font-bold">Save 20%</span></span>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-3 max-w-6xl mx-auto items-center">
                {{-- Starter --}}
                <div class="premium-border rounded-[2rem] p-8 bg-slate-900/30 lg:translate-y-4">
                    <h3 class="text-lg font-bold text-white mb-2">Starter</h3>
                    <p class="text-[13px] text-slate-400 mb-6 h-10">Essential features for small teams.</p>
                    <div class="flex items-baseline gap-1 mb-8">
                        <span class="text-sm text-slate-500">RM</span>
                        <span class="text-4xl font-bold text-white tracking-tight">15</span>
                        <span class="text-sm text-slate-500">/mo</span>
                    </div>
                    <a href="{{ route('register') }}" class="btn-glow flex w-full justify-center rounded-xl bg-white/5 py-2.5 text-sm font-semibold text-white transition-all hover:bg-white/10 mb-8">
                        Start Free Trial
                    </a>
                    <ul class="space-y-4 text-[13px] text-slate-300">
                        <li class="flex items-start gap-3"><flux:icon.check class="h-4 w-4 text-teal-400 shrink-0" /> Basic queue management</li>
                        <li class="flex items-start gap-3"><flux:icon.check class="h-4 w-4 text-teal-400 shrink-0" /> WhatsApp notifications</li>
                        <li class="flex items-start gap-3"><flux:icon.check class="h-4 w-4 text-teal-400 shrink-0" /> 1 service location</li>
                        <li class="flex items-start gap-3"><flux:icon.check class="h-4 w-4 text-teal-400 shrink-0" /> Standard support</li>
                    </ul>
                </div>

                {{-- Growth (Highlighted) --}}
                <div class="relative premium-border rounded-[2rem] p-10 bg-slate-900/60 border border-teal-500/30 shadow-2xl z-10 transform lg:-translate-y-2">
                    <div class="absolute inset-0 bg-gradient-to-b from-teal-500/10 to-transparent rounded-[2rem] pointer-events-none"></div>
                    <div class="absolute -top-3.5 left-1/2 -translate-x-1/2 rounded-full bg-teal-500/10 border border-teal-500/30 px-3 py-1 text-[10px] font-bold uppercase tracking-widest text-teal-400 backdrop-blur-md shadow-[0_0_15px_rgba(45,212,191,0.2)]">
                        Most Popular
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2 relative z-10">Growth</h3>
                    <p class="text-[13px] text-teal-100/60 mb-6 h-10 relative z-10">Everything you need to scale operations.</p>
                    <div class="flex items-baseline gap-1 mb-8 relative z-10">
                        <span class="text-sm text-teal-400/80">RM</span>
                        <span class="text-5xl font-black text-white tracking-tighter drop-shadow-lg">99</span>
                        <span class="text-sm text-slate-400">/mo</span>
                    </div>
                    <a href="{{ route('register') }}" class="relative z-10 group flex w-full justify-center rounded-xl bg-teal-500 py-3 text-sm font-bold text-teal-950 transition-all hover:bg-teal-400 hover:scale-[1.02] active:scale-[0.98] shadow-[0_0_20px_rgba(45,212,191,0.25)] mb-8">
                        Start Free Trial
                    </a>
                    <ul class="space-y-4 text-[13px] text-white relative z-10 font-medium">
                        <li class="flex items-start gap-3"><flux:icon.check class="h-4 w-4 text-teal-400 shrink-0" /> All Starter features</li>
                        <li class="flex items-start gap-3"><flux:icon.check class="h-4 w-4 text-teal-400 shrink-0" /> Multi-channel (QR + Web + WhatsApp)</li>
                        <li class="flex items-start gap-3"><flux:icon.check class="h-4 w-4 text-teal-400 shrink-0" /> Workflow automation rules</li>
                        <li class="flex items-start gap-3"><flux:icon.check class="h-4 w-4 text-teal-400 shrink-0" /> Advanced analytics dashboard</li>
                        <li class="flex items-start gap-3"><flux:icon.check class="h-4 w-4 text-teal-400 shrink-0" /> Custom branding</li>
                    </ul>
                </div>

                {{-- Scale --}}
                <div class="premium-border rounded-[2rem] p-8 bg-slate-900/30 lg:translate-y-4">
                    <h3 class="text-lg font-bold text-white mb-2">Scale</h3>
                    <p class="text-[13px] text-slate-400 mb-6 h-10">For enterprise organizations.</p>
                    <div class="flex items-baseline gap-1 mb-8">
                        <span class="text-sm text-slate-500">RM</span>
                        <span class="text-4xl font-bold text-white tracking-tight">249</span>
                        <span class="text-sm text-slate-500">/mo</span>
                    </div>
                    <a href="{{ route('contact') }}" class="btn-glow flex w-full justify-center rounded-xl bg-transparent border border-white/10 py-2.5 text-sm font-semibold text-white transition-all hover:bg-white/5 mb-8">
                        Contact Sales
                    </a>
                    <ul class="space-y-4 text-[13px] text-slate-300">
                        <li class="flex items-start gap-3"><flux:icon.check class="h-4 w-4 text-teal-400 shrink-0" /> All Growth features</li>
                        <li class="flex items-start gap-3"><flux:icon.check class="h-4 w-4 text-teal-400 shrink-0" /> Unlimited queues & locations</li>
                        <li class="flex items-start gap-3"><flux:icon.check class="h-4 w-4 text-teal-400 shrink-0" /> Advanced smart routing</li>
                        <li class="flex items-start gap-3"><flux:icon.check class="h-4 w-4 text-teal-400 shrink-0" /> Custom API integrations</li>
                        <li class="flex items-start gap-3"><flux:icon.check class="h-4 w-4 text-teal-400 shrink-0" /> Dedicated priority support</li>
                    </ul>
                </div>
            </div>

            <div class="mt-16 flex flex-wrap justify-center gap-10 text-[12px] font-semibold tracking-wide text-slate-500 uppercase">
                <span class="flex items-center gap-2"><flux:icon.check-badge class="h-4 w-4 text-slate-400" /> 14-day free trial</span>
                <span class="flex items-center gap-2"><flux:icon.credit-card class="h-4 w-4 text-slate-400" /> No credit card required</span>
                <span class="flex items-center gap-2"><flux:icon.arrow-path class="h-4 w-4 text-slate-400" /> Cancel anytime</span>
            </div>
        </section>

        {{-- Final CTA --}}
        <section class="mx-auto max-w-7xl px-6 pb-32">
            <div class="relative overflow-hidden rounded-[3rem] p-16 lg:p-20 text-center premium-border bg-slate-900/40">
                <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full max-w-2xl h-1/2 bg-teal-500/10 blur-[80px] rounded-full pointer-events-none"></div>
                
                <h2 class="relative z-10 text-4xl md:text-5xl font-bold tracking-tight text-white mb-6 leading-tight">
                    Stop managing crowds.<br>Start managing flow.
                </h2>
                <p class="relative z-10 text-slate-400 text-lg max-w-xl mx-auto mb-10">
                    Give your customers a smoother wait and your team a calmer service rhythm with the operating system for modern queues.
                </p>
                <div class="relative z-10">
                    <a href="{{ route('register') }}"
                        class="inline-flex justify-center items-center rounded-full bg-white px-8 py-4 text-[15px] font-bold text-slate-950 transition-all hover:bg-slate-200 hover:scale-105 active:scale-95 shadow-xl">
                        Start Your Free Trial
                    </a>
                </div>
            </div>
        </section>

    </main>

    {{-- Precision Footer --}}
    <footer class="border-t border-white/[0.04] bg-[#060913] pt-24 pb-12 relative z-10">
        <div class="mx-auto max-w-7xl px-6">
            <div class="grid grid-cols-2 gap-x-8 gap-y-16 md:grid-cols-6 lg:gap-12">
                
                {{-- Brand --}}
                <div class="col-span-2 space-y-6">
                    <x-app-logo href="{{ route('home') }}" />
                    <p class="text-[13px] text-slate-500 max-w-xs leading-relaxed">
                        The queue management system designed for modern teams who care about customer experience.
                    </p>
                    <div class="flex gap-4">
                        <a href="#" class="text-slate-600 hover:text-white transition-colors"><flux:icon.building-storefront class="h-4 w-4" /></a>
                        <a href="#" class="text-slate-600 hover:text-white transition-colors"><flux:icon.chat-bubble-bottom-center class="h-4 w-4" /></a>
                        <a href="#" class="text-slate-600 hover:text-white transition-colors"><flux:icon.envelope class="h-4 w-4" /></a>
                    </div>
                </div>

                {{-- Links --}}
                <div>
                    <h3 class="text-[11px] font-bold uppercase tracking-widest text-white mb-6">Product</h3>
                    <ul class="space-y-4 text-[13px] text-slate-500 font-medium">
                        <li><a href="#features" class="hover:text-white transition-colors">Features</a></li>
                        <li><a href="#pricing" class="hover:text-white transition-colors">Pricing</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Integrations</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">API Docs</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-[11px] font-bold uppercase tracking-widest text-white mb-6">Resources</h3>
                    <ul class="space-y-4 text-[13px] text-slate-500 font-medium">
                        <li><a href="#" class="hover:text-white transition-colors">Help Center</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Guides</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Blog</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Webinars</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-[11px] font-bold uppercase tracking-widest text-white mb-6">Company</h3>
                    <ul class="space-y-4 text-[13px] text-slate-500 font-medium">
                        <li><a href="#" class="hover:text-white transition-colors">About Us</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Careers</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Contact</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-[11px] font-bold uppercase tracking-widest text-white mb-6">Legal</h3>
                    <ul class="space-y-4 text-[13px] text-slate-500 font-medium">
                        <li><a href="#" class="hover:text-white transition-colors">Privacy Policy</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Terms of Service</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Security</a></li>
                    </ul>
                </div>
            </div>

            <div class="mt-20 flex flex-col md:flex-row items-center justify-between gap-4 border-t border-white/[0.04] pt-8 text-[12px] text-slate-600 font-medium">
                <p>&copy; {{ date('Y') }} Qline Inc. All rights reserved.</p>
                <div class="flex items-center gap-6">
                    <span class="flex items-center gap-2"><span class="h-1.5 w-1.5 rounded-full bg-teal-500"></span> All systems operational</span>
                    <span>Kuala Lumpur, MY</span>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>