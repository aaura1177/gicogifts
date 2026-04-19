<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['slug' => 'gift-boxes', 'name' => 'Gift Boxes', 'sort_order' => 1],
            ['slug' => 'individual-items', 'name' => 'Individual Items', 'sort_order' => 2],
            ['slug' => 'occasions', 'name' => 'Occasions', 'sort_order' => 3],
        ];

        foreach ($rows as $row) {
            Category::query()->updateOrCreate(
                ['slug' => $row['slug']],
                ['name' => $row['name'], 'sort_order' => $row['sort_order']]
            );
        }
    }
}
