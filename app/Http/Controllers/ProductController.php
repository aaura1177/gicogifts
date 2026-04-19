<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function show(string $slug): View
    {
        $product = Product::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->with([
                'media',
                'region',
                'category',
                'artisans.region',
                'components',
                'occasions',
                'reviews' => fn ($q) => $q->where('is_approved', true)->orderByDesc('created_at'),
            ])
            ->firstOrFail();

        $relatedProducts = Product::query()
            ->where('is_active', true)
            ->where('id', '!=', $product->id)
            ->when($product->region_id, fn ($q) => $q->where('region_id', $product->region_id))
            ->with('media')
            ->limit(8)
            ->get();

        if ($relatedProducts->isEmpty()) {
            $relatedProducts = Product::query()
                ->where('is_active', true)
                ->where('id', '!=', $product->id)
                ->with('media')
                ->limit(8)
                ->get();
        }

        return view('frontend.product.show', compact('product', 'relatedProducts'));
    }
}
