@extends('layouts.checkout')

@section('title', 'Thank you — '.config('app.name'))

@section('content')
    <h1 class="font-display text-2xl font-medium text-chocolate-900">Thank you</h1>
    <p class="mt-4 text-chocolate-800/90">
        Order <span class="font-mono font-medium text-chocolate-900">{{ $order->order_number }}</span>
        @if($order->status === 'paid')
            is <strong class="text-emerald-800">paid</strong>. A confirmation email with your invoice PDF is on the way.
        @else
            is <strong>{{ $order->status }}</strong>. If you just finished paying, confirmation can take a few seconds while we verify with the bank — refresh this page shortly.
        @endif
    </p>
    @if($order->status !== 'paid')
        <p class="mt-2 text-sm text-chocolate-800/75">We only mark orders paid after a verified webhook from Razorpay or Stripe — never from the browser alone.</p>
    @endif
    <a href="{{ route('shop.index') }}" class="mt-8 inline-flex text-sm font-medium text-sienna-600 hover:text-sienna-700">Continue shopping</a>
@endsection
