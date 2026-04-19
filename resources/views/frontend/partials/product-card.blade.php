@php
    $quickAdd = $quickAdd ?? false;
    $narrow = $narrow ?? false;
    $img = $product->getFirstMediaUrl('images');
    if (! $img) {
        $img = 'https://placehold.co/640x480/F7EEE3/6D3620?text=' . rawurlencode($product->name);
    }
    $articleClass = 'group rounded-2xl border border-ivory-200 bg-white overflow-hidden shadow-sm transition hover:shadow-warm ' . ($narrow ? 'w-full max-w-sm' : '');
@endphp
<article class="{{ trim($articleClass) }}">
    <div class="relative aspect-[4/3] w-full overflow-hidden bg-ivory-100">
        <a href="{{ route('product.show', $product->slug) }}" class="block h-full">
            <img src="{{ $img }}" alt="{{ $product->name }}" class="h-full w-full object-cover transition duration-300 group-hover:scale-[1.02]" width="640" height="480" loading="lazy">
        </a>
        @if($quickAdd)
            <div class="pointer-events-none absolute inset-0 flex items-end justify-center bg-gradient-to-t from-chocolate-900/50 to-transparent opacity-0 transition group-hover:opacity-100">
                <div class="pointer-events-auto m-4 w-full max-w-[12rem]" x-data="addToCartBtn({{ $product->id }})">
                    <button type="button" @click="add" x-bind:disabled="loading" class="w-full min-h-[44px] rounded-lg bg-sienna-500 px-4 py-2.5 text-sm font-medium text-white shadow-warm hover:bg-sienna-600 disabled:opacity-50">
                        <span x-show="!loading">Add to cart</span>
                        <span x-show="loading" x-cloak>Adding…</span>
                    </button>
                    <p x-show="message" x-text="message" class="mt-2 text-center text-xs text-white drop-shadow" x-cloak></p>
                </div>
            </div>
        @endif
    </div>
    <div class="p-4">
        <a href="{{ route('product.show', $product->slug) }}" class="block">
            <h3 class="font-medium text-chocolate-900 group-hover:text-sienna-600">{{ $product->name }}</h3>
            @if($product->subtitle)
                <p class="mt-1 text-sm text-chocolate-800/75 line-clamp-2">{{ $product->subtitle }}</p>
            @endif
            <p class="mt-2 text-sm font-semibold text-chocolate-900">₹{{ number_format($product->price_inr, 0) }}</p>
        </a>
    </div>
</article>
