<a href="{{ route('stories.show', $story->slug) }}" class="group block">
    <div class="aspect-[4/5] rounded-2xl overflow-hidden mb-5">
        <img src="{{ $story->cover_image }}" alt="{{ $story->title }}"
             class="w-full h-full object-cover transition-transform duration-700 ease-[cubic-bezier(0.22,1,0.36,1)] group-hover:scale-[1.03]"
             loading="lazy">
    </div>
    <p class="gico-overline text-chocolate-800/60">
        {{ $story->category ?? 'JOURNAL' }}{{ $story->read_time ? ' · '.$story->read_time.' MIN READ' : '' }}
    </p>
    <h3 class="mt-2 font-display text-2xl font-medium text-chocolate-900 group-hover:text-sienna-600 transition leading-snug">{{ $story->title }}</h3>
    @if($story->excerpt)
      <p class="mt-2 text-[15px] leading-relaxed text-chocolate-800/80 line-clamp-2">{{ $story->excerpt }}</p>
    @endif
</a>
