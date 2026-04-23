<a href="{{ route('artisans.show', $artisan->slug) }}" class="group block text-center">
    <div class="w-32 h-32 md:w-40 md:h-40 mx-auto rounded-full overflow-hidden mb-5">
        <img src="{{ $artisan->photo_url ?? 'https://placehold.co/400x400/ECDBC4/6D3620?text='.rawurlencode($artisan->name) }}"
             alt="{{ $artisan->name }}"
             class="w-full h-full object-cover transition-transform duration-500 ease-[cubic-bezier(0.22,1,0.36,1)] group-hover:scale-[1.04]"
             loading="lazy">
    </div>
    <h3 class="font-display text-xl font-medium text-chocolate-900 group-hover:text-sienna-600 transition">{{ $artisan->name }}</h3>
    <p class="mt-1 gico-overline text-chocolate-800/60">{{ $artisan->craft }} · {{ $artisan->region?->name }}</p>
</a>
