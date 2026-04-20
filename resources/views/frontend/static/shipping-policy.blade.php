@extends('layouts.app')

@section('title', 'Shipping policy — '.config('app.name'))

@section('meta_description', 'GicoGifts shipping within India and internationally — timelines, ₹99 flat / free above ₹2,000, and customs for NRI orders.')

@section('content')
    <article class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10 md:py-14 prose prose-stone prose-headings:font-display prose-headings:text-chocolate-900 max-w-none text-sm text-chocolate-800/95">
        <h1 class="text-3xl font-medium text-chocolate-900 not-prose">Shipping policy</h1>
        <p class="not-prose text-chocolate-700/85">Last updated: {{ now()->format('F j, Y') }}. We ship from <strong>Udaipur, Rajasthan</strong>.</p>

        <h2>India — rates &amp; timelines</h2>
        <ul>
            <li><strong>Flat ₹99</strong> shipping on orders below ₹2,000.</li>
            <li><strong>Free shipping</strong> on orders of ₹2,000 or more (India only).</li>
            <li>After payment and packing, most metros receive orders in about <strong>3–7 business days</strong>; remote areas may take longer.</li>
        </ul>
        <p>Carriers and ETAs vary by pincode. You will receive tracking (AWB) when the parcel is handed to the courier.</p>

        <h2>International (NRI / abroad)</h2>
        <p>International delivery typically takes <strong>7–14 business days</strong> after dispatch, depending on destination and customs. <strong>Import duties, taxes, and customs clearance are the customer’s responsibility</strong> unless we explicitly state otherwise on the checkout page for your country.</p>

        <h2>Order processing</h2>
        <p>We pack in the order queue after payment confirmation. During peak seasons (e.g. Diwali), allow extra handling time; we will communicate delays on the site or by email when significant.</p>

        <h2>Damaged or lost shipments</h2>
        <p>If your parcel arrives damaged, follow the <a href="{{ route('refund-policy') }}">Refund policy</a> window and photo requirements. For loss in transit after courier pickup, contact us with your order number — we will work with the carrier to trace or replace where applicable.</p>

        <h2>Address accuracy</h2>
        <p>You are responsible for a deliverable address and reachable phone. Re-shipment due to incorrect address may incur additional shipping charges.</p>

        <h2>Contact</h2>
        <p>Questions? Use our <a href="{{ route('contact') }}">Contact</a> page with your order number.</p>
    </article>
@endsection
