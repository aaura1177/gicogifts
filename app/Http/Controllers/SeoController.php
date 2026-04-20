<?php

namespace App\Http\Controllers;

use App\Models\Artisan;
use App\Models\Occasion;
use App\Models\Product;
use App\Models\Region;
use App\Models\Story;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class SeoController extends Controller
{
    public function robots(): Response
    {
        $sitemap = url('/sitemap.xml');
        $body = <<<TXT
User-agent: *
Allow: /

User-agent: GPTBot
Allow: /

User-agent: ChatGPT-User
Allow: /

User-agent: Google-Extended
Allow: /

User-agent: PerplexityBot
Allow: /

Sitemap: {$sitemap}
TXT;

        return response($body, 200)->header('Content-Type', 'text/plain; charset=UTF-8');
    }

    public function sitemap(): Response
    {
        $xml = Cache::remember('seo.sitemap_xml_v2', 3600, fn (): string => $this->buildSitemapXml());

        return response($xml, 200)->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    private function buildSitemapXml(): string
    {
        $static = collect([
            '/',
            '/shop',
            '/shop/boxes',
            '/shop/individual',
            '/stories',
            '/artisans',
            '/about',
            '/corporate-gifting',
            '/faq',
            '/contact',
            '/cart',
            '/privacy-policy',
            '/terms',
            '/shipping-policy',
            '/refund-policy',
        ])->map(fn (string $path): array => [
            'loc' => url($path),
            'lastmod' => null,
        ]);

        $productRows = Product::query()
            ->where('is_active', true)
            ->select(['slug', 'updated_at'])
            ->orderBy('slug')
            ->get()
            ->map(fn (Product $p): array => [
                'loc' => url('/product/'.$p->slug),
                'lastmod' => $p->updated_at?->toAtomString(),
            ]);

        $storyRows = Story::query()
            ->where('is_published', true)
            ->select(['slug', 'updated_at'])
            ->orderBy('slug')
            ->get()
            ->map(fn (Story $s): array => [
                'loc' => url('/stories/'.$s->slug),
                'lastmod' => $s->updated_at?->toAtomString(),
            ]);

        $artisanRows = Artisan::query()
            ->select(['slug', 'updated_at'])
            ->orderBy('slug')
            ->get()
            ->map(fn (Artisan $a): array => [
                'loc' => url('/artisans/'.$a->slug),
                'lastmod' => $a->updated_at?->toAtomString(),
            ]);

        $occasionRows = Occasion::query()
            ->select(['slug', 'updated_at'])
            ->orderBy('slug')
            ->get()
            ->map(fn (Occasion $o): array => [
                'loc' => url('/shop/occasion/'.$o->slug),
                'lastmod' => $o->updated_at?->toAtomString(),
            ]);

        $regionRows = Region::query()
            ->select(['slug', 'updated_at'])
            ->orderBy('slug')
            ->get()
            ->map(fn (Region $r): array => [
                'loc' => url('/shop/region/'.$r->slug),
                'lastmod' => $r->updated_at?->toAtomString(),
            ]);

        $all = $static
            ->merge($productRows)
            ->merge($storyRows)
            ->merge($artisanRows)
            ->merge($occasionRows)
            ->merge($regionRows)
            ->unique('loc')
            ->values();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'
            .'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        foreach ($all as $row) {
            $xml .= '<url><loc>'.e($row['loc']).'</loc>';
            if (is_string($row['lastmod']) && $row['lastmod'] !== '') {
                $xml .= '<lastmod>'.e($row['lastmod']).'</lastmod>';
            }
            $xml .= '<changefreq>weekly</changefreq></url>';
        }

        $xml .= '</urlset>';

        return $xml;
    }
}
