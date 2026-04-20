@extends('layouts.app')

@section('title', $product->name.' — '.config('app.name'))

@php
    $galleryUrls = $product->getMedia('images')->map(fn ($m) => $m->getUrl())->filter()->values()->all();
    if ($galleryUrls === []) {
        $galleryUrls = ['https://placehold.co/900x700/F7EEE3/6D3620?text='.rawurlencode($product->name)];
    }
    $primaryImage = $galleryUrls[0];
    $metaDesc = $product->meta_description
        ?: \Illuminate\Support\Str::limit(strip_tags((string) $product->short_description), 158);
    if (trim($metaDesc) === '') {
        $metaDesc = (string) config('gicogifts.default_meta_description');
    }
    $reviewCount = $product->reviews->count();
    $avgRating = $reviewCount > 0 ? round((float) $product->reviews->avg('rating'), 2) : null;
    $productUrl = route('product.show', $product->slug, true);
    $crumbs = [
        ['Home', url('/')],
        ['Shop', route('shop.index', [], true)],
    ];
    if ($product->region) {
        $crumbs[] = [$product->region->name, route('shop.region', $product->region->slug, true)];
    }
    $breadcrumbItems = [];
    $pos = 1;
    foreach ($crumbs as [$label, $u]) {
        $breadcrumbItems[] = [
            '@type' => 'ListItem',
            'position' => $pos++,
            'name' => $label,
            'item' => $u,
        ];
    }
    $breadcrumbItems[] = [
        '@type' => 'ListItem',
        'position' => $pos,
        'name' => $product->name,
        'item' => $productUrl,
    ];
    $graph = [
        [
            '@type' => 'Product',
            'name' => $product->name,
            'sku' => $product->sku,
            'description' => $metaDesc,
            'image' => $galleryUrls,
            'url' => $productUrl,
            'offers' => [
                '@type' => 'Offer',
                'url' => $productUrl,
                'priceCurrency' => 'INR',
                'price' => number_format((float) $product->price_inr, 2, '.', ''),
                'availability' => 'https://schema.org/InStock',
            ],
        ],
        [
            '@type' => 'BreadcrumbList',
            'itemListElement' => $breadcrumbItems,
        ],
    ];
    if ($reviewCount > 0 && $avgRating !== null) {
        $graph[0]['aggregateRating'] = [
            '@type' => 'AggregateRating',
            'ratingValue' => (string) $avgRating,
            'reviewCount' => (string) $reviewCount,
        ];
    }
@endphp

@section('meta_description', $metaDesc)
@section('canonical', $productUrl)
@section('og_type', 'product')
@section('og_image', $primaryImage)

@push('jsonld')
    <script type="application/ld+json">{!! json_encode(['@context' => 'https://schema.org', '@graph' => $graph], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS) !!}</script>
