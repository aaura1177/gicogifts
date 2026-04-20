@extends('layouts.app')

@section('title', 'Privacy policy — '.config('app.name'))

@section('meta_description', 'How GicoGifts collects, uses, and protects your data — orders, cookies, payments via Razorpay and Stripe, and your choices.')

@section('content')
    <article class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10 md:py-14 prose prose-stone prose-headings:font-display prose-headings:text-chocolate-900 max-w-none text-sm text-chocolate-800/95">
        <h1 class="text-3xl font-medium text-chocolate-900 not-prose">Privacy policy</h1>
        <p class="not-prose text-chocolate-700/85">Last updated: {{ now()->format('F j, Y') }}. This policy describes how {{ config('gicogifts.organization.name', config('app.name')) }} (“we”, “us”) handles personal information when you use {{ config('app.url') }}.</p>

        <h2>What we collect</h2>
        <ul>
            <li><strong>Account &amp; orders:</strong> name, email, phone, shipping and billing addresses, order contents, and messages you send us (including gift notes).</li>
            <li><strong>Payments:</strong> card and UPI details are processed by <strong>Razorpay</strong> (India) and <strong>Stripe</strong> (international) on their hosted pages. We receive payment status, transaction references, and limited metadata — not your full card number.</li>
            <li><strong>Site usage:</strong> standard server logs, session cookies for cart and login, and optional analytics if enabled.</li>
            <li><strong>Marketing:</strong> newsletter email only if you opt in.</li>
        </ul>

        <h2>Why we use it</h2>
        <p>We use this information to fulfil orders, prevent fraud, provide customer support, improve the site, and (where you have agreed) send marketing. Legal bases include contract, legitimate interests, and consent where required.</p>

        <h2>Sharing</h2>
        <p>We share data with payment processors (Razorpay, Stripe), shipping partners (e.g. courier APIs), email providers, and hosting/infrastructure vendors who process it on our instructions. We do not sell your personal information.</p>

        <h2>Cookies</h2>
        <p>We use cookies for essential functions (session, cart, security). Non-essential cookies, if any, will be disclosed in a cookie banner update.</p>

        <h2>Retention</h2>
        <p>We keep order and tax records as required by Indian law (typically several years). Marketing data is kept until you unsubscribe.</p>

        <h2>Your rights</h2>
        <p>You may request access, correction, or deletion of your personal data where applicable. Contact us using the details on our <a href="{{ route('contact') }}">Contact</a> page. We may need to verify your identity.</p>

        <h2>International visitors</h2>
        <p>If you order from outside India, your information may be processed in India and in countries where our subprocessors operate, with appropriate safeguards as required.</p>

        <h2>Contact</h2>
        <p>For privacy questions, reach us via the <a href="{{ route('contact') }}">contact form</a>@if(filled(config('gicogifts.organization.contact_email'))) or at {{ config('gicogifts.organization.contact_email') }}@endif.</p>
    </article>
@endsection
