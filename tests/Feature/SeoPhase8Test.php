<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class SeoPhase8Test extends TestCase
{
    use RefreshDatabase;

    public function test_robots_includes_sitemap_and_ai_user_agents(): void
    {
        $response = $this->get('/robots.txt');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/plain; charset=UTF-8');
        $this->assertStringContainsString('Sitemap:', $response->getContent());
        $this->assertStringContainsString('GPTBot', $response->getContent());
        $this->assertStringContainsString('Google-Extended', $response->getContent());
    }

    public function test_sitemap_xml_is_valid_and_lists_product_url(): void
    {
        Cache::forget('seo.sitemap_xml_v2');

        $category = Category::query()->create([
            'slug' => 'test-cat',
            'name' => 'Test Cat',
            'sort_order' => 0,
        ]);

        Product::query()->create([
            'category_id' => $category->id,
            'region_id' => null,
            'slug' => 'test-product-seo',
            'sku' => 'SKU-SEO-1',
            'name' => 'Test Product SEO',
            'price_inr' => 100,
            'is_active' => true,
            'is_box' => false,
            'sort_order' => 0,
            'published_at' => now(),
        ]);

        $response = $this->get('/sitemap.xml');

        $response->assertOk();
        $content = $response->getContent();
        $this->assertStringContainsString('<?xml version="1.0" encoding="UTF-8"?>', $content);
        $this->assertStringContainsString('<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">', $content);
        $this->assertStringContainsString('/product/test-product-seo', $content);
        $this->assertStringContainsString('<lastmod>', $content);
    }

    public function test_product_page_contains_product_json_ld(): void
    {
        $category = Category::query()->create([
            'slug' => 'cat-ld',
            'name' => 'Cat LD',
            'sort_order' => 0,
        ]);

        Product::query()->create([
            'category_id' => $category->id,
            'region_id' => null,
            'slug' => 'boxed-ld',
            'sku' => 'SKU-LD-1',
            'name' => 'Boxed LD',
            'short_description' => 'A short line for meta.',
            'price_inr' => 1500,
            'is_active' => true,
            'is_box' => true,
            'sort_order' => 0,
            'published_at' => now(),
        ]);

        $response = $this->get('/product/boxed-ld');

        $response->assertOk();
        $this->assertStringContainsString('"@type":"Product"', $response->getContent());
        $this->assertStringContainsString('boxed-ld', $response->getContent());
        $this->assertStringContainsString('rel="canonical"', $response->getContent());
    }
}
