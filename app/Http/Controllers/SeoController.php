<?php

namespace App\Http\Controllers;

use App\Models\Artisan;
use App\Models\Occasion;
use App\Models\Product;
use App\Models\Story;
use Illuminate\Http\Response;

class SeoController extends Controller
{
    public function robots(): Response
    {
        $sitemap = url('/sitemap.xml');
        $body = "User-agent: *\nAllow: /\n\nSitemap: {$sitemap}\n";

        return response($body, 200)->header('Content-Type', 'text/plain; charset=UTF-8');
    }

    public function sitemap(): Response
    {
        $urls = collect([
            url('/'),
            url('/shop'),
            url('/shop/boxes'),
            url('/shop/individual'),
            url('/stories'),
            url('/artisans'),
            url('/about'),
            url('/corporate-gifting'),
            url('/faq'),
            url('/contact'),
            url('/privacy-policy'),
            url('/terms'),
            url('/shipping-policy'),
            url('/refund-policy'),
        ]);

        $productUrls = Product::query()
            ->where('is_active', true)
            ->pluck('slug')
            ->map(fn (string $slug) => url('/product/'.$slug));

        $storyUrls = Story::query()
            ->where('is_published', true)
            ->pluck('slug')
            ->map(fn (string $slug) => url('/stories/'.$slug));

        $artisanUrls = Artisan::query()
            ->pluck('slug')
            ->map(fn (string $slug) => url('/artisans/'.$slug));

        $occasionUrls = Occasion::query()
            ->pluck('slug')
            ->map(fn (string $slug) => url('/shop/occasion/'.$slug));

        $all = $urls->merge($productUrls)->merge($storyUrls)->merge($artisanUrls)->merge($occasionUrls)->unique()->values();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'
            .'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        foreach ($all as $loc) {
            $xml .= '<url><loc>'.e($loc).'</loc><changefreq>weekly</changefreq></url>';
        }

        $xml .= '</urlset>';

        return response($xml, 200)->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}
