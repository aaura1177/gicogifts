<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name'))</title>
    @include('partials.seo-head')
    @stack('meta')
    <script>
        window.ggRoutes = {
            cartAdd: @json(route('cart.add')),
            gigiChat: @json(route('gigi.chat')),
        };
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full bg-ivory-50 font-sans text-chocolate-900 antialiased" x-data="cartDrawer({{ $storeCart->itemCount() }})" @keydown.escape.window="closeOverlays()">
    @include('frontend.partials.announcement-bar')
    <div class="sticky top-0 z-40 border-b border-ivory-200/80 bg-ivory-50/95 shadow-sm backdrop-blur">
        @include('frontend.partials.nav')
    </div>
    @if (session('status'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
            <div class="rounded-xl border border-emerald-200/80 bg-emerald-50 px-4 py-3 text-sm text-emerald-900 shadow-warm">{{ session('status') }}</div>
        </div>
    @endif
    <main>
        @yield('content')
    </main>
    @include('frontend.partials.footer')
    @include('frontend.partials.cart-drawer')
    @include('frontend.partials.gigi')
    @stack('scripts')
</body>
</html>
