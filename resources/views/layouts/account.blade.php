<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Account — '.config('app.name'))</title>
    <script>
        window.ggRoutes = @json(['cartAdd' => route('cart.add'), 'gigiChat' => route('gigi.chat')]);
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full bg-ivory-50 font-sans text-chocolate-900 antialiased" x-data="cartDrawer({{ $storeCart->itemCount() }})" @keydown.escape.window="closeOverlays()">
    @include('frontend.partials.announcement-bar')
    <div class="sticky top-0 z-40 border-b border-ivory-200/80 bg-ivory-50/95 backdrop-blur">
        @include('frontend.partials.nav')
    </div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 lg:flex lg:gap-10">
        <aside class="lg:w-56 shrink-0 mb-8 lg:mb-0">
            <nav class="rounded-2xl border border-ivory-200 bg-white p-4 shadow-warm space-y-1 text-sm">
                <a href="{{ route('account.dashboard') }}" class="block rounded-lg px-3 py-2 font-medium {{ request()->routeIs('account.dashboard') ? 'bg-ivory-100 text-chocolate-900' : 'text-chocolate-700 hover:bg-ivory-50' }}">Overview</a>
                <a href="{{ route('account.orders') }}" class="block rounded-lg px-3 py-2 font-medium {{ request()->routeIs('account.orders') || request()->routeIs('account.order.show') ? 'bg-ivory-100 text-chocolate-900' : 'text-chocolate-700 hover:bg-ivory-50' }}">Orders</a>
                <a href="{{ route('account.addresses') }}" class="block rounded-lg px-3 py-2 font-medium {{ request()->routeIs('account.addresses') ? 'bg-ivory-100 text-chocolate-900' : 'text-chocolate-700 hover:bg-ivory-50' }}">Addresses</a>
                <a href="{{ route('account.wishlist') }}" class="block rounded-lg px-3 py-2 font-medium {{ request()->routeIs('account.wishlist') ? 'bg-ivory-100 text-chocolate-900' : 'text-chocolate-700 hover:bg-ivory-50' }}">Wishlist</a>
                <a href="{{ route('profile.edit') }}" class="block rounded-lg px-3 py-2 font-medium text-chocolate-700 hover:bg-ivory-50">Profile</a>
            </nav>
        </aside>
        <div class="flex-1 min-w-0">
            @yield('content')
        </div>
    </div>
    @include('frontend.partials.footer')
    @include('frontend.partials.cart-drawer')
    @include('frontend.partials.gigi')
    @stack('scripts')
</body>
</html>
