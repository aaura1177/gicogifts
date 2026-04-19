<header class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex h-[60px] md:h-[72px] items-center justify-between gap-4">
        <a href="{{ route('home') }}" class="font-display text-xl md:text-2xl font-medium tracking-tight text-chocolate-900">{{ config('app.name') }}</a>
        <nav class="hidden lg:flex items-center gap-8 text-sm font-medium text-chocolate-800">
            <a href="{{ route('shop.index') }}" class="hover:text-sienna-600">Shop</a>
            <a href="{{ route('shop.boxes') }}" class="hover:text-sienna-600">Gift Boxes</a>
            <a href="{{ route('stories.index') }}" class="hover:text-sienna-600">Stories</a>
            <a href="{{ route('artisans.index') }}" class="hover:text-sienna-600">Artisans</a>
            <a href="{{ route('about') }}" class="hover:text-sienna-600">About</a>
        </nav>
        <div class="flex items-center gap-2 sm:gap-3">
            <button type="button" class="relative inline-flex min-h-[44px] min-w-[44px] items-center justify-center rounded-lg border border-ivory-200 bg-white px-3 text-sm font-medium text-chocolate-900 shadow-sm hover:bg-ivory-50" @click="openCart()" aria-label="Open cart">
                <span class="hidden sm:inline">Cart</span>
                <span class="sm:hidden">Bag</span>
                <span x-show="cartCount > 0" x-cloak class="absolute -top-1 -right-1 flex h-5 min-w-[1.25rem] items-center justify-center rounded-full bg-sienna-500 px-1 text-xs font-medium text-white" x-text="cartCount"></span>
            </button>
            <a href="{{ route('cart.show') }}" class="hidden md:inline-flex min-h-[44px] items-center text-sm font-medium text-chocolate-700 hover:text-sienna-600">View cart</a>
            @auth
                <a href="{{ route('account.dashboard') }}" class="inline-flex min-h-[44px] min-w-[44px] items-center justify-center rounded-lg px-2 text-sm font-medium text-chocolate-800 hover:text-sienna-600">Account</a>
            @else
                <a href="{{ route('login') }}" class="inline-flex min-h-[44px] min-w-[44px] items-center justify-center rounded-lg px-2 text-sm font-medium text-chocolate-800 hover:text-sienna-600">Log in</a>
            @endauth
            <button type="button" class="inline-flex lg:hidden min-h-[44px] min-w-[44px] items-center justify-center rounded-lg border border-ivory-200 text-chocolate-900" @click="toggleMobileNav()" aria-label="Menu">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
        </div>
    </div>
    <div x-show="mobileNav" x-cloak x-transition class="lg:hidden border-t border-ivory-200 py-3 space-y-1">
        <a href="{{ route('shop.index') }}" @click="closeMobileNav()" class="block min-h-[44px] px-2 py-2 text-sm font-medium text-chocolate-800">Shop</a>
        <a href="{{ route('shop.boxes') }}" @click="closeMobileNav()" class="block min-h-[44px] px-2 py-2 text-sm font-medium text-chocolate-800">Gift Boxes</a>
        <a href="{{ route('stories.index') }}" @click="closeMobileNav()" class="block min-h-[44px] px-2 py-2 text-sm font-medium text-chocolate-800">Stories</a>
        <a href="{{ route('artisans.index') }}" @click="closeMobileNav()" class="block min-h-[44px] px-2 py-2 text-sm font-medium text-chocolate-800">Artisans</a>
        <a href="{{ route('about') }}" @click="closeMobileNav()" class="block min-h-[44px] px-2 py-2 text-sm font-medium text-chocolate-800">About</a>
    </div>
</header>
