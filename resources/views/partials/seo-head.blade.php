@php
    $pageTitle = trim($__env->yieldContent('title')) ?: config('app.name');
    $desc = trim($__env->yieldContent('meta_description'));
    if ($desc === '') {
        $desc = (string) config('gicogifts.default_meta_description');
    }
    $canonical = trim($__env->yieldContent('canonical'));
    if ($canonical === '') {
        $canonical = request()->url();
    }
    $ogType = trim($__env->yieldContent('og_type')) ?: 'website';
    $ogImage = trim($__env->yieldContent('og_image'));
    if ($ogImage === '') {
        $ogImage = (string) config('gicogifts.default_og_image');
    }
    if ($ogImage === '') {
        $ogImage = url(asset('images/og-default.svg'));
    }
    $orgLd = [
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        'name' => config('gicogifts.organization.name'),
        'url' => rtrim((string) config('app.url'), '/'),
        'description' => (string) config('gicogifts.default_meta_description'),
    ];
    $contact = (string) config('gicogifts.organization.contact_email');
    if ($contact !== '') {
        $orgLd['email'] = $contact;
    }
@endphp
<link rel="canonical" href="{{ $canonical }}">
<meta name="description" content="{{ $desc }}">
<meta property="og:site_name" content="{{ config('app.name') }}">
<meta property="og:title" content="{{ $pageTitle }}">
<meta property="og:description" content="{{ $desc }}">
<meta property="og:url" content="{{ $canonical }}">
<meta property="og:type" content="{{ $ogType }}">
<meta property="og:image" content="{{ $ogImage }}">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $pageTitle }}">
<meta name="twitter:description" content="{{ $desc }}">
<meta name="twitter:image" content="{{ $ogImage }}">
<script type="application/ld+json">{!! json_encode($orgLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS) !!}</script>
@stack('jsonld')
