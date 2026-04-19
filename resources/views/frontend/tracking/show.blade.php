@extends('layouts.app')

@section('title', 'Track order — '.config('app.name'))

@section('content')
    <h1 class="text-2xl font-semibold text-stone-900">Tracking</h1>
    @if(!$shipment)
        <p class="mt-4 text-stone-600">No shipment found for AWB <span class="font-mono">{{ $awb }}</span>.</p>
    @else
        <div class="mt-6 flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-sm text-stone-600">AWB <span class="font-mono font-medium text-stone-900">{{ $shipment->awb_code }}</span></p>
                @if($shipment->order?->order_number)
                    <p class="mt-1 text-sm text-stone-600">Order <span class="font-medium text-stone-900">{{ $shipment->order->order_number }}</span></p>
                @endif
                <p class="mt-2 text-sm">Courier: {{ $shipment->courier_name ?? '—' }}</p>
                <p class="mt-1 text-sm">Status: {{ $shipment->status ?? '—' }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ $whatsappShare }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center rounded-lg border border-stone-300 bg-white px-3 py-2 text-sm font-medium text-stone-900 hover:bg-stone-50">Share on WhatsApp</a>
                @if($shipment->tracking_url)
                    <a href="{{ $shipment->tracking_url }}" class="inline-flex items-center rounded-lg border border-stone-900 bg-stone-900 px-3 py-2 text-sm font-medium text-white hover:bg-stone-800">Courier tracking</a>
                @endif
            </div>
        </div>

        <ol class="mt-10 space-y-4">
            @foreach($timeline as $step)
                <li class="flex gap-3">
                    <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full text-xs font-semibold {{ $step['done'] ? 'bg-emerald-600 text-white' : 'bg-stone-200 text-stone-600' }}">
                        @if($step['done'])✓@else · @endif
                    </span>
                    <div>
                        <p class="font-medium text-stone-900">{{ $step['label'] }}</p>
                        @if(!empty($step['detail']))
                            <p class="text-sm text-stone-600">{{ $step['detail'] }}</p>
                        @endif
                    </div>
                </li>
            @endforeach
        </ol>

        @if($live && !empty($live['tracking_data']) && is_array($live['tracking_data']))
            <div class="mt-10 border-t border-stone-200 pt-6">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-stone-500">Carrier scan log</h2>
                <ul class="mt-3 space-y-2 text-sm text-stone-700">
                    @foreach(array_slice($live['tracking_data'], -12) as $row)
                        @if(is_array($row))
                            <li class="border-b border-stone-100 pb-2">
                                <span class="font-medium text-stone-900">{{ $row['activity'] ?? $row['status'] ?? 'Update' }}</span>
                                @if(!empty($row['date']) || !empty($row['created_at']))
                                    <span class="text-stone-500"> — {{ $row['date'] ?? $row['created_at'] ?? '' }}</span>
                                @endif
                                @if(!empty($row['location']))
                                    <span class="block text-stone-600">{{ $row['location'] }}</span>
                                @endif
                            </li>
                        @endif
                    @endforeach
                </ul>
            </div>
        @endif
    @endif
@endsection
