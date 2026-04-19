@props([
    'label' => null,
])

<div class="space-y-1">
    @if($label)
        <label class="block text-sm font-medium text-chocolate-800">{{ $label }}</label>
    @endif
    <input {{ $attributes->merge([
        'class' => 'min-h-[44px] w-full rounded-lg border border-ivory-200 bg-white px-3 py-2 text-sm text-chocolate-900 shadow-sm placeholder:text-chocolate-700/40 focus:border-sienna-500 focus:outline-none focus:ring-1 focus:ring-sienna-500',
    ]) }} />
</div>
