@extends('layouts.app')

@section('title', config('app.name').' — Rajasthan, unboxed.')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Hero --}}
        <section class="grid gap-10 lg:grid-cols-2 lg:items-center lg:gap-14 py-12 lg:py-20">
            <div class="order-2 lg:order-1 text-center lg:text-left">
                <p class="text-xs font-medium uppercase tracking-[0.2em] text-sienna-600">GicoGifts · Udaipur</p>
                <h1 class="mt-4 font-display text-4xl sm:text-5xl lg:text-6xl font-medium tracking-tight text-chocolate-900">Rajasthan, unboxed.</h1>
                <p class="mt-5 text-lg text-chocolate-800/90 max-w-xl mx-auto lg:mx-0">Curated artisan gift boxes — story-first, hand-packed with care.</p>
                <div class="mt-8 flex flex-wrap justify-center lg:justify-start gap-3">
                    <x-gico.button variant="primary" href="{{ route('shop.boxes') }}">Shop gift boxes</x-gico.button>
                    <x-gico.button variant="ghost" href="{{ route('about') }}">Our story</x-gico.button>
                </div>
            </div>
            <div class="order-1 lg:order-2">
                <div class="aspect-[4/3] overflow-hidden rounded-3xl shadow-warm ring-1 ring-ivory-200">
                    <img src="{{ $heroImage }}" alt="Curated gift box" class="h-full w-full object-cover" width="1200" height="900" fetchpriority="high">
                </div>
            </div>
        </section>

        {{-- Trust strip --}}
        <section class="grid gap-4 sm:grid-cols-3 rounded-2xl border border-ivory-200 bg-white/80 px-4 py-5 sm:px-6 text-center text-sm text-chocolate-800 shadow-sm">
            <p><span class="font-medium text-chocolate-900">Free shipping</span> above ₹2,000</p>
            <p><span class="font-medium text-chocolate-900">Handmade</span> by vetted artisans</p>
            <p><span class="font-medium text-chocolate-900">Packed in Udaipur</span> with care</p>
        </section>

        {{-- Featured boxes 3 + 2 --}}
        <section class="mt-20">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <h2 class="font-display text-2xl font-medium text-chocolate-900">Featured gift boxes</h2>
                    <p class="mt-1 text-sm text-chocolate-800/80">Hover a card to add straight to your bag.</p>
                </div>
                <a href="{{ route('shop.boxes') }}" class="text-sm font-medium text-sienna-600 hover:text-sienna-700">View all boxes</a>
            </div>
            @if($featuredBoxes->isNotEmpty())
                <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($featuredBoxes->take(3) as $product)
                        @include('frontend.partials.product-card', ['product' => $product, 'quickAdd' => true])
                    @endforeach
                </div>
                @if($featuredBoxes->count() > 3)
                    <div class="mt-6 flex flex-wrap justify-center gap-6">
                        @foreach($featuredBoxes->slice(3, 2) as $product)
                            @include('frontend.partials.product-card', ['product' => $product, 'quickAdd' => true, 'narrow' => true])
                        @endforeach
                    </div>
                @endif
            @else
                <p class="mt-6 text-sm text-chocolate-700/80">Gift boxes will appear here once catalogued.</p>
            @endif
        </section>

        {{-- Occasions --}}
        <section class="mt-20">
            <h2 class="font-display text-2xl font-medium text-chocolate-900">Shop by occasion</h2>
            <div class="mt-6 flex flex-wrap gap-2">
                @foreach($occasions as $occasion)
                    <a href="{{ route('shop.occasion', $occasion->slug) }}" class="rounded-full border border-ivory-200 bg-white px-4 py-2.5 text-sm font-medium text-chocolate-900 shadow-sm hover:border-sienna-300 hover:bg-ivory-50">{{ $occasion->name }}</a>
                @endforeach
            </div>
        </section>

        {{-- Regions --}}
        <section class="mt-20">
            <h2 class="font-display text-2xl font-medium text-chocolate-900">Our craft regions</h2>
            <p class="mt-2 max-w-2xl text-sm text-chocolate-800/85">Each landscape leaves a fingerprint on material, motif, and making.</p>
            <div class="mt-8 grid gap-6 md:grid-cols-3">
                @foreach($regions as $region)
                    <a href="{{ route('shop.region', $region->slug) }}" class="group rounded-2xl border border-ivory-200 bg-white p-6 shadow-sm transition hover:shadow-warm hover:border-sienna-200/60">
                        <h3 class="font-display text-lg font-medium text-chocolate-900 group-hover:text-sienna-600">{{ $region->name }}</h3>
                        @if($region->description)
                            <p class="mt-3 text-sm text-chocolate-800/80 line-clamp-3">{{ $region->description }}</p>
                        @endif
                        <p class="mt-4 text-xs font-medium uppercase tracking-wide text-sienna-600">Shop region →</p>
                    </a>
                @endforeach
            </div>
        </section>

        {{-- How it works --}}
        <section class="mt-20 rounded-3xl border border-ivory-200 bg-white px-6 py-12 sm:px-10 shadow-sm">
            <h2 class="font-display text-2xl font-medium text-chocolate-900 text-center">How it works</h2>
            <ol class="mt-10 grid gap-8 sm:grid-cols-2 lg:grid-cols-4 text-center text-sm text-chocolate-800/90">
                <li>
                    <span class="mx-auto flex h-10 w-10 items-center justify-center rounded-full bg-ivory-100 text-sm font-semibold text-chocolate-900">1</span>
                    <p class="mt-3 font-medium text-chocolate-900">Choose</p>
                    <p class="mt-1">Pick a box or build from individual pieces.</p>
                </li>
                <li>
                    <span class="mx-auto flex h-10 w-10 items-center justify-center rounded-full bg-ivory-100 text-sm font-semibold text-chocolate-900">2</span>
                    <p class="mt-3 font-medium text-chocolate-900">We assemble</p>
                    <p class="mt-1">We curate components and check quality.</p>
                </li>
                <li>
                    <span class="mx-auto flex h-10 w-10 items-center justify-center rounded-full bg-ivory-100 text-sm font-semibold text-chocolate-900">3</span>
                    <p class="mt-3 font-medium text-chocolate-900">Hand-packed</p>
                    <p class="mt-1">Wrapped in Udaipur with tissue and care.</p>
                </li>
                <li>
                    <span class="mx-auto flex h-10 w-10 items-center justify-center rounded-full bg-ivory-100 text-sm font-semibold text-chocolate-900">4</span>
                    <p class="mt-3 font-medium text-chocolate-900">Delivered</p>
                    <p class="mt-1">Most metros in 3–7 business days.</p>
                </li>
            </ol>
        </section>

        {{-- Artisans horizontal scroll --}}
        @if($artisans->isNotEmpty())
            <section class="mt-20">
                <div class="flex items-end justify-between gap-4">
                    <h2 class="font-display text-2xl font-medium text-chocolate-900">Meet our artisans</h2>
                    <a href="{{ route('artisans.index') }}" class="text-sm font-medium text-sienna-600 hover:text-sienna-700">All artisans</a>
                </div>
                <div class="mt-6 -mx-4 flex gap-4 overflow-x-auto px-4 pb-2 snap-x snap-mandatory sm:-mx-6 sm:px-6">
                    @foreach($artisans as $artisan)
                        <a href="{{ route('artisans.show', $artisan->slug) }}" class="snap-start shrink-0 w-40 sm:w-44 text-center">
                            <div class="mx-auto h-28 w-28 overflow-hidden rounded-full border border-ivory-200 bg-ivory-100 shadow-sm ring-2 ring-white">
                                @if($artisan->photo_path)
                                    <img src="{{ $artisan->photo_path }}" alt="" class="h-full w-full object-cover" width="112" height="112" loading="lazy">
                                @else
                                    <div class="flex h-full w-full items-center justify-center text-2xl font-display text-chocolate-700/40">{{ \Illuminate\Support\Str::substr($artisan->name, 0, 1) }}</div>
                                @endif
                            </div>
                            <p class="mt-3 text-sm font-medium text-chocolate-900 line-clamp-2">{{ $artisan->name }}</p>
                            @if($artisan->region)
                                <p class="mt-0.5 text-xs text-chocolate-700/70">{{ $artisan->region->name }}</p>
                            @endif
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Social proof --}}
        <section class="mt-20">
            <div class="grid gap-6 sm:grid-cols-3 rounded-2xl border border-ivory-200 bg-gradient-to-b from-white to-ivory-50 px-6 py-8 text-center shadow-sm">
                <div>
                    <p class="font-display text-3xl font-medium text-chocolate-900">150+</p>
                    <p class="mt-1 text-sm text-chocolate-800/80">Artisan partners</p>
                </div>
                <div>
                    <p class="font-display text-3xl font-medium text-chocolate-900">{{ $regions->count() ?: '12' }}</p>
                    <p class="mt-1 text-sm text-chocolate-800/80">Regions represented</p>
                </div>
                <div>
                    <p class="font-display text-3xl font-medium text-chocolate-900">4.9★</p>
                    <p class="mt-1 text-sm text-chocolate-800/80">Average rating</p>
                </div>
            </div>
            <div class="mt-8 grid gap-6 md:grid-cols-3">
                @foreach([
                    ['We sent the Mewar box to London — it arrived flawless. The note inside made my mum cry (happy tears).', '— Aditi, Mumbai'],
                    ['Finally gifting that feels intentional. The artisan cards are such a thoughtful touch.', '— Rahul, Bengaluru'],
                    ['Corporate orders for our Jaipur offsite were handled end-to-end. Impressive packaging.', '— Neha, HR'],
                ] as $t)
                    <figure class="rounded-2xl border border-ivory-200 bg-white p-6 shadow-sm">
                        <blockquote class="text-sm leading-relaxed text-chocolate-800/90">“{{ $t[0] }}”</blockquote>
                        <figcaption class="mt-4 text-xs font-medium text-chocolate-700/80">{{ $t[1] }}</figcaption>
                    </figure>
                @endforeach
            </div>
        </section>

        {{-- Stories --}}
        <section class="mt-20">
            <div class="flex items-end justify-between gap-4">
                <h2 class="font-display text-2xl font-medium text-chocolate-900">Stories from the workshop road</h2>
                <a href="{{ route('stories.index') }}" class="text-sm font-medium text-sienna-600 hover:text-sienna-700">View all</a>
            </div>
            <div class="mt-8 grid gap-6 md:grid-cols-3">
                @foreach($stories as $story)
                    @include('frontend.partials.story-card', ['story' => $story, 'compact' => false])
                @endforeach
            </div>
        </section>

        {{-- Newsletter --}}
        <div class="mt-20 mb-6">
            @include('frontend.partials.newsletter-cta', [
                'wrapperClass' => '',
                'heading' => 'Notes from the packing table',
                'subheading' => 'Occasional email: new boxes, artisan visits, and quiet shop updates. No spam.',
            ])
        </div>
    </div>
@endsection
