@extends('layouts.app')

@section('title', ($story->meta_title ?: $story->title).' — '.config('app.name'))

@section('meta_description', $story->meta_description ?: \Illuminate\Support\Str::limit(strip_tags((string) $story->excerpt), 158))

@section('canonical', route('stories.show', $story->slug, true))

@section('og_type', 'article')

@section('og_image', $story->cover_image ?: url(asset('images/og-default.svg')))

@php
    $storyUrl = route('stories.show', $story->slug, true);
    $cover = $story->cover_image ?: url(asset('images/og-default.svg'));
    $published = $story->published_at?->toAtomString();
    $articleLd = [
        '@context' => 'https://schema.org',
        '@graph' => [
            [
                '@type' => 'Article',
                'headline' => $story->title,
                'description' => trim((string) ($story->meta_description ?: $story->excerpt)),
                'url' => $storyUrl,
                'image' => [$cover],
                'datePublished' => $published,
                'dateModified' => $story->updated_at?->toAtomString(),
                'author' => [
                    '@type' => 'Organization',
                    'name' => config('gicogifts.organization.name'),
                ],
                'publisher' => [
                    '@type' => 'Organization',
                    'name' => config('gicogifts.organization.name'),
                    'url' => rtrim((string) config('app.url'), '/'),
                ],
            ],
            [
                '@type' => 'BreadcrumbList',
                'itemListElement' => [
                    ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => url('/')],
                    ['@type' => 'ListItem', 'position' => 2, 'name' => 'Stories', 'item' => route('stories.index', [], true)],
                    ['@type' => 'ListItem', 'position' => 3, 'name' => $story->title, 'item' => $storyUrl],
                ],
            ],
        ],
    ];
@endphp

@push('jsonld')
    <script type="application/ld+json">{!! json_encode($articleLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS) !!}</script>
@endpush

@section('content')
    <article class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10 md:py-14">
        <nav class="text-xs text-chocolate-700/80">
            <a href="{{ route('stories.index') }}" class="hover:text-sienna-600">Stories</a>
            <span class="mx-1">/</span>
            <span class="text-chocolate-900">{{ $story->title }}</span>
        </nav>
        <h1 class="mt-4 font-display text-3xl sm:text-4xl font-medium tracking-tight text-chocolate-900">{{ $story->title }}</h1>
        @if($story->excerpt)
            <p class="mt-4 text-lg text-chocolate-800/90">{{ $story->excerpt }}</p>
        @endif
        @if($story->cover_image)
            <div class="mt-8 overflow-hidden rounded-3xl ring-1 ring-ivory-200 shadow-warm">
                <img src="{{ $story->cover_image }}" alt="" class="h-auto w-full object-cover" width="1200" height="630" fetchpriority="high">
            </div>
        @endif
        <div class="prose prose-stone mt-10 max-w-none text-chocolate-900 prose-headings:font-display prose-a:text-sienna-600">
            {!! \Illuminate\Support\Str::markdown($story->body ?? '') !!}
        </div>
    </article>
@endsection
