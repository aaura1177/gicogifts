<?php

namespace Database\Seeders;

use App\Models\Region;
use Illuminate\Database\Seeder;

class RegionSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['slug' => 'mewar', 'name' => 'Mewar', 'description' => 'Udaipur, Nathdwara, and the Aravalli heartland.', 'sort_order' => 1],
            ['slug' => 'jaipur', 'name' => 'Jaipur', 'description' => 'Sanganer block prints, blue pottery, and Pink City craft.', 'sort_order' => 2],
            ['slug' => 'tribal-belt', 'name' => 'Tribal Belt', 'description' => 'Banswara, Dungarpur, and tribal textile traditions.', 'sort_order' => 3],
        ];

        foreach ($rows as $row) {
            Region::query()->updateOrCreate(
                ['slug' => $row['slug']],
                [
                    'name' => $row['name'],
                    'description' => $row['description'],
                    'sort_order' => $row['sort_order'],
                ]
            );
        }
    }
}
