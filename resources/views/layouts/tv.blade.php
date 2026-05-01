<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.head')
</head>

<body class="bg-slate-950 font-sans text-white antialiased selection:bg-brand-500 selection:text-white min-h-screen">
    <main class="relative z-10 min-h-screen flex flex-col">
        {{ $slot }}
    </main>

    @fluxScripts
</body>

</html>