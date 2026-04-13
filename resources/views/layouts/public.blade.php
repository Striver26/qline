<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
<head>
    @include('partials.head')
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #f8fafc;
            color: #334155;
        }
        .public-bg {
            min-height: 100vh;
        }
        .brand-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
        }
        .btn-teal {
            background-color: #14B8A6;
            color: white;
            transition: all 0.2s;
        }
        .btn-teal:hover {
            background-color: #0d9488;
            transform: translateY(-1px);
        }
        .btn-teal:active {
            transform: translateY(0);
        }
        .qline-logo {
            font-weight: 800;
            font-size: 1.25rem;
            color: #0f172a;
            text-decoration: none;
            letter-spacing: -0.02em;
        }
        .qline-logo em {
            font-style: normal;
            color: #14B8A6;
        }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="public-bg antialiased">

    {{-- Header --}}
    <header class="bg-white border-b border-slate-200">
        <div class="max-w-3xl mx-auto flex items-center justify-between px-5 py-4">
            <span class="qline-logo">Q<em>line</em></span>
            <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Queue System</span>
        </div>
    </header>

    {{-- Main content --}}
    <main class="pt-8 pb-12 px-4">
        {{ $slot }}
    </main>

    {{-- Footer --}}
    <footer class="text-center text-xs text-slate-400 py-6 border-t border-slate-100 mt-auto">
        Powered by <span class="qline-logo" style="font-size: 0.8rem;">Q<em>line</em></span>
    </footer>

    @fluxScripts
</body>
</html>
