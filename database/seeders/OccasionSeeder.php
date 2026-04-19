<?php

namespace Database\Seeders;

use App\Models\Occasion;
use Illuminate\Database\Seeder;

class OccasionSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['slug' => 'diwali', 'name' => 'Diwali', 'sort_order' => 1],
            ['slug' => 'wedding', 'name' => 'Wedding', 'sort_order' => 2],
            ['slug' => 'birthday', 'name' => 'Birthday', 'sort_order' => 3],
            ['slug' => 'housewarming', 'name' => 'Housewarming', 'sort_order' => 4],
            ['slug' => 'thank-you', 'name' => 'Thank You', 'sort_order' => 5],
            ['slug' => 'corporate', 'name' => 'Corporate', 'sort_order' => 6],
        ];

        foreach ($rows as $row) {
            Occasion::query()->updateOrCreate(
                ['slug' => $row['slug']],
                ['name' => $row['name'], 'sort_order' => $row['sort_order']]
            );
        }
    }
}
