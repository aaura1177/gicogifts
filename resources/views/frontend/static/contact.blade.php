@extends('layouts.app')

@section('title', 'Contact — '.config('app.name'))

@section('content')
    <h1 class="text-2xl font-semibold text-stone-900">Contact</h1>
    <form method="post" action="{{ route('contact.store') }}" class="mt-8 max-w-xl space-y-4">
        @csrf
        <input type="text" name="name" value="{{ old('name') }}" required placeholder="Name" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
        <input type="email" name="email" value="{{ old('email') }}" required placeholder="Email" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
        <input type="text" name="phone" value="{{ old('phone') }}" placeholder="Phone" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
        <textarea name="message" rows="5" required placeholder="Message" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">{{ old('message') }}</textarea>
        @if($errors->any())
            <div class="text-sm text-red-600">{{ $errors->first() }}</div>
        @endif
        <button type="submit" class="rounded-lg bg-stone-900 px-4 py-2 text-sm font-medium text-white">Send</button>
    </form>
@endsection
