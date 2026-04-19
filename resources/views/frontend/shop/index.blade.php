@extends('layouts.app')

@section('title', 'Shop — '.config('app.name'))

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 md:py-16">
        <h1 class="font-display text-3xl font-medium text-chocolate-900">Shop</h1>
        <form method="get" action="{{ route('shop.index') }}" class="mt-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:gap-4 max-w-xl">
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Search name or SKU" class="min-h-[44px] flex-1 rounded-lg border border-ivory-200 bg-white px-3 py-2 text-sm text-chocolate-900 shadow-sm focus:border-sienna-500 focus:outline-none focus:ring-1 focus:ring-sienna-500">
            <x-gico.button variant="primary" type="submit" class="shrink-0">Search</x-gico.button>
        </form>

        @if($occasions->isNotEmpty())
            <section class="mt-14" aria-labelledby="shop-occasions-heading">
                <h2 id="shop-occasions-heading" class="font-display text-xl font-medium text-chocolate-900">Shop by occasion</h2>
                <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($occasions as $occasion)
                        @include('frontend.partials.occasion-card', ['occasion' => $occasion])
                    @endforeach
                </div>
            </section>
        @endif

        <div class="mt-14 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($products as $product)
                @include('frontend.partials.product-card', ['product' => $product])
            @endforeach
        </div>
        <div class="mt-10">{{ $products->links() }}</div>
    </div>
@endsection
