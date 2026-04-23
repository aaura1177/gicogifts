@php
    $img = $occasion->hero_image ?? 'https://placehold.co/600x750/ECDBC4/6D3620?text='.rawurlencode($occasion->name);
@endphp
<a href="{{ route('shop.occasion', $occasion->slug) }}"
   class="group relative aspect-[4/5] rounded-2xl overflow-hidden block">
    <img src="{{ $img }}" alt="{{ $occasion->name }} gifting"
         class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 ease-[cubic-bezier(0.22,1,0.36,1)] group-hover:scale-[1.05]"
         loading="lazy">
    {{-- Gradient for text legibility --}}
    <div class="absolute inset-0 bg-gradient-to-t from-chocolate-900/85 via-chocolate-900/10 to-transparent"></div>
    <div class="absolute bottom-0 left-0 right-0 p-5 md:p-6">
        <h3 class="font-display text-xl md:text-2xl font-medium text-ivory-50 leading-tight">{{ $occasion->name }}</h3>
    </div>
</a>
