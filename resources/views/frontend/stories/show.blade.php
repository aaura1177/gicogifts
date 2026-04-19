@extends('layouts.app')

@section('title', $story->title.' — '.config('app.name'))

@section('content')
    <article class="max-w-3xl">
        <h1 class="text-3xl font-semibold text-stone-900">{{ $story->title }}</h1>
        @if($story->excerpt)
            <p class="mt-4 text-lg text-stone-600">{{ $story->excerpt }}</p>
        @endif
        <div class="mt-8 prose prose-stone max-w-none text-sm">
            {!! nl2br(e($story->body ?? '')) !!}
        </div>
    </article>
@endsection
