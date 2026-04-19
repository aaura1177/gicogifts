<a href="{{ route('artisans.show', $artisan->slug) }}" class="group flex flex-col rounded-2xl border border-ivory-200 bg-white p-6 shadow-sm transition hover:border-sienna-200/60 hover:shadow-warm">
    <div class="flex items-start gap-4">
        <div class="h-16 w-16 shrink-0 overflow-hidden rounded-full border border-ivory-200 bg-ivory-100">
            @if($artisan->photo_path)
                <img src="{{ $artisan->photo_path }}" alt="" class="h-full w-full object-cover" width="64" height="64" loading="lazy">
            @else
                <div class="flex h-full w-full items-center justify-center font-display text-xl text-chocolate-700/35">{{ \Illuminate\Support\Str::substr($artisan->name, 0, 1) }}</div>
            @endif
        </div>
        <div class="min-w-0">
            <h2 class="font-display text-lg font-medium text-chocolate-900 group-hover:text-sienna-600">{{ $artisan->name }}</h2>
            @if($artisan->region)
                <p class="mt-0.5 text-xs text-chocolate-700/70">{{ $artisan->region->name }}</p>
            @endif
        </div>
    </div>
    @if($artisan->bio)
        <p class="mt-4 text-sm leading-relaxed text-chocolate-800/85 line-clamp-3">{{ $artisan->bio }}</p>
    @endif
</a>