@endpush

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12">
        <div class="grid gap-10 lg:grid-cols-2 lg:gap-14">
            <div x-data="productGallery(@js($galleryUrls))">
                <div class="aspect-square overflow-hidden rounded-3xl bg-ivory-100 ring-1 ring-ivory-200">
                    <button type="button" class="group relative block h-full w-full cursor-zoom-in text-left focus:outline-none focus-visible:ring-2 focus-visible:ring-sienna-500 focus-visible:ring-offset-2 focus-visible:ring-offset-ivory-50" @click="openZoom()" title="View larger">
                        <img :src="src" alt="{{ $product->name }}" class="h-full w-full object-cover transition group-hover:opacity-95" width="900" height="900" fetchpriority="high">
                        <span class="pointer-events-none absolute bottom-3 right-3 rounded-full bg-chocolate-900/70 px-3 py-1 text-xs font-medium text-white opacity-0 transition group-hover:opacity-100">Enlarge</span>
                    </button>
                </div>
                <div x-show="zoomOpen" x-cloak class="fixed inset-0 z-[60] flex items-center justify-center p-4" role="dialog" aria-modal="true" aria-label="Product image enlarged">
                    <div class="absolute inset-0 bg-chocolate-900/80" @click="closeZoom()"></div>
                    <div class="relative max-h-[min(92vh,920px)] max-w-[min(92vw,920px)]">
                        <img :src="src" alt="{{ $product->name }}" class="max-h-[min(92vh,920px)] max-w-full rounded-lg object-contain shadow-warm ring-1 ring-white/20" width="920" height="920">
                        <button type="button" class="absolute -right-1 -top-1 inline-flex min-h-[44px] min-w-[44px] items-center justify-center rounded-full bg-white text-lg font-medium text-chocolate-900 shadow-warm ring-1 ring-ivory-200 hover:bg-ivory-50" @click="closeZoom()" aria-label="Close enlarged image">&times;</button>
                    </div>
                </div>
                @if(count($galleryUrls) > 1)
                    <div class="mt-4 flex gap-2 overflow-x-auto pb-1">
                        <template x-for="(url, i) in images" :key="i">
                            <button type="button" @click="select(i)" class="h-16 w-16 shrink-0 overflow-hidden rounded-lg ring-2 transition" :class="current === i ? 'ring-sienna-500' : 'ring-transparent hover:ring-ivory-300'">
                                <img :src="url" alt="" class="h-full w-full object-cover" width="64" height="64">
                            </button>
                        </template>
                    </div>
                @endif
            </div>

            <div>
                <nav class="text-xs text-chocolate-700/75">
                    <a href="{{ route('shop.index') }}" class="hover:text-sienna-600">Shop</a>
                    <span class="mx-1">/</span>
                    @if($product->region)
                        <a href="{{ route('shop.region', $product->region->slug) }}" class="hover:text-sienna-600">{{ $product->region->name }}</a>
                        <span class="mx-1">/</span>
                    @endif
                    <span class="text-chocolate-900">{{ $product->name }}</span>
                </nav>
                <h1 class="mt-3 font-display text-3xl sm:text-4xl font-medium tracking-tight text-chocolate-900">{{ $product->name }}</h1>
                @if($product->subtitle)
                    <p class="mt-2 text-chocolate-800/85">{{ $product->subtitle }}</p>
                @endif
                <div class="mt-6 flex flex-wrap items-baseline gap-3">
                    <p class="text-2xl font-semibold text-chocolate-900">₹{{ number_format($product->price_inr, 0) }}</p>
                    @if($product->compare_at_price_inr && (float) $product->compare_at_price_inr > (float) $product->price_inr)
                        <p class="text-lg text-chocolate-700/60 line-through">₹{{ number_format($product->compare_at_price_inr, 0) }}</p>
                    @endif
                </div>

                <div class="mt-6 flex flex-wrap gap-3" x-data="addToCartBtn({{ $product->id }})">
                    <x-gico.button variant="primary" type="button" @click="add" x-bind:disabled="loading">
                        <span x-show="!loading">Add to cart</span>
                        <span x-show="loading" x-cloak>Adding…</span>
                    </x-gico.button>
                    <x-gico.button variant="secondary" href="{{ route('checkout.show') }}">Buy now</x-gico.button>
                    <p x-show="message" x-text="message" class="w-full text-sm text-emerald-800" x-cloak></p>
                </div>

                @if($product->short_description)
                    <p class="mt-8 text-sm leading-relaxed text-chocolate-800/90">{{ $product->short_description }}</p>
                @endif

                <ul class="mt-8 flex flex-wrap gap-4 text-xs text-chocolate-800/85">
                    <li class="rounded-full border border-ivory-200 bg-white px-3 py-1.5">Free ship &gt; ₹2,000</li>
                    <li class="rounded-full border border-ivory-200 bg-white px-3 py-1.5">Handmade</li>
                    <li class="rounded-full border border-ivory-200 bg-white px-3 py-1.5">7-day return on breakage</li>
                </ul>
            </div>
        </div>

        <div class="mt-16 border-t border-ivory-200 pt-10" x-data="productTabs()">
            <div class="flex flex-wrap gap-2 border-b border-ivory-200 pb-3">
                <button type="button" @click="tab = 'story'" class="min-h-[44px] rounded-lg px-4 text-sm font-medium transition" :class="tab === 'story' ? 'bg-sienna-500 text-white' : 'text-chocolate-800 hover:bg-ivory-100'">The story</button>
                <button type="button" @click="tab = 'inside'" class="min-h-[44px] rounded-lg px-4 text-sm font-medium transition" :class="tab === 'inside' ? 'bg-sienna-500 text-white' : 'text-chocolate-800 hover:bg-ivory-100'">What&apos;s inside</button>
                <button type="button" @click="tab = 'artisan'" class="min-h-[44px] rounded-lg px-4 text-sm font-medium transition" :class="tab === 'artisan' ? 'bg-sienna-500 text-white' : 'text-chocolate-800 hover:bg-ivory-100'">The artisan</button>
                <button type="button" @click="tab = 'shipping'" class="min-h-[44px] rounded-lg px-4 text-sm font-medium transition" :class="tab === 'shipping' ? 'bg-sienna-500 text-white' : 'text-chocolate-800 hover:bg-ivory-100'">Shipping &amp; returns</button>
                <button type="button" @click="tab = 'reviews'" class="min-h-[44px] rounded-lg px-4 text-sm font-medium transition" :class="tab === 'reviews' ? 'bg-sienna-500 text-white' : 'text-chocolate-800 hover:bg-ivory-100'">Reviews</button>
            </div>

            <div class="mt-6 max-w-none space-y-4 text-sm leading-relaxed text-chocolate-800/95 [&_h1]:font-display [&_h1]:text-2xl [&_h1]:text-chocolate-900 [&_h2]:font-display [&_h2]:text-xl [&_h2]:text-chocolate-900 [&_ul]:list-disc [&_ul]:pl-5" x-show="tab === 'story'" x-cloak>
                @if($product->story_md)
                    {!! \Illuminate\Support\Str::markdown($product->story_md) !!}
                @else
                    <p class="text-sm text-chocolate-700/80">Story coming soon.</p>
                @endif
            </div>

            <div class="mt-6 text-sm text-chocolate-800/90" x-show="tab === 'inside'" x-cloak>
                @if($product->is_box && $product->components->isNotEmpty())
                    <ul class="list-disc space-y-2 pl-5">
                        @foreach($product->components as $c)
                            <li>{{ $c->name }} × {{ (float) $c->pivot->quantity }}</li>
                        @endforeach
                    </ul>
                @else
                    <p>This piece ships as a single curated item.</p>
                @endif
            </div>

            <div class="mt-6 text-sm text-chocolate-800/90" x-show="tab === 'artisan'" x-cloak>
                @forelse($product->artisans as $a)
                    <div class="rounded-2xl border border-ivory-200 bg-white p-5 shadow-sm">
                        <a href="{{ route('artisans.show', $a->slug) }}" class="font-display text-lg font-medium text-chocolate-900 hover:text-sienna-600">{{ $a->name }}</a>
                        @if($a->region)
                            <p class="mt-1 text-xs text-chocolate-700/75">{{ $a->region->name }}</p>
                        @endif
                        @if($a->bio)
                            <p class="mt-3 leading-relaxed">{{ $a->bio }}</p>
                        @endif
                    </div>
                @empty
                    <p>Artisan credits will appear here soon.</p>
                @endforelse
            </div>

            <div class="mt-6 space-y-4 text-sm leading-relaxed text-chocolate-800/90" x-show="tab === 'shipping'" x-cloak>
                <p>We ship across India from Udaipur. Flat ₹99 shipping; free above ₹2,000. Most metros arrive in 3–7 business days once dispatched.</p>
                <p>If something arrives damaged, email us within 7 days with photos — we will replace or refund the affected pieces.</p>
            </div>

            <div class="mt-6 space-y-6" x-show="tab === 'reviews'" x-cloak>
                @forelse($product->reviews as $review)
                    <article class="rounded-2xl border border-ivory-200 bg-white p-5 shadow-sm">
                        <div class="flex flex-wrap items-center gap-2">
                            <x-gico.rating-stars :rating="$review->rating" />
                            @if($review->title)
                                <h3 class="font-medium text-chocolate-900">{{ $review->title }}</h3>
                            @endif
                        </div>
                        <p class="mt-2 text-sm leading-relaxed text-chocolate-800/90">{{ $review->body }}</p>
                        <p class="mt-2 text-xs text-chocolate-700/60">{{ $review->user?->name ?? $review->guest_name ?? 'Verified buyer' }}</p>
                    </article>
                @empty
                    <p class="text-sm text-chocolate-700/80">No reviews yet — be the first after your order arrives.</p>
                @endforelse
            </div>
        </div>

        @if($relatedProducts->isNotEmpty())
            <section class="mt-16 border-t border-ivory-200 pt-12">
                <h2 class="font-display text-xl font-medium text-chocolate-900">You may also like</h2>
                <div class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach($relatedProducts as $rp)
                        @include('frontend.partials.product-card', ['product' => $rp])
                    @endforeach
                </div>
            </section>
        @endif
    </div>
@endsection
