@extends('layouts.account')

@section('title', 'Order '.$order->order_number.' — '.config('app.name'))

@section('content')
    <h1 class="text-2xl font-semibold text-stone-900">Order {{ $order->order_number }}</h1>
    <p class="mt-2 text-sm text-stone-600">Status: <strong>{{ $order->status }}</strong></p>
    <ul class="mt-6 space-y-2 text-sm">
        @foreach($order->items as $item)
            <li class="flex justify-between border-b border-stone-100 py-2">
                <span>{{ $item->product_name }} × {{ $item->quantity }}</span>
                <span>₹{{ number_format($item->line_total_inr, 0) }}</span>
            </li>
        @endforeach
    </ul>
    <p class="mt-6 font-semibold">Total: ₹{{ number_format($order->total_inr, 0) }}</p>
@endsection
