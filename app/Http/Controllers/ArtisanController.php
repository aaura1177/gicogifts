<?php

namespace App\Http\Controllers;

use App\Models\Artisan;
use Illuminate\View\View;

class ArtisanController extends Controller
{
    public function index(): View
    {
        $artisans = Artisan::query()
            ->with('region')
            ->orderBy('name')
            ->paginate(24);

        return view('frontend.artisans.index', compact('artisans'));
    }

    public function show(string $slug): View
    {
        $artisan = Artisan::query()
            ->where('slug', $slug)
            ->with(['region', 'products' => fn ($q) => $q->where('is_active', true)->with('media')])
            ->firstOrFail();

        return view('frontend.artisans.show', compact('artisan'));
    }
}
