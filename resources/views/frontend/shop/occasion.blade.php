@extends('layouts.app')

@section('title', $occasion->name.' — '.config('app.name'))

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 md:py-16">
        <nav class="text-xs text-chocolate-700/80">
            <a href="{{ route('shop.index') }}" class="hover:text-sienna-600">Shop</a>
            <span class="mx-1">/</span>
            <span class="text-chocolate-900">{{ $occasion->name }}</span>
        </nav>
        <h1 class="mt-3 font-display text-3xl font-medium text-chocolate-900">{{ $occasion->name }}</h1>
        <p class="mt-2 text-sm text-chocolate-800/85">Products curated for this occasion.</p>

        @if($occasionList->isNotEmpty())
            <section class="mt-10" aria-labelledby="other-occasions-heading">
                <h2 id="other-occasions-heading" class="text-sm font-medium uppercase tracking-wide text-chocolate-700/80">Other occasions</h2>
                <div class="mt-4 flex flex-wrap gap-3">
                    @foreach($occasionList as $o)
                        @if($o->id !== $occasion->id)
                            <a href="{{ route('shop.occasion', $o->slug) }}" class="inline-flex min-h-[44px] items-center rounded-full border border-ivory-200 bg-white px-4 py-2 text-sm font-medium text-chocolate-900 shadow-sm hover:border-sienna-300 hover:bg-ivory-50">{{ $o->name }}</a>
                        @endif
                    @endforeach
                </div>
            </section>
        @endif

        <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($products as $product)
                @include('frontend.partials.product-card', ['product' => $product])
            @endforeach
        </div>
        <div class="mt-10">{{ $products->links() }}</div>
    </div>
@endsection
