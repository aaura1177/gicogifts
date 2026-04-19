@props([
    'variant' => 'primary',
])

@php
    $base = 'inline-flex min-h-[44px] min-w-[44px] items-center justify-center rounded-lg px-5 py-2.5 text-sm font-medium transition focus:outline-none focus-visible:ring-2 focus-visible:ring-sienna-500 focus-visible:ring-offset-2 focus-visible:ring-offset-ivory-50';
    $variants = [
        'primary' => 'bg-sienna-500 text-white hover:bg-sienna-600',
        'secondary' => 'border-2 border-chocolate-800 bg-transparent text-chocolate-900 hover:bg-ivory-100',
        'ghost' => 'border border-chocolate-800/20 bg-transparent text-chocolate-900 hover:bg-ivory-100',
    ];
    $class = $base.' '.($variants[$variant] ?? $variants['primary']);
@endphp

@if ($attributes->has('href'))
    <a {{ $attributes->merge(['class' => $class]) }}>{{ $slot }}</a>
@else
    <button {{ $attributes->merge(['type' => 'button', 'class' => $class]) }}>{{ $slot }}</button>
@endif
