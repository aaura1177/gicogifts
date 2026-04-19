@extends('layouts.checkout')

@section('title', 'Checkout — '.config('app.name'))

@section('content')
    @php
        $sub = $cart->subtotalInr();
        $ship = $sub >= 2000 ? 0 : 99;
        $total = $sub + $ship;
    @endphp
    <h1 class="font-display text-2xl font-medium text-chocolate-900">Checkout</h1>
    <p class="mt-2 text-sm text-chocolate-800/85">Flat ₹99 shipping; free above ₹2,000. India: Razorpay. Outside India: Stripe.</p>

    <div class="mt-8 grid gap-10 lg:grid-cols-2">
        <form
            x-data="checkoutForm(@js([
                'email' => old('email', auth()->user()?->email),
                'phone' => old('phone'),
                'name' => old('name', auth()->user()?->name),
                'line1' => old('line1'),
                'line2' => old('line2'),
                'city' => old('city'),
                'state' => old('state'),
                'postal_code' => old('postal_code'),
                'country' => old('country', 'IN'),
            ]))"
            @submit.prevent="submit"
            class="space-y-5"
        >
            @csrf
            <fieldset class="rounded-2xl border border-ivory-200 bg-white p-5 space-y-3 shadow-sm">
                <legend class="text-sm font-semibold text-chocolate-900">Contact</legend>
                <input type="email" x-model="fields.email" required placeholder="Email" class="w-full min-h-[44px] rounded-lg border border-ivory-200 bg-ivory-50/50 px-3 py-2 text-sm text-chocolate-900 shadow-sm focus:border-sienna-500 focus:outline-none focus:ring-1 focus:ring-sienna-500">
                <input type="text" x-model="fields.phone" placeholder="Phone" class="w-full min-h-[44px] rounded-lg border border-ivory-200 bg-ivory-50/50 px-3 py-2 text-sm text-chocolate-900 shadow-sm focus:border-sienna-500 focus:outline-none focus:ring-1 focus:ring-sienna-500">
            </fieldset>
            <fieldset class="rounded-2xl border border-ivory-200 bg-white p-5 space-y-3 shadow-sm">
                <legend class="text-sm font-semibold text-chocolate-900">Shipping address</legend>
                <input type="text" x-model="fields.name" required placeholder="Full name" class="w-full min-h-[44px] rounded-lg border border-ivory-200 bg-ivory-50/50 px-3 py-2 text-sm text-chocolate-900 shadow-sm focus:border-sienna-500 focus:outline-none focus:ring-1 focus:ring-sienna-500">
                <input type="text" x-model="fields.line1" required placeholder="Address line 1" class="w-full min-h-[44px] rounded-lg border border-ivory-200 bg-ivory-50/50 px-3 py-2 text-sm text-chocolate-900 shadow-sm focus:border-sienna-500 focus:outline-none focus:ring-1 focus:ring-sienna-500">
                <input type="text" x-model="fields.line2" placeholder="Address line 2" class="w-full min-h-[44px] rounded-lg border border-ivory-200 bg-ivory-50/50 px-3 py-2 text-sm text-chocolate-900 shadow-sm focus:border-sienna-500 focus:outline-none focus:ring-1 focus:ring-sienna-500">
                <div class="grid grid-cols-2 gap-2">
                    <input type="text" x-model="fields.city" required placeholder="City" class="min-h-[44px] rounded-lg border border-ivory-200 bg-ivory-50/50 px-3 py-2 text-sm text-chocolate-900 shadow-sm focus:border-sienna-500 focus:outline-none focus:ring-1 focus:ring-sienna-500">
                    <input type="text" x-model="fields.state" required placeholder="State" class="min-h-[44px] rounded-lg border border-ivory-200 bg-ivory-50/50 px-3 py-2 text-sm text-chocolate-900 shadow-sm focus:border-sienna-500 focus:outline-none focus:ring-1 focus:ring-sienna-500">
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <input type="text" x-model="fields.postal_code" @input.debounce.500ms="checkDeliveryPin()" required placeholder="PIN code" class="min-h-[44px] rounded-lg border border-ivory-200 bg-ivory-50/50 px-3 py-2 text-sm text-chocolate-900 shadow-sm focus:border-sienna-500 focus:outline-none focus:ring-1 focus:ring-sienna-500">
                    <input type="text" x-model="fields.country" @input.debounce.200ms="syncGatewayFromCountry(); checkDeliveryPin()" placeholder="ISO country (IN, US…)" class="min-h-[44px] rounded-lg border border-ivory-200 bg-ivory-50/50 px-3 py-2 text-sm text-chocolate-900 shadow-sm focus:border-sienna-500 focus:outline-none focus:ring-1 focus:ring-sienna-500">
                </div>
                <p x-show="deliveryHint" x-text="deliveryHint" class="text-xs text-chocolate-800/80" x-cloak></p>
            </fieldset>
            <fieldset class="rounded-2xl border border-ivory-200 bg-white p-5 space-y-3 shadow-sm">
                <legend class="text-sm font-semibold text-chocolate-900">Payment</legend>
                <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-ivory-200 p-3 hover:bg-ivory-50" :class="payment_gateway === 'razorpay' ? 'ring-1 ring-sienna-400' : ''">
                    <input type="radio" name="payment_gateway" value="razorpay" x-model="payment_gateway" class="mt-1">
                    <span>
                        <span class="font-medium text-chocolate-900">Razorpay</span>
                        <span class="block text-xs text-chocolate-800/75">Cards, UPI, netbanking, wallets (India)</span>
                    </span>
                </label>
                <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-ivory-200 p-3 hover:bg-ivory-50" :class="payment_gateway === 'stripe' ? 'ring-1 ring-sienna-400' : ''">
                    <input type="radio" name="payment_gateway" value="stripe" x-model="payment_gateway" class="mt-1">
                    <span>
                        <span class="font-medium text-chocolate-900">Stripe</span>
                        <span class="block text-xs text-chocolate-800/75">International cards (non-India shipping)</span>
                    </span>
                </label>
            </fieldset>
            <p x-show="error" x-text="error" class="text-sm text-red-800" x-cloak></p>
            <x-gico.button variant="primary" type="submit" class="w-full" x-bind:disabled="loading">
                <span x-show="!loading">Pay ₹{{ number_format($total, 0) }}</span>
                <span x-show="loading" x-cloak>Processing…</span>
            </x-gico.button>
        </form>
        <div class="rounded-2xl border border-ivory-200 bg-white p-5 text-sm text-chocolate-800 shadow-sm">
            <h2 class="font-display font-medium text-chocolate-900">Order summary</h2>
            <ul class="mt-4 space-y-2">
                @foreach($cart->items as $line)
                    <li class="flex justify-between gap-4">
                        <span>{{ $line->product->name }} × {{ $line->quantity }}</span>
                        <span>₹{{ number_format((float) $line->unit_price_inr * $line->quantity, 0) }}</span>
                    </li>
                @endforeach
            </ul>
            <div class="mt-4 border-t border-ivory-100 pt-4 space-y-1">
                <div class="flex justify-between"><span>Subtotal</span><span>₹{{ number_format($sub, 0) }}</span></div>
                <div class="flex justify-between"><span>Shipping</span><span>₹{{ number_format($ship, 0) }}</span></div>
                <div class="flex justify-between font-semibold text-chocolate-900"><span>Total</span><span>₹{{ number_format($total, 0) }}</span></div>
            </div>
        </div>
    </div>
@endsection
