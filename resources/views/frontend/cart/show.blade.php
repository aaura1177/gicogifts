@extends('layouts.app')

@section('title', 'Cart — '.config('app.name'))

@section('content')
    <h1 class="text-2xl font-semibold text-stone-900">Your cart</h1>
    @if($cart->items->isEmpty())
        <p class="mt-4 text-stone-600">Your cart is empty. <a href="{{ route('shop.index') }}" class="font-medium text-stone-900 underline">Continue shopping</a></p>
    @else
        <div class="mt-8 space-y-6">
            @foreach($cart->items as $line)
                @php($p = $line->product)
                <div class="flex flex-wrap items-center gap-4 border-b border-stone-100 pb-6">
                    <img src="{{ $p?->getFirstMediaUrl('images') ?: 'https://placehold.co/96x96/e7e5e4/57534e?text=GG' }}" alt="" class="h-20 w-20 rounded object-cover" width="80" height="80">
                    <div class="flex-1 min-w-[200px]">
                        <a href="{{ route('product.show', $p->slug) }}" class="font-medium text-stone-900 hover:underline">{{ $p->name }}</a>
                        <p class="text-sm text-stone-600">₹{{ number_format($line->unit_price_inr, 0) }} each</p>
                    </div>
                    <form method="post" action="{{ route('cart.update') }}" class="flex items-center gap-2">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="cart_item_id" value="{{ $line->id }}">
                        <label class="text-sm text-stone-600">Qty</label>
                        <input type="number" name="quantity" value="{{ $line->quantity }}" min="0" max="99" class="w-20 rounded border border-stone-300 px-2 py-1 text-sm">
                        <button type="submit" class="rounded border border-stone-300 px-3 py-1 text-sm hover:bg-stone-50">Update</button>
                    </form>
                    <form method="post" action="{{ route('cart.remove') }}" onsubmit="return confirm('Remove this item?');">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="cart_item_id" value="{{ $line->id }}">
                        <button type="submit" class="text-sm text-red-600 hover:underline">Remove</button>
                    </form>
                </div>
            @endforeach
        </div>
        <div class="mt-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <p class="text-lg font-semibold">Subtotal: ₹{{ number_format($cart->subtotalInr(), 0) }}</p>
            <a href="{{ route('checkout.show') }}" class="inline-flex justify-center rounded-lg bg-stone-900 px-6 py-3 text-sm font-medium text-white hover:bg-stone-800">Checkout</a>
        </div>
    @endif
@endsection
