{{-- Canva-pattern zone: title + 3-feature + visual --}}
<section class="py-20 md:py-28 bg-ivory-50">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">
    {{-- LEFT: headline + 3 feature rows --}}
    <div>
      <p class="gico-overline text-sienna-600">WHAT'S INSIDE</p>
      <h2 class="mt-5 font-display text-4xl lg:text-5xl font-normal tracking-tight text-chocolate-900 leading-[1.1]">
        Everything you need<br />for a <em class="italic font-normal">proper gift.</em>
      </h2>

      <div class="mt-10 space-y-8">
        {{-- Feature row 1 --}}
        <div class="flex gap-5">
          <div class="shrink-0 w-14 h-14 rounded-xl bg-ivory-100 flex items-center justify-center text-sienna-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 11.25v8.25a1.5 1.5 0 0 1-1.5 1.5H5.25a1.5 1.5 0 0 1-1.5-1.5v-8.25M12 4.875A2.625 2.625 0 1 0 9.375 7.5H12m0-2.625V7.5m0-2.625A2.625 2.625 0 1 1 14.625 7.5H12m0 0V21m-8.625-9.75h18c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125h-18c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z"/></svg>
          </div>
          <div>
            <h3 class="font-display text-xl font-medium text-chocolate-900">Hand-packed in Udaipur</h3>
            <p class="mt-2 text-[15px] leading-relaxed text-chocolate-800/80">Every box is assembled by our small team in Udaipur. No conveyor belts, no warehouses abroad.</p>
          </div>
        </div>
        {{-- Feature row 2 --}}
        <div class="flex gap-5">
          <div class="shrink-0 w-14 h-14 rounded-xl bg-ivory-100 flex items-center justify-center text-sienna-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"/></svg>
          </div>
          <div>
            <h3 class="font-display text-xl font-medium text-chocolate-900">A story for every piece</h3>
            <p class="mt-2 text-[15px] leading-relaxed text-chocolate-800/80">Each box ships with a printed story card — who made the pieces, where, and how long they took.</p>
          </div>
        </div>
        {{-- Feature row 3 --}}
        <div class="flex gap-5">
          <div class="shrink-0 w-14 h-14 rounded-xl bg-ivory-100 flex items-center justify-center text-sienna-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9-1.5h12m-12 0a1.5 1.5 0 0 1-1.5-1.5V7.5m15 7.5v1.5m-15-9V4.5A1.5 1.5 0 0 1 3 3h12a1.5 1.5 0 0 1 1.5 1.5V6m0 0h1.584c.535 0 1.046.211 1.424.586l2.906 2.906c.378.378.586.889.586 1.424V15a1.5 1.5 0 0 1-1.5 1.5h-1.5m0-10.5V6"/></svg>
          </div>
          <div>
            <h3 class="font-display text-xl font-medium text-chocolate-900">Delivered in 3–7 days</h3>
            <p class="mt-2 text-[15px] leading-relaxed text-chocolate-800/80">Across India, flat ₹99 — free above ₹2,000. International shipping at checkout.</p>
          </div>
        </div>
      </div>

      <div class="mt-12">
        <a href="{{ route('shop.boxes') }}" class="inline-flex items-center gap-2 min-h-[48px] px-7 rounded-lg bg-sienna-500 text-ivory-50 text-[15px] font-medium hover:bg-sienna-600 transition">
          Shop the boxes
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
        </a>
      </div>
    </div>

    {{-- RIGHT: hero visual --}}
    <div class="relative">
      <div class="aspect-[4/5] rounded-2xl overflow-hidden">
        <img src="{{ $explainerImage ?? $heroImage }}" alt="A GicoGifts box being assembled in Udaipur" class="w-full h-full object-cover" loading="lazy">
      </div>
    </div>
  </div>
</section>
