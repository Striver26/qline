<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>{{ $title ?? 'Admin Dashboard - QLine' }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-slate-900 font-sans antialiased text-slate-100">
        <nav class="bg-slate-800 border-b border-slate-700 px-4 py-2">
           <div class="font-bold text-xl">QLine Platform Admin</div>
        </nav>
        <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            {{ $slot }}
        </main>
    </body>
</html>
