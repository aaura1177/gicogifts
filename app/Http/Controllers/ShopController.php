<?php

namespace App\Http\Controllers;

use App\Models\Occasion;
use App\Models\Product;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShopController extends Controller
{
    public function index(Request $request): View
    {
        $products = Product::query()
            ->where('is_active', true)
            ->with(['media', 'region', 'category'])
            ->when($request->string('q')->toString(), function ($q, string $term): void {
                $q->where(function ($q) use ($term): void {
                    $q->where('name', 'like', '%'.$term.'%')
                        ->orWhere('sku', 'like', '%'.$term.'%');
                });
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(24)
            ->withQueryString();

        $occasions = Occasion::query()->orderBy('sort_order')->orderBy('name')->get();

        return view('frontend.shop.index', compact('products', 'occasions'));
    }

    public function boxes(): View
    {
        $products = Product::query()
            ->where('is_active', true)
            ->where('is_box', true)
            ->with(['media', 'region'])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(24);

        return view('frontend.shop.boxes', compact('products'));
    }

    public function individual(): View
    {
        $products = Product::query()
            ->where('is_active', true)
            ->where('is_box', false)
            ->with(['media', 'region'])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(24);

        return view('frontend.shop.individual', compact('products'));
    }

    public function occasion(string $slug): View
    {
        $occasion = Occasion::query()->where('slug', $slug)->firstOrFail();

        $occasionList = Occasion::query()->orderBy('sort_order')->orderBy('name')->get();

        $products = $occasion->products()
            ->where('is_active', true)
            ->with(['media', 'region'])
            ->orderBy('name')
            ->paginate(24);

        return view('frontend.shop.occasion', compact('occasion', 'occasionList', 'products'));
    }

    public function region(string $slug): View
    {
        $region = Region::query()->where('slug', $slug)->firstOrFail();

        $products = Product::query()
            ->where('is_active', true)
            ->where('region_id', $region->id)
            ->with(['media', 'region', 'category'])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(24);

        return view('frontend.shop.region', compact('region', 'products'));
    }
}
