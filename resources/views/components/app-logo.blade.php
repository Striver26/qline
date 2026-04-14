@props([
    'sidebar' => false,
    'inverse' => false,
])

@php
    $tag = $attributes->has('href') ? 'a' : 'div';
@endphp

<{{ $tag }} {{ $attributes->class(['brand-link group']) }}>
    <span class="brand-mark {{ $sidebar ? 'h-10 w-10 text-base' : '' }}">
        Q
    </span>

    <span class="min-w-0">
        <span class="brand-wordmark {{ $sidebar ? 'text-lg' : '' }} {{ $inverse ? '!text-white' : '' }}">Qline</span>
        <span class="brand-meta {{ $inverse ? '!text-white/70' : '' }}">
        </span>
    </span>
</{{ $tag }}>
