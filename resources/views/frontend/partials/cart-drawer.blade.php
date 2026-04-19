<div x-show="cartOpen" x-cloak class="fixed inset-0 z-50 flex justify-end" aria-modal="true" role="dialog">
    <div class="absolute inset-0 bg-chocolate-900/40" @click="closeCart()"></div>
    <div class="relative flex h-full w-full max-w-md flex-col bg-ivory-50 shadow-warm ring-1 ring-ivory-200">
        <div class="flex items-center justify-between border-b border-ivory-200 px-4 py-4">
            <h2 class="font-display text-lg font-medium text-chocolate-900">Cart</h2>
            <button type="button" class="inline-flex min-h-[44px] min-w-[44px] items-center justify-center rounded-lg text-chocolate-700 hover:bg-ivory-100" @click="closeCart()" aria-label="Close cart">&times;</button>
        </div>
        <div class="flex-1 space-y-4 overflow-y-auto p-4 text-sm">
            @forelse($storeCart->items as $line)
                @php($p = $line->product)
                <div class="flex gap-3 border-b border-ivory-200 pb-4">
                    <img src="{{ $p?->getFirstMediaUrl('images') ?: 'https://placehold.co/96x96/F7EEE3/6D3620?text=GG' }}" alt="" class="h-16 w-16 shrink-0 rounded-lg object-cover ring-1 ring-ivory-200" width="64" height="64" loading="lazy">
                    <div class="min-w-0 flex-1">
                        <a href="{{ route('product.show', $p->slug) }}" class="font-medium text-chocolate-900 hover:text-sienna-600">{{ $p->name }}</a>
                        <p class="text-chocolate-700/80">Qty {{ $line->quantity }} · ₹{{ number_format($line->unit_price_inr, 0) }}</p>
                    </div>
                </div>
            @empty
                <p class="text-chocolate-700/80">Your cart is empty.</p>
            @endforelse
        </div>
        <div class="space-y-2 border-t border-ivory-200 bg-white p-4">
            <x-gico.button variant="secondary" href="{{ route('cart.show') }}" class="w-full">Open full cart</x-gico.button>
            <x-gico.button variant="primary" href="{{ route('checkout.show') }}" class="w-full">Checkout</x-gico.button>
        </div>
    </div>
</div>
