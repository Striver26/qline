@props([
    'sidebar' => false,
])

@php
    $logoHtml = '<span class="text-xl font-black tracking-tight text-gray-900 dark:text-white">Q<span style="color: #14B8A6;">Line</span></span>';
    $iconHtml = '<div class="flex items-center justify-center w-8 h-8 rounded-lg shadow-sm" style="background: linear-gradient(135deg, #14B8A6, #0d9488);">
        <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
    </div>';
@endphp

@if($sidebar)
    <flux:sidebar.brand name="QLine" {{ $attributes }}>
        <x-slot name="logo" class="flex aspect-square size-8 items-center justify-center rounded-md">
            {!! $iconHtml !!}
        </x-slot>
    </flux:sidebar.brand>
@else
    <flux:brand name="QLine" {{ $attributes }}>
        <x-slot name="logo" class="flex aspect-square size-8 items-center justify-center rounded-md">
            {!! $iconHtml !!}
        </x-slot>
    </flux:brand>
@endif
