<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Checkout — '.config('app.name'))</title>
    <script>
        window.ggRoutes = window.ggRoutes || {};
        window.ggRoutes.checkoutPlace = @json(route('checkout.place'));
        window.ggRoutes.checkoutServiceability = @json(route('checkout.serviceability'));
        window.ggRazorpayKey = @json(config('services.razorpay.key_id'));
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full bg-ivory-50 font-sans text-chocolate-900 antialiased">
    <header class="border-b border-ivory-200 bg-white/90">
        <div class="max-w-3xl mx-auto px-4 py-5 flex items-center justify-between">
            <a href="{{ route('home') }}" class="font-display text-xl font-medium text-chocolate-900">{{ config('app.name') }}</a>
            <a href="{{ route('cart.show') }}" class="text-sm font-medium text-sienna-600 hover:text-sienna-700">Cart</a>
        </div>
    </header>
    @if (session('status'))
        <div class="max-w-3xl mx-auto px-4 pt-4">
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">{{ session('status') }}</div>
        </div>
    @endif
    @if ($errors->any())
        <div class="max-w-3xl mx-auto px-4 pt-4 space-y-1">
            @foreach($errors->all() as $err)
                <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-900">{{ $err }}</div>
            @endforeach
        </div>
    @endif
    <main class="max-w-3xl mx-auto px-4 py-10">
        @yield('content')
    </main>
</body>
</html>
