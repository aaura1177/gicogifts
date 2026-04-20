<?php

namespace Database\Seeders;

use App\Models\Occasion;
use Illuminate\Database\Seeder;

class OccasionSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['slug' => 'diwali', 'name' => 'Diwali', 'sort_order' => 1, 'hero_image' => 'https://picsum.photos/seed/occasion-diwali/1600/640'],
            ['slug' => 'wedding', 'name' => 'Wedding', 'sort_order' => 2, 'hero_image' => 'https://picsum.photos/seed/occasion-wedding/1600/640'],
            ['slug' => 'birthday', 'name' => 'Birthday', 'sort_order' => 3, 'hero_image' => 'https://picsum.photos/seed/occasion-birthday/1600/640'],
            ['slug' => 'housewarming', 'name' => 'Housewarming', 'sort_order' => 4, 'hero_image' => 'https://picsum.photos/seed/occasion-housewarming/1600/640'],
            ['slug' => 'thank-you', 'name' => 'Thank You', 'sort_order' => 5, 'hero_image' => 'https://picsum.photos/seed/occasion-thankyou/1600/640'],
            ['slug' => 'corporate', 'name' => 'Corporate', 'sort_order' => 6, 'hero_image' => 'https://picsum.photos/seed/occasion-corporate/1600/640'],
        ];

        foreach ($rows as $row) {
            Occasion::query()->updateOrCreate(
                ['slug' => $row['slug']],
                [
                    'name' => $row['name'],
                    'sort_order' => $row['sort_order'],
                    'hero_image' => $row['hero_image'],
                ]
            );
        }
    }
}
