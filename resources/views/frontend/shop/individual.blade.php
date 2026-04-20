@extends('layouts.app')

@section('title', 'Individual pieces — '.config('app.name'))

@section('meta_description', 'Handmade individual gifts and décor from GicoGifts — marble, textiles, pottery, and more from Rajasthan.')

@section('content')
    <h1 class="text-2xl font-semibold text-stone-900">Individual pieces</h1>
    <p class="mt-2 text-stone-600 text-sm">Marble, soapstone, and more from the bench.</p>
    <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @foreach($products as $product)
            @include('frontend.partials.product-card', ['product' => $product])
        @endforeach
    </div>
    <div class="mt-8">{{ $products->links() }}</div>
@endsection
