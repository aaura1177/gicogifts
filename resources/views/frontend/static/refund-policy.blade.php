@extends('layouts.app')

@section('title', 'Refund policy — '.config('app.name'))

@section('meta_description', 'GicoGifts returns: 7-day breakage-only window, non-returnable food items, and refund timelines after approval.')

@section('content')
    <article class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10 md:py-14 prose prose-stone prose-headings:font-display prose-headings:text-chocolate-900 max-w-none text-sm text-chocolate-800/95">
        <h1 class="text-3xl font-medium text-chocolate-900 not-prose">Refund &amp; returns policy</h1>
        <p class="not-prose text-chocolate-700/85">Last updated: {{ now()->format('F j, Y') }}. We want every gift to arrive beautifully — if something went wrong, here is how we fix it.</p>

        <h2>What we accept</h2>
        <p>We offer <strong>breakage-only returns within 7 calendar days of delivery</strong>, with clear <strong>photographs</strong> of the damage and outer packaging. This covers manufacturing or transit damage that makes the item unusable or materially different from what you ordered.</p>

        <h2>What we do not accept</h2>
        <ul>
            <li><strong>Food, tea, spices, and other perishables</strong> are <strong>non-returnable</strong> once shipped, for safety reasons.</li>
            <li>Change-of-mind returns (e.g. colour preference) are generally not accepted for artisan goods — variation is part of handmade work.</li>
        </ul>

        <h2>How to start a claim</h2>
        <ol>
            <li>Email us via <a href="{{ route('contact') }}">Contact</a> within 7 days of delivery with your order number.</li>
            <li>Attach photos of the damage, the inner packing, and the shipping label/box if available.</li>
            <li>We will confirm eligibility within <strong>2 business days</strong> where possible.</li>
        </ol>

        <h2>Refunds &amp; timelines</h2>
        <p>Approved refunds are initiated to your <strong>original payment method</strong>. Depending on Razorpay, Stripe, or your bank, funds typically appear within <strong>5–7 business days</strong> after we process the refund (timelines may vary by issuer).</p>
        <p>Replacements may be offered instead of a refund when stock allows.</p>

        <h2>Partial shipments</h2>
        <p>If only part of an order is affected, we may refund or replace the affected line items only.</p>

        <h2>Chargebacks</h2>
        <p>Please contact us before initiating a payment dispute — we resolve most issues faster directly.</p>
    </article>
@endsection
