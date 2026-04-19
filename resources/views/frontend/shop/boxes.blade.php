@extends('layouts.app')

@section('title', 'Gift boxes — '.config('app.name'))

@section('content')
    <h1 class="text-2xl font-semibold text-stone-900">Gift boxes</h1>
    <p class="mt-2 text-stone-600 text-sm">Our curated Rajasthan boxes.</p>
    <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @foreach($products as $product)
            @include('frontend.partials.product-card', ['product' => $product])
        @endforeach
    </div>
    <div class="mt-8">{{ $products->links() }}</div>
@endsection
