@extends('layouts.app')

@section('title', $artisan->name.' — '.config('app.name'))

@section('meta_description', \Illuminate\Support\Str::limit(strip_tags((string) ($artisan->bio ?? 'Artisan profile on GicoGifts.')), 158))

@section('canonical', route('artisans.show', $artisan->slug, true))

@php
    $artisanUrl = route('artisans.show', $artisan->slug, true);
    $artisanLd = ['@context' => 'https://schema.org', '@graph' => [[
        '@type' => 'BreadcrumbList',
        'itemListElement' => [
            ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => url('/')],
            ['@type' => 'ListItem', 'position' => 2, 'name' => 'Artisans', 'item' => route('artisans.index', [], true)],
            ['@type' => 'ListItem', 'position' => 3, 'name' => $artisan->name, 'item' => $artisanUrl],
        ],
    ]]];
@endphp

@push('jsonld')
    <script type="application/ld+json">{!! json_encode($artisanLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS) !!}</script>
@endpush

@section('content')
    <h1 class="text-3xl font-semibold text-stone-900">{{ $artisan->name }}</h1>
    @if($artisan->region)
        <p class="mt-2 text-sm text-stone-600">{{ $artisan->region->name }}</p>
    @endif
    @if($artisan->bio)
        <p class="mt-6 text-stone-700">{{ $artisan->bio }}</p>
    @endif
    @if($artisan->products->isNotEmpty())
        <h2 class="mt-10 text-lg font-semibold">Products</h2>
        <div class="mt-4 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($artisan->products as $product)
                @include('frontend.partials.product-card', ['product' => $product])
            @endforeach
        </div>
    @endif
@endsection
