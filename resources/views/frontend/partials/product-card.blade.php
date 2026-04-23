@php
    $quickAdd = $quickAdd ?? false;
    $narrow = $narrow ?? false;
    $img = $product->getFirstMediaUrl('images');
    if (! $img) {
        $img = 'https://placehold.co/800x800/F7EEE3/6D3620?text=' . rawurlencode($product->name);
    }
    $region = $product->region?->name;
@endphp

<article class="group {{ $narrow ? 'w-full max-w-sm' : '' }}">
    {{-- Image is the frame. No border, no bg-white wrapper. --}}
    <a href="{{ route('product.show', $product->slug) }}" class="block relative aspect-square rounded-2xl overflow-hidden bg-ivory-100">
        <img src="{{ $img }}" alt="{{ $product->name }}"
             class="w-full h-full object-cover transition-transform duration-700 ease-[cubic-bezier(0.22,1,0.36,1)] group-hover:scale-[1.04]"
             width="800" height="800" loading="lazy">

        @if($quickAdd)
          {{-- Subtle hover-only quick-add, no gradient shout --}}
          <div class="pointer-events-none absolute inset-x-0 bottom-0 p-4 opacity-0 translate-y-2 transition duration-300 group-hover:opacity-100 group-hover:translate-y-0"
               x-data="addToCartBtn({{ $product->id }})">
            <button type="button" @click.prevent="add" x-bind:disabled="loading"
                    class="pointer-events-auto w-full min-h-[44px] rounded-lg bg-chocolate-900/95 text-ivory-50 text-sm font-medium backdrop-blur hover:bg-chocolate-900 disabled:opacity-50 transition">
              <span x-show="!loading">Add to cart — ₹{{ number_format($product->price_inr, 0) }}</span>
              <span x-show="loading" x-cloak>Adding…</span>
            </button>
          </div>
        @endif
    </a>

    <div class="pt-5">
        @if($region)
          <p class="gico-overline text-chocolate-800/60">{{ $region }}</p>
        @endif
        <a href="{{ route('product.show', $product->slug) }}" class="block">
            <h3 class="font-display text-xl font-medium text-chocolate-900 group-hover:text-sienna-600 transition">{{ $product->name }}</h3>
        </a>
        <div class="mt-3 flex items-baseline justify-between">
            <p class="text-lg font-medium text-chocolate-900">₹{{ number_format($product->price_inr, 0) }}</p>
            @if($product->compare_at_price_inr && $product->compare_at_price_inr > $product->price_inr)
                <p class="text-sm text-chocolate-700/50 line-through">₹{{ number_format($product->compare_at_price_inr, 0) }}</p>
            @endif
        </div>
    </div>
</article>
