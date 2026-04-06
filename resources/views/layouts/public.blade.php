<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    @include('partials.head')
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@800&display=swap" rel="stylesheet">
    <style>
        .public-bg {
            background: linear-gradient(135deg, #0f172a 0%, #0c1220 50%, #0a1628 100%);
            min-height: 100vh;
        }
        .glow-teal {
            box-shadow: 0 0 60px rgba(20, 184, 166, 0.15), 0 0 120px rgba(20, 184, 166, 0.05);
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.06);
            backdrop-filter: blur(20px);
        }
        .pulse-dot {
            animation: pulse-glow 2s ease-in-out infinite;
        }
        @keyframes pulse-glow {
            0%, 100% { opacity: 1; box-shadow: 0 0 0 0 rgba(20, 184, 166, 0.4); }
            50% { opacity: 0.8; box-shadow: 0 0 0 8px rgba(20, 184, 166, 0); }
        }
        .qline-logo {
            font-family: 'Syne', sans-serif;
            font-weight: 800;
            font-size: 1.2rem;
            color: #ededea;
            text-decoration: none;
            letter-spacing: -0.02em;
        }
        .qline-logo em {
            font-style: normal;
            color: #14B8A6;
        }
    </style>
</head>
<body class="public-bg text-slate-200 antialiased">

    {{-- Minimal header --}}
    <header class="fixed top-0 w-full z-50 backdrop-blur-md bg-slate-950/60 border-b border-white/5">
        <div class="max-w-3xl mx-auto flex items-center justify-between px-5 py-3">
            <span class="qline-logo">Q<em>line</em></span>
            <span class="text-[10px] font-bold uppercase tracking-widest text-slate-500">Queue System</span>
        </div>
    </header>

    {{-- Main content --}}
    <main class="pt-20 pb-12 px-4">
        {{ $slot }}
    </main>

    {{-- Footer --}}
    <footer class="text-center text-xs text-slate-600 py-6">
        Powered by <span class="qline-logo" style="font-size: 0.8rem;">Q<em>line</em></span>
    </footer>

    @fluxScripts
</body>
</html>
