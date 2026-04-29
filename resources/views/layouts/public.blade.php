<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.head')
</head>

<body>
    <main class="relative z-10 px-4 py-8 sm:px-6 sm:py-10">
        <div class="public-panel">
            {{ $slot }}
        </div>
    </main>

    @fluxScripts
</body>

</html>