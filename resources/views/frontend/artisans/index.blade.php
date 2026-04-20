@extends('layouts.app')

@section('title', 'Artisans — '.config('app.name'))

@section('meta_description', 'Meet the painters, weavers, and makers behind GicoGifts — artisan profiles from across Rajasthan.')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 md:py-16">
        <h1 class="font-display text-3xl font-medium text-chocolate-900">Our artisans</h1>
        <p class="mt-2 max-w-2xl text-sm text-chocolate-800/85">The hands behind the boxes — painters, weavers, printers, and makers across Rajasthan.</p>
        <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($artisans as $artisan)
                @include('frontend.partials.artisan-card', ['artisan' => $artisan])
            @endforeach
        </div>
        <div class="mt-10">{{ $artisans->links() }}</div>
    </div>
@endsection
