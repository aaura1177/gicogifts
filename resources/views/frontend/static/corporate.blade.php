@extends('layouts.app')

@section('title', 'Corporate gifting — '.config('app.name'))

@section('content')
    <h1 class="text-2xl font-semibold text-stone-900">Corporate gifting</h1>
    <p class="mt-4 text-stone-600 text-sm max-w-2xl">Tell us about your event, headcount, cities, and timeline. We reply within two business days.</p>
    <form method="post" action="{{ route('corporate.submit') }}" class="mt-8 max-w-xl space-y-4">
        @csrf
        <input type="text" name="name" value="{{ old('name') }}" required placeholder="Your name" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
        <input type="email" name="email" value="{{ old('email') }}" required placeholder="Work email" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
        <input type="text" name="phone" value="{{ old('phone') }}" placeholder="Phone" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
        <input type="text" name="company" value="{{ old('company') }}" placeholder="Company" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
        <textarea name="message" rows="5" required placeholder="Brief, budgets, cities, dates" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">{{ old('message') }}</textarea>
        @if($errors->any())
            <div class="text-sm text-red-600">{{ $errors->first() }}</div>
        @endif
        <button type="submit" class="rounded-lg bg-stone-900 px-4 py-2 text-sm font-medium text-white">Submit enquiry</button>
    </form>
@endsection
