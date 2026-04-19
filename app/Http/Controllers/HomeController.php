<?php

namespace App\Http\Controllers;

use App\Models\Artisan;
use App\Models\Occasion;
use App\Models\Product;
use App\Models\Region;
use App\Models\Story;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $featuredBoxes = Product::query()
            ->where('is_active', true)
            ->where('is_box', true)
            ->where('is_featured', true)
            ->with(['media', 'region'])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->limit(5)
            ->get();

        if ($featuredBoxes->count() < 5) {
            $ids = $featuredBoxes->pluck('id');
            $fill = Product::query()
                ->where('is_active', true)
                ->where('is_box', true)
                ->when($ids->isNotEmpty(), fn ($q) => $q->whereNotIn('id', $ids))
                ->with(['media', 'region'])
                ->orderBy('sort_order')
                ->orderBy('name')
                ->limit(5 - $featuredBoxes->count())
                ->get();
            $featuredBoxes = $featuredBoxes->concat($fill);
        }

        $occasions = Occasion::query()->orderBy('sort_order')->limit(6)->get();

        $regions = Region::query()->orderBy('sort_order')->orderBy('name')->get();

        $artisans = Artisan::query()
            ->with('region')
            ->orderBy('name')
            ->limit(12)
            ->get();

        $stories = Story::query()
            ->where('is_published', true)
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();

        $heroImage = $featuredBoxes->first()?->getFirstMediaUrl('images')
            ?: 'https://placehold.co/1600x900/F7EEE3/6D3620?text=GicoGifts';

        return view('frontend.home', compact('featuredBoxes', 'occasions', 'regions', 'artisans', 'stories', 'heroImage'));
    }
}
