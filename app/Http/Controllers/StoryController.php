<?php

namespace App\Http\Controllers;

use App\Models\Story;
use Illuminate\View\View;

class StoryController extends Controller
{
    public function index(): View
    {
        $stories = Story::query()
            ->where('is_published', true)
            ->orderByDesc('published_at')
            ->paginate(12);

        return view('frontend.stories.index', compact('stories'));
    }

    public function show(string $slug): View
    {
        $story = Story::query()
            ->where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        return view('frontend.stories.show', compact('story'));
    }
}
