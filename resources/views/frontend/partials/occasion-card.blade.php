<a href="{{ route('shop.occasion', $occasion->slug) }}" class="group flex min-h-[44px] flex-col justify-center rounded-2xl border border-ivory-200 bg-white p-5 shadow-sm transition hover:border-sienna-200/60 hover:shadow-warm">
    <span class="font-display text-lg font-medium text-chocolate-900 group-hover:text-sienna-600">{{ $occasion->name }}</span>
    <span class="mt-3 text-xs font-medium uppercase tracking-wide text-sienna-600">Shop occasion →</span>
</a>
