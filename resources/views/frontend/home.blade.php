@extends('layouts.app')

@section('title', config('app.name').' — Rajasthan, unboxed.')
@section('meta_description', 'Curated artisan gift boxes from Rajasthan — handmade pieces, story-led gifting, and flat ₹99 shipping (free above ₹2,000).')

@section('content')

  {{-- HERO — escapes container for full-bleed split --}}
  <section class="relative">
    <div class="grid lg:grid-cols-[1.1fr_1fr] min-h-[78vh]">
      <div class="order-2 lg:order-1 flex items-center px-6 sm:px-10 lg:px-16 py-16 lg:py-24 bg-ivory-50">
        <div class="max-w-lg">
          <p class="gico-overline text-sienna-600 animate-fade-up stagger-1">FROM RAJASTHAN · UDAIPUR</p>
          <h1 class="mt-5 font-display text-display-xxl text-chocolate-900 animate-fade-up stagger-2">
            Rajasthan,<br /><em class="italic">unboxed.</em>
          </h1>
          <p class="mt-6 text-lg leading-relaxed text-chocolate-800/85 animate-fade-up stagger-3">
            Curated gift boxes of the region's quiet crafts — block prints from Sanganer, Pichwai from Nathdwara, soapstone from Banswara. Hand-assembled in Udaipur.
          </p>
          <div class="mt-10 flex flex-wrap gap-3 animate-fade-up stagger-4">
            <a href="{{ route('shop.boxes') }}" class="btn-press inline-flex items-center gap-2 min-h-[48px] px-7 rounded-lg bg-sienna-500 text-ivory-50 text-[15px] font-medium hover:bg-sienna-600 transition">
              Shop the boxes
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
            </a>
            <a href="{{ route('artisans.index') }}" class="btn-press inline-flex items-center min-h-[48px] px-7 rounded-lg border border-chocolate-900 text-chocolate-900 text-[15px] font-medium hover:bg-chocolate-900 hover:text-ivory-50 transition">Meet the artisans</a>
          </div>
        </div>
      </div>
      <div class="order-1 lg:order-2 relative min-h-[420px] lg:min-h-full">
        <img src="{{ $heroImage }}" alt="A Rajasthan gift box being hand-packed in Udaipur"
             class="absolute inset-0 w-full h-full object-cover"
             width="1600" height="1200" fetchpriority="high">
      </div>
    </div>
  </section>

  {{-- QUIET TRUST STRIP --}}
  <section class="border-y border-ivory-200 bg-ivory-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 grid sm:grid-cols-3 gap-6 text-center">
      <p class="text-sm text-chocolate-800/80"><span class="font-medium text-chocolate-900">Free shipping</span> above ₹2,000</p>
      <p class="text-sm text-chocolate-800/80"><span class="font-medium text-chocolate-900">Hand-packed</span> in Udaipur</p>
      <p class="text-sm text-chocolate-800/80"><span class="font-medium text-chocolate-900">7-day</span> breakage guarantee</p>
    </div>
  </section>

  {{-- FEATURED BOXES --}}
  <section class="py-20 md:py-28 bg-ivory-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <x-gico.section-header
        overline="THE COLLECTION"
        :headline="'Five boxes,<br><em class=\'italic\'>five Rajasthans.</em>'"
        subtitle="Our launch collection. Each box its own story, each piece credited to the hands that made it."
        actionText="View all five"
        :actionHref="route('shop.boxes')" />

      @if($featuredBoxes->isNotEmpty())
        <div class="mt-12 md:mt-16 grid sm:grid-cols-2 lg:grid-cols-3 gap-8 lg:gap-10">
          @foreach($featuredBoxes->take(3) as $product)
            @include('frontend.partials.product-card', ['product' => $product, 'quickAdd' => true])
          @endforeach
        </div>
      @endif
    </div>
  </section>

  {{-- EDITORIAL QUOTE BREAK --}}
  <section class="py-24 md:py-32 bg-ivory-50">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 text-center">
      <x-gico.ornamental-divider />
      <p class="font-display text-display-md italic text-chocolate-800 mt-8" style="font-weight:300;">
        "A gift should feel like it came from somewhere."
      </p>
      <p class="gico-overline text-chocolate-800/60 mt-8">— FOUNDER'S NOTE</p>
    </div>
  </section>

  {{-- CANVA-PATTERN EXPLAINER --}}
  @include('frontend.partials.canva-explainer')

  {{-- SHOP BY OCCASION --}}
  <section class="py-20 md:py-28 bg-ivory-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <x-gico.section-header
        overline="BY OCCASION"
        :headline="'Find the <em class=\'italic\'>right one.</em>'" />
      <div class="mt-12 md:mt-16 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        @foreach($occasions as $occasion)
          @include('frontend.partials.occasion-card', ['occasion' => $occasion])
        @endforeach
      </div>
    </div>
  </section>

  {{-- CRAFT REGIONS --}}
  <section class="py-20 md:py-28 bg-ivory-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <x-gico.section-header
        overline="THE PLACES"
        :headline="'Three regions.<br><em class=\'italic\'>One spirit.</em>'"
        center />
      <div class="mt-12 md:mt-16 grid md:grid-cols-3 gap-6 lg:gap-8">
        @foreach($regions as $region)
          <a href="{{ route('shop.region', $region->slug) }}" class="group block">
            <div class="aspect-[4/5] rounded-2xl overflow-hidden mb-5">
              <img src="{{ $region->hero_image ?? 'https://placehold.co/600x750/ECDBC4/6D3620?text='.rawurlencode($region->name) }}"
                   alt="{{ $region->name }}"
                   class="w-full h-full object-cover transition-transform duration-700 ease-[cubic-bezier(0.22,1,0.36,1)] group-hover:scale-[1.04]"
                   loading="lazy">
            </div>
            <p class="gico-overline text-chocolate-800/60">{{ $region->tagline ?? 'CRAFT REGION' }}</p>
            <h3 class="mt-2 font-display text-2xl font-medium text-chocolate-900 group-hover:text-sienna-600 transition">{{ $region->name }}</h3>
            @if($region->description)
              <p class="mt-2 text-[15px] leading-relaxed text-chocolate-800/80 line-clamp-3">{{ $region->description }}</p>
            @endif
          </a>
        @endforeach
      </div>
    </div>
  </section>

  {{-- HOW IT WORKS --}}
  <section class="py-20 md:py-28 bg-ivory-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <x-gico.section-header
        overline="HOW IT WORKS"
        :headline="'<em class=\'italic\'>Slowly,</em> and by hand.'"
        center />
      <div class="mt-14 md:mt-20 grid md:grid-cols-4 gap-10">
        @foreach(['Choose a box' => 'Pick from the collection, or build one around your occasion.',
                 'We assemble in Udaipur' => 'Each piece is sourced, inspected and packed by our small team.',
                 'Hand-packed with a story card' => 'Every box carries a printed note about who made what\'s inside.',
                 'Delivered in 3–7 days' => 'Across India. International shipping available at checkout.'] as $step => $desc)
          <div>
            <div class="w-14 h-14 rounded-xl bg-ivory-100 flex items-center justify-center mb-5 text-sienna-600">
              <span class="gico-overline text-sienna-600">0{{ $loop->iteration }}</span>
            </div>
            <h4 class="font-display text-xl font-medium text-chocolate-900">{{ $step }}</h4>
            <p class="mt-2 text-[15px] leading-relaxed text-chocolate-800/80">{{ $desc }}</p>
          </div>
        @endforeach
      </div>
    </div>
  </section>

  {{-- STORIES TEASER --}}
  @isset($featuredStories)
    @if($featuredStories->isNotEmpty())
    <section class="py-20 md:py-28 bg-ivory-100">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <x-gico.section-header
          overline="THE JOURNAL"
          :headline="'From the <em class=\'italic\'>journal.</em>'"
          actionText="All stories"
          :actionHref="route('stories.index')" />
        <div class="mt-12 md:mt-16 grid md:grid-cols-3 gap-8">
          @foreach($featuredStories->take(3) as $story)
            @include('frontend.partials.story-card', ['story' => $story])
          @endforeach
        </div>
      </div>
    </section>
    @endif
  @endisset

  {{-- NEWSLETTER (dark closing chapter) --}}
  <section class="py-24 md:py-32 bg-chocolate-900 text-ivory-50">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 text-center">
      <p class="gico-overline text-gold-400">THE LETTER</p>
      <h2 class="mt-4 font-display text-display-md">Slow news<br /><em class="italic">from Rajasthan.</em></h2>
      <p class="mt-5 text-lg leading-relaxed text-ivory-200/80">A letter every other Sunday — what we're sourcing, who we're meeting, what's being made.</p>
      <form action="{{ route('newsletter.subscribe') }}" method="POST" class="mt-10 flex flex-col sm:flex-row gap-3 max-w-md mx-auto">
        @csrf
        <input type="email" name="email" required placeholder="your@email.com"
               class="flex-1 min-h-[48px] px-5 rounded-lg bg-chocolate-800 border border-chocolate-700 text-ivory-50 placeholder:text-ivory-200/50 focus:border-gold-500 focus:outline-none">
        <button type="submit" class="btn-press min-h-[48px] px-7 rounded-lg bg-sienna-500 text-ivory-50 text-[15px] font-medium hover:bg-sienna-400 transition">Subscribe</button>
      </form>
      <p class="mt-4 text-xs text-ivory-200/50">No spam. Unsubscribe anytime.</p>
    </div>
  </section>

@endsection
