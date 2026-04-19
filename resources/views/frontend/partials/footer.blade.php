@php
    $social = config('gicogifts.social', []);
    $legal = trim((string) config('gicogifts.legal_line', ''));
@endphp
<footer class="mt-16 border-t border-ivory-200 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24 grid gap-10 sm:grid-cols-2 lg:grid-cols-4 text-sm text-chocolate-800/90">
        <div>
            <p class="font-display text-lg font-medium text-chocolate-900">{{ config('app.name') }}</p>
            <p class="mt-3 leading-relaxed">Premium artisan gift boxes from Rajasthan — refined luxury, warm craft.</p>
            @if(array_filter($social))
                <div class="mt-5 flex flex-wrap gap-3">
                    @if(!empty($social['instagram']))
                        <a href="{{ $social['instagram'] }}" class="inline-flex min-h-[44px] min-w-[44px] items-center justify-center rounded-lg border border-ivory-200 text-chocolate-800 hover:border-sienna-300 hover:text-sienna-600" rel="noopener noreferrer" target="_blank" aria-label="Instagram">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M7.8 2h8.4C19.4 2 22 4.6 22 7.8v8.4a5.8 5.8 0 0 1-5.8 5.8H7.8C4.6 22 2 19.4 2 16.2V7.8A5.8 5.8 0 0 1 7.8 2m-.2 2A3.6 3.6 0 0 0 4 7.6v8.8C4 18.39 5.61 20 7.6 20h8.8a3.6 3.6 0 0 0 3.6-3.6V7.6C20 5.61 18.39 4 16.4 4H7.6m9.65 1.5a1.25 1.25 0 0 1 1.25 1.25A1.25 1.25 0 0 1 17.25 8 1.25 1.25 0 0 1 16 6.75a1.25 1.25 0 0 1 1.25-1.25M12 7a5 5 0 0 1 5 5 5 5 0 0 1-5 5 5 5 0 0 1-5-5 5 5 0 0 1 5-5m0 2a3 3 0 0 0-3 3 3 3 0 0 0 3 3 3 3 0 0 0 3-3 3 3 0 0 0-3-3z"/></svg>
                        </a>
                    @endif
                    @if(!empty($social['facebook']))
                        <a href="{{ $social['facebook'] }}" class="inline-flex min-h-[44px] min-w-[44px] items-center justify-center rounded-lg border border-ivory-200 text-chocolate-800 hover:border-sienna-300 hover:text-sienna-600" rel="noopener noreferrer" target="_blank" aria-label="Facebook">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M22 12c0-5.52-4.48-10-10-10S2 6.48 2 12c0 4.84 3.44 8.87 8 9.8V15H8v-3h2v-2.5C10 7.57 11.57 6 13.5 6H16v3h-1.5c-.83 0-1 .47-1 1.2V12h3l-.5 3H14v7.95c4.56-.93 8-4.96 8-9.95z"/></svg>
                        </a>
                    @endif
                    @if(!empty($social['linkedin']))
                        <a href="{{ $social['linkedin'] }}" class="inline-flex min-h-[44px] min-w-[44px] items-center justify-center rounded-lg border border-ivory-200 text-chocolate-800 hover:border-sienna-300 hover:text-sienna-600" rel="noopener noreferrer" target="_blank" aria-label="LinkedIn">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.32 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 0 1 1.4 1.4v4.93h2.79M6.88 8.56a1.68 1.68 0 0 0 1.68-1.68c0-.93-.75-1.69-1.68-1.69a1.69 1.69 0 0 0-1.69 1.69c0 .93.76 1.68 1.69 1.68m1.39 9.94v-8.37H5.5v8.37h2.77z"/></svg>
                        </a>
                    @endif
                    @if(!empty($social['youtube']))
                        <a href="{{ $social['youtube'] }}" class="inline-flex min-h-[44px] min-w-[44px] items-center justify-center rounded-lg border border-ivory-200 text-chocolate-800 hover:border-sienna-300 hover:text-sienna-600" rel="noopener noreferrer" target="_blank" aria-label="YouTube">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M10 15l5.19-3L10 9v6m11.56-7.83c.13.47.22 1.1.28 1.9.07.8.1 1.49.1 2.09L22 12c0 2.19-.16 3.8-.44 4.83-.25.9-.83 1.48-1.73 1.73-.95.3-2.51.44-4.66.44H8.83c-2.15 0-3.71-.14-4.66-.43-.9-.25-1.48-.84-1.73-1.73-.3-.95-.44-2.51-.44-4.66 0-2.15.14-3.71.44-4.66.25-.9.83-1.48 1.73-1.73C5.12 4.14 6.68 4 8.83 4h6.34c2.15 0 3.71.14 4.66.44.9.25 1.48.83 1.73 1.73z"/></svg>
                        </a>
                    @endif
                </div>
            @endif
        </div>
        <div>
            <p class="font-medium text-chocolate-900">Shop</p>
            <ul class="mt-3 space-y-2">
                <li><a href="{{ route('shop.boxes') }}" class="hover:text-sienna-600">Gift boxes</a></li>
                <li><a href="{{ route('shop.individual') }}" class="hover:text-sienna-600">Individual pieces</a></li>
                <li><a href="{{ route('shop.index') }}" class="hover:text-sienna-600">All products</a></li>
            </ul>
        </div>
        <div>
            <p class="font-medium text-chocolate-900">Help</p>
            <ul class="mt-3 space-y-2">
                <li><a href="{{ route('faq') }}" class="hover:text-sienna-600">FAQ</a></li>
                <li><a href="{{ route('contact') }}" class="hover:text-sienna-600">Contact</a></li>
                <li><a href="{{ route('corporate') }}" class="hover:text-sienna-600">Corporate gifting</a></li>
            </ul>
        </div>
        <div>
            <p class="font-medium text-chocolate-900">Legal</p>
            <ul class="mt-3 space-y-2">
                <li><a href="{{ route('privacy-policy') }}" class="hover:text-sienna-600">Privacy</a></li>
                <li><a href="{{ route('terms') }}" class="hover:text-sienna-600">Terms</a></li>
                <li><a href="{{ route('shipping-policy') }}" class="hover:text-sienna-600">Shipping</a></li>
                <li><a href="{{ route('refund-policy') }}" class="hover:text-sienna-600">Refunds</a></li>
            </ul>
        </div>
    </div>

    <div class="border-t border-ivory-200 bg-ivory-50/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <p class="text-center text-xs font-medium uppercase tracking-wide text-chocolate-800/70">We accept</p>
            <div class="mt-4">
                @include('frontend.partials.payment-methods')
            </div>
            <ul class="mt-8 flex flex-wrap items-center justify-center gap-x-8 gap-y-3 text-xs text-chocolate-800/80">
                <li class="flex items-center gap-2">
                    <svg class="h-5 w-5 shrink-0 text-sienna-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    Secure checkout
                </li>
                <li class="flex items-center gap-2">
                    <svg class="h-5 w-5 shrink-0 text-sienna-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Hand-packed in India
                </li>
                <li class="flex items-center gap-2">
                    <svg class="h-5 w-5 shrink-0 text-sienna-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Artisan-first sourcing
                </li>
            </ul>
        </div>
    </div>

    <div class="border-t border-ivory-100 py-4 px-4 text-center text-xs text-chocolate-700/70 space-y-1">
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}</p>
        @if($legal !== '')
            <p class="text-chocolate-700/60">{{ $legal }}</p>
        @endif
    </div>
</footer>
