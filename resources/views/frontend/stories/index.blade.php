@extends('layouts.app')

@section('title', 'Stories — '.config('app.name'))

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 md:py-16">
        <h1 class="font-display text-3xl font-medium text-chocolate-900">Stories</h1>
        <p class="mt-2 max-w-2xl text-sm text-chocolate-800/85">Long reads from the road — regions, workshops, and the people who define each piece.</p>
        <div class="mt-10 grid gap-6 md:grid-cols-2">
            @foreach($stories as $story)
                @include('frontend.partials.story-card', ['story' => $story, 'compact' => true])
            @endforeach
        </div>
        <div class="mt-10">{{ $stories->links() }}</div>
    </div>
@endsection
