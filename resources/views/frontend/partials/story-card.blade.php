<a href="{{ route('stories.show', $story->slug) }}" class="group flex flex-col overflow-hidden rounded-2xl border border-ivory-200 bg-white shadow-sm transition hover:shadow-warm {{ $compact ?? false ? 'md:flex-row' : '' }}">
    @if($story->cover_image)
        <div class="{{ ($compact ?? false) ? 'aspect-video md:w-2/5 shrink-0' : 'aspect-[16/10]' }} overflow-hidden bg-ivory-100">
            <img src="{{ $story->cover_image }}" alt="" class="h-full w-full object-cover transition duration-300 group-hover:scale-[1.02]" width="640" height="400" loading="lazy">
        </div>
    @endif
    <div class="flex flex-1 flex-col p-5 md:min-w-0">
        <h2 class="font-display text-lg font-medium text-chocolate-900 group-hover:text-sienna-600">{{ $story->title }}</h2>
        <p class="mt-2 flex-1 text-sm text-chocolate-800/80 line-clamp-3">{{ $story->excerpt }}</p>
        <p class="mt-4 text-xs font-medium text-sienna-600">Read story →</p>
    </div>
</a>
