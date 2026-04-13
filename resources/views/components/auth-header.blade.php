@props([
    'title',
    'description',
])

<div class="flex w-full flex-col text-center">
    <span class="page-kicker mx-auto mb-3">{{ __('Welcome to Qline') }}</span>
    <h1 class="text-3xl font-bold tracking-[-0.05em] text-slate-950 dark:text-white sm:text-4xl">{{ $title }}</h1>
    <p class="mt-3 text-sm text-slate-500 dark:text-slate-400">{{ $description }}</p>
</div>
