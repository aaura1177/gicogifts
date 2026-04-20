@extends('layouts.app')

@section('title', $region->name.' — Shop — '.config('app.name'))

@section('meta_description', 'Browse '.$region->name.' gifts and décor from GicoGifts — handmade in Rajasthan, shipped from Udaipur.')

@section('canonical', route('shop.region', $region->slug, true))

@php
    $regionUrl = route('shop.region', $region->slug, true);
    $regionLd = ['@context' => 'https://schema.org', '@graph' => [[
        '@type' => 'BreadcrumbList',
        'itemListElement' => [
            ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => url('/')],
            ['@type' => 'ListItem', 'position' => 2, 'name' => 'Shop', 'item' => route('shop.index', [], true)],
            ['@type' => 'ListItem', 'position' => 3, 'name' => $region->name, 'item' => $regionUrl],
        ],
    ]]];
@endphp

@push('jsonld')
    <script type="application/ld+json">{!! json_encode($regionLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS) !!}</script>
@endpush

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <nav class="text-xs text-chocolate-700/80">
            <a href="{{ route('shop.index') }}" class="hover:text-sienna-600">Shop</a>
            <span class="mx-1">/</span>
            <span class="text-chocolate-900">{{ $region->name }}</span>
        </nav>
        <h1 class="mt-3 font-display text-3xl font-medium text-chocolate-900">{{ $region->name }}</h1>
        @if($region->description)
            <p class="mt-3 max-w-2xl text-sm leading-relaxed text-chocolate-800/85">{{ $region->description }}</p>
        @endif
        <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($products as $product)
                @include('frontend.partials.product-card', ['product' => $product])
            @endforeach
        </div>
        <div class="mt-10">{{ $products->links() }}</div>
    </div>
@endsection
