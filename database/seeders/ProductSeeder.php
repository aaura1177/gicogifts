<?php

namespace Database\Seeders;

use App\Models\Artisan;
use App\Models\Category;
use App\Models\Component;
use App\Models\Occasion;
use App\Models\Product;
use App\Models\Region;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $giftBoxes = Category::query()->where('slug', 'gift-boxes')->first();
        $individual = Category::query()->where('slug', 'individual-items')->first();

        $mewar = Region::query()->where('slug', 'mewar')->first();
        $jaipur = Region::query()->where('slug', 'jaipur')->first();
        $tribal = Region::query()->where('slug', 'tribal-belt')->first();

        $bySku = fn (string $sku) => Component::query()->where('sku', $sku)->first();

        $boxes = [
            [
                'slug' => 'mewar-heritage-box',
                'sku' => 'GG-BOX-MWR-01',
                'name' => 'The Mewar Heritage Box',
                'price_inr' => 2200,
                'hsn_code' => '9701',
                'is_featured' => true,
                'region_id' => $mewar?->id,
                'category_id' => $giftBoxes?->id,
                'subtitle' => 'Pichwai tones, brass light, and slow chai.',
                'short_description' => 'A curated tribute to Mewar — artful, warm, and unmistakably Rajasthan.',
                'story_md' => "## The Mewar Heritage Box\n\nCrafted around Nathdwara's pichwai palette and Udaipur's brasswork, this box is designed for hosts who love story-rich tables.",
                'bom' => [
                    'GG-CMP-PICH-PRINT' => 1,
                    'GG-CMP-BRASS-DIYA' => 1,
                    'GG-CMP-MASALA-CHAI' => 1,
                    'GG-CMP-BP-NAPKIN' => 1,
                ],
                'occasions' => ['diwali', 'wedding', 'housewarming'],
                'artisans' => ['govindji-pichwai', 'prakash-marble-inlay'],
            ],
            [
                'slug' => 'jaipur-colour-box',
                'sku' => 'GG-BOX-JPR-01',
                'name' => 'The Jaipur Colour Box',
                'price_inr' => 1500,
                'hsn_code' => '5208',
                'is_featured' => true,
                'region_id' => $jaipur?->id,
                'category_id' => $giftBoxes?->id,
                'subtitle' => 'Sanganer prints meet cool blue pottery.',
                'short_description' => 'Bright, joyful, and perfect for birthdays or thank-you gestures.',
                'story_md' => "## The Jaipur Colour Box\n\nSanganer's water-led block prints and Jaipur blue pottery in one joyful composition.",
                'bom' => [
                    'GG-CMP-BP-SCARF' => 1,
                    'GG-CMP-BLUE-COASTER' => 1,
                    'GG-CMP-DRYFRUIT' => 1,
                    'GG-CMP-SAFFRON' => 1,
                ],
                'occasions' => ['birthday', 'thank-you', 'diwali'],
                'artisans' => ['rekha-block-print', 'meera-blue-pottery'],
            ],
            [
                'slug' => 'tribal-discovery-box',
                'sku' => 'GG-BOX-TRB-01',
                'name' => 'The Tribal Discovery Box',
                'price_inr' => 1800,
                'hsn_code' => '6802',
                'is_featured' => false,
                'region_id' => $tribal?->id,
                'category_id' => $giftBoxes?->id,
                'subtitle' => 'Jewellery, weave, and forest herbs.',
                'short_description' => 'A tactile journey through the tribal belt — warm metals and handloom.',
                'story_md' => "## The Tribal Discovery Box\n\nJewellery, textiles, and herbal teas sourced with artisan partners in southern Rajasthan.",
                'bom' => [
                    'GG-CMP-TRIBAL-JEWEL' => 1,
                    'GG-CMP-HANDLOOM' => 1,
                    'GG-CMP-HERBAL-TEA' => 1,
                    'GG-CMP-TERRACOTTA' => 1,
                ],
                'occasions' => ['wedding', 'thank-you', 'corporate'],
                'artisans' => ['bhanwar-tribal-weave'],
            ],
            [
                'slug' => 'royal-udaipur-experience',
                'sku' => 'GG-BOX-UDP-01',
                'name' => 'The Royal Udaipur Experience',
                'price_inr' => 3500,
                'hsn_code' => '9701',
                'is_featured' => true,
                'region_id' => $mewar?->id,
                'category_id' => $giftBoxes?->id,
                'subtitle' => 'Miniatures, marble inlay, and a tea ritual.',
                'short_description' => 'Our flagship box for milestone gifting — museum-quiet luxury.',
                'story_md' => "## The Royal Udaipur Experience\n\nMiniature painting, marble inlay, and a refined tea ritual — composed like a palace afternoon.",
                'bom' => [
                    'GG-CMP-MINIATURE' => 1,
                    'GG-CMP-MARBLE-ACCENT' => 1,
                    'GG-CMP-TEA-TIN' => 1,
                    'GG-CMP-NOTEBOOK' => 1,
                    'GG-CMP-BOOKMARK' => 1,
                ],
                'occasions' => ['wedding', 'corporate', 'diwali', 'thank-you'],
                'artisans' => ['govindji-pichwai', 'prakash-marble-inlay'],
            ],
            [
                'slug' => 'mini-rajasthan-sampler',
                'sku' => 'GG-BOX-MIN-01',
                'name' => 'Mini Rajasthan Sampler',
                'price_inr' => 799,
                'hsn_code' => '0902',
                'is_featured' => false,
                'region_id' => $mewar?->id,
                'category_id' => $giftBoxes?->id,
                'subtitle' => 'Entry funnel — the pouch is the packaging.',
                'short_description' => 'Spice, tea, and cotton — a gentle first taste of GicoGifts.',
                'story_md' => "## Mini Rajasthan Sampler\n\nA pocket-friendly introduction: spices, chai, and a reusable cotton pouch.",
                'bom' => [
                    'GG-CMP-COTTON-POUCH' => 1,
                    'GG-CMP-SPICE-SAMPLER' => 1,
                    'GG-CMP-MASALA-CHAI' => 1,
                ],
                'occasions' => ['birthday', 'thank-you', 'housewarming'],
                'artisans' => ['rekha-block-print'],
            ],
        ];

        $items = [
            ['slug' => 'small-marble-inlay-box', 'sku' => 'GG-ITM-MRB-01', 'name' => 'Small Marble Inlay Box', 'price_inr' => 399, 'hsn_code' => '6802', 'is_featured' => false, 'region_id' => $mewar?->id],
            ['slug' => 'large-marble-inlay-box', 'sku' => 'GG-ITM-MRB-02', 'name' => 'Large Marble Inlay Box', 'price_inr' => 599, 'hsn_code' => '6802', 'is_featured' => false, 'region_id' => $mewar?->id],
            ['slug' => 'marble-coaster-set-6', 'sku' => 'GG-ITM-MRB-03', 'name' => 'Marble Inlay Coaster Set (6)', 'price_inr' => 1599, 'hsn_code' => '6802', 'is_featured' => true, 'region_id' => $mewar?->id],
            ['slug' => 'printed-marble-elephant', 'sku' => 'GG-ITM-MRB-04', 'name' => 'Hand-Painted Marble Elephant', 'price_inr' => 799, 'hsn_code' => '6802', 'is_featured' => false, 'region_id' => $mewar?->id],
            ['slug' => 'white-marble-elephant', 'sku' => 'GG-ITM-MRB-05', 'name' => 'White Marble Elephant', 'price_inr' => 749, 'hsn_code' => '6802', 'is_featured' => false, 'region_id' => $mewar?->id],
            ['slug' => 'small-lady-elephant', 'sku' => 'GG-ITM-MRB-06', 'name' => 'Petite Lady Elephant', 'price_inr' => 549, 'hsn_code' => '6802', 'is_featured' => false, 'region_id' => $mewar?->id],
            ['slug' => 'soapstone-candle-holder', 'sku' => 'GG-ITM-SST-01', 'name' => 'Soapstone Jaali Candle Holder', 'price_inr' => 649, 'hsn_code' => '6802', 'is_featured' => true, 'region_id' => $mewar?->id],
            ['slug' => 'soapstone-face-sculpture', 'sku' => 'GG-ITM-SST-02', 'name' => 'Soapstone Face Sculpture', 'price_inr' => 499, 'hsn_code' => '6802', 'is_featured' => false, 'region_id' => $mewar?->id],
        ];

        foreach ($boxes as $def) {
            $this->seedProduct(array_merge($def, [
                'is_box' => true,
                'weight_grams' => 1200,
                'length_cm' => 32,
                'width_cm' => 24,
                'height_cm' => 12,
            ]), $bySku);
        }

        foreach ($items as $def) {
            $this->seedProduct(array_merge($def, [
                'is_box' => false,
                'category_id' => $individual?->id,
                'subtitle' => 'Hand-finished in our Udaipur studio network.',
                'short_description' => 'Individual artisan piece — pair with a gift box or ship solo.',
                'story_md' => "## {$def['name']}\n\nA standalone piece from our marble and soapstone bench.",
                'bom' => [],
                'occasions' => ['thank-you', 'birthday', 'housewarming'],
                'artisans' => ['prakash-marble-inlay'],
                'weight_grams' => 400,
                'length_cm' => 18,
                'width_cm' => 14,
                'height_cm' => 8,
            ]), $bySku);
        }
    }

    /**
     * @param  callable(string): ?Component  $bySku
     */
    private function seedProduct(array $def, callable $bySku): void
    {
        $product = Product::query()->updateOrCreate(
            ['slug' => $def['slug']],
            [
                'sku' => $def['sku'],
                'name' => $def['name'],
                'subtitle' => $def['subtitle'] ?? null,
                'story_md' => $def['story_md'] ?? null,
                'short_description' => $def['short_description'] ?? null,
                'price_inr' => $def['price_inr'],
                'compare_at_price_inr' => null,
                'is_box' => $def['is_box'],
                'is_active' => true,
                'is_featured' => $def['is_featured'],
                'region_id' => $def['region_id'] ?? null,
                'category_id' => $def['category_id'] ?? null,
                'hsn_code' => $def['hsn_code'],
                'gst_rate' => 5,
                'weight_grams' => $def['weight_grams'] ?? null,
                'length_cm' => $def['length_cm'] ?? null,
                'width_cm' => $def['width_cm'] ?? null,
                'height_cm' => $def['height_cm'] ?? null,
                'meta_title' => $def['name'].' | GicoGifts',
                'meta_description' => $def['short_description'] ?? $def['name'],
                'sort_order' => 0,
                'published_at' => now(),
            ]
        );

        $pivot = [];
        foreach ($def['bom'] ?? [] as $sku => $qty) {
            $c = $bySku($sku);
            if ($c) {
                $pivot[$c->id] = ['quantity' => $qty, 'notes' => null];
            }
        }
        $product->components()->sync($pivot);

        $occasionIds = Occasion::query()->whereIn('slug', $def['occasions'] ?? [])->pluck('id');
        $product->occasions()->sync($occasionIds);

        $artisanIds = Artisan::query()->whereIn('slug', $def['artisans'] ?? [])->pluck('id');
        $product->artisans()->sync($artisanIds);

        $this->attachPlaceholderMedia($product);
    }

    private function attachPlaceholderMedia(Product $product): void
    {
        if ($product->getMedia('images')->isNotEmpty()) {
            return;
        }

        $url = 'https://picsum.photos/seed/'.substr(sha1($product->slug), 0, 12).'/900/700.jpg';

        try {
            $product->addMediaFromUrl($url)
                ->usingFileName($product->slug.'-1.jpg')
                ->toMediaCollection('images');
        } catch (\Throwable $e) {
            Log::warning('ProductSeeder: could not attach placeholder media', [
                'product' => $product->slug,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
