@php
    $wrapperClass = $wrapperClass ?? '';
@endphp
<section class="rounded-3xl border border-ivory-200 bg-white px-6 py-10 sm:px-10 shadow-sm{{ ($wrapperClass ?? '') !== '' ? ' '.$wrapperClass : '' }}">
    <h2 class="font-display text-xl font-medium text-chocolate-900">{{ $heading ?? 'Notes from the packing table' }}</h2>
    <p class="mt-2 text-sm text-chocolate-800/85">{{ $subheading ?? 'Occasional email: new boxes, artisan visits, and quiet shop updates. No spam.' }}</p>
    <form method="post" action="{{ route('newsletter.subscribe') }}" class="mt-6 flex flex-col sm:flex-row gap-3 max-w-lg">
        @csrf
        <x-gico.input name="email" type="email" required placeholder="you@example.com" class="flex-1 w-full" />
        <x-gico.button variant="primary" type="submit" class="shrink-0">Subscribe</x-gico.button>
    </form>
</section>
