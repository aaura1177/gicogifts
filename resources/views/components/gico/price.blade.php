@props([
    'amount',
    'compare' => null,
])

<div {{ $attributes->merge(['class' => 'flex flex-wrap items-baseline gap-2']) }}>
    <span class="text-lg font-medium text-chocolate-900">₹{{ number_format((float) $amount, 0) }}</span>
    @if($compare && (float) $compare > (float) $amount)
        <span class="text-sm text-chocolate-700/60 line-through">₹{{ number_format((float) $compare, 0) }}</span>
    @endif
</div>
