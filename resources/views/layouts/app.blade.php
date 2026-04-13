<x-layouts::app.sidebar :title="$title ?? null">
    <flux:main class="relative">
        <div class="page-shell">
            {{ $slot }}
        </div>
    </flux:main>
</x-layouts::app.sidebar>
