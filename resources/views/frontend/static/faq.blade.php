@extends('layouts.app')

@section('title', 'FAQ — '.config('app.name'))

@section('content')
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <h1 class="font-display text-3xl font-medium text-chocolate-900">FAQ</h1>
        <p class="mt-2 text-sm text-chocolate-800/80">Quick answers about shipping, orders, and our boxes.</p>

        <div class="mt-10 space-y-2" x-data="accordion()">
            @foreach($faqs as $faq)
                <div class="rounded-xl border border-ivory-200 bg-white shadow-sm overflow-hidden">
                    <button type="button" class="flex w-full min-h-[48px] items-center justify-between gap-4 px-4 py-3 text-left text-sm font-medium text-chocolate-900 hover:bg-ivory-50" @click="toggle({{ $faq->id }})" :aria-expanded="openId === {{ $faq->id }}">
                        <span>{{ $faq->question }}</span>
                        <span class="text-sienna-500 text-lg leading-none" x-text="openId === {{ $faq->id }} ? '−' : '+'"></span>
                    </button>
                    <div x-show="openId === {{ $faq->id }}" x-transition class="border-t border-ivory-100 px-4 py-3 text-sm text-chocolate-800/90 whitespace-pre-line">
                        {{ $faq->answer }}
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
