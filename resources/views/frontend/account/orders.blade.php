@extends('layouts.account')

@section('title', 'Orders — '.config('app.name'))

@section('content')
    <h1 class="text-2xl font-semibold text-stone-900">Your orders</h1>
    @if($orders->isEmpty())
        <p class="mt-4 text-stone-600">No orders yet.</p>
    @else
        <ul class="mt-6 space-y-3 text-sm">
            @foreach($orders as $order)
                <li class="flex justify-between gap-4 rounded-lg border border-stone-200 bg-white px-4 py-3">
                    <a href="{{ route('account.order.show', $order) }}" class="font-mono font-medium text-stone-900 hover:underline">{{ $order->order_number }}</a>
                    <span class="text-stone-600">{{ $order->status }}</span>
                </li>
            @endforeach
        </ul>
        <div class="mt-6">{{ $orders->links() }}</div>
    @endif
@endsection
