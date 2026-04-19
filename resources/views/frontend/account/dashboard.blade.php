@extends('layouts.account')

@section('title', 'Account — '.config('app.name'))

@section('content')
    <h1 class="font-display text-2xl font-medium text-chocolate-900">Account</h1>
    <p class="mt-4 text-sm text-chocolate-800/85">Welcome, {{ auth()->user()->name }}.</p>
    <ul class="mt-8 space-y-2 text-sm text-chocolate-800">
        <li><a href="{{ route('account.orders') }}" class="font-medium text-sienna-600 hover:text-sienna-700">Orders</a></li>
        <li><a href="{{ route('account.addresses') }}" class="font-medium text-sienna-600 hover:text-sienna-700">Addresses</a> <span class="text-chocolate-700/50">(placeholder)</span></li>
        <li><a href="{{ route('account.wishlist') }}" class="font-medium text-sienna-600 hover:text-sienna-700">Wishlist</a> <span class="text-chocolate-700/50">(placeholder)</span></li>
        <li><a href="{{ route('profile.edit') }}" class="font-medium text-sienna-600 hover:text-sienna-700">Profile settings</a></li>
    </ul>
@endsection
