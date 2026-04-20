@extends('layouts.app')

@section('title', 'Terms of service — '.config('app.name'))

@section('meta_description', 'Terms governing orders, pricing, cancellations, and use of the GicoGifts website — jurisdiction Udaipur, India.')

@section('content')
    <article class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10 md:py-14 prose prose-stone prose-headings:font-display prose-headings:text-chocolate-900 max-w-none text-sm text-chocolate-800/95">
        <h1 class="text-3xl font-medium text-chocolate-900 not-prose">Terms of service</h1>
        <p class="not-prose text-chocolate-700/85">Last updated: {{ now()->format('F j, Y') }}. By placing an order or using {{ parse_url((string) config('app.url'), PHP_URL_HOST) ?? 'this site' }}, you agree to these terms.</p>

        <h2>Orders &amp; acceptance</h2>
        <p>When you complete checkout and payment succeeds, we accept your order subject to product availability and fraud checks. We may cancel an order before dispatch and refund you if an item is unavailable or we detect a risk issue.</p>

        <h2>Pricing &amp; taxes</h2>
        <p>Prices are listed in INR and include GST where stated. We may correct pricing or description errors before acceptance; if we have already charged you, we will refund the difference or cancel at your choice.</p>

        <h2>Cancellations</h2>
        <p>Once an order is paid, changes depend on packing status. See our <a href="{{ route('refund-policy') }}">Refund policy</a> for returns and refunds after delivery.</p>

        <h2>Shipping &amp; risk</h2>
        <p>Risk of loss passes in line with our <a href="{{ route('shipping-policy') }}">Shipping policy</a> and courier terms. Tracking is provided when available.</p>

        <h2>Prohibited use</h2>
        <p>You may not misuse the site (including scraping that harms performance, unlawful resale schemes, or attempting to access non-public systems).</p>

        <h2>Intellectual property</h2>
        <p>Content, branding, photography, and product descriptions are owned by us or our licensors. You may not copy them for commercial use without permission.</p>

        <h2>Limitation of liability</h2>
        <p>To the maximum extent permitted by law, we are not liable for indirect or consequential losses. Our total liability for any claim relating to an order is limited to the amount you paid for that order.</p>

        <h2>Governing law &amp; jurisdiction</h2>
        <p>These terms are governed by the laws of India. Courts at <strong>Udaipur, Rajasthan</strong> shall have exclusive jurisdiction, subject to any non-waivable rights you have as a consumer.</p>

        <h2>Changes</h2>
        <p>We may update these terms; the “Last updated” date will change. Material changes affecting open orders will be communicated where practical.</p>
    </article>
@endsection
