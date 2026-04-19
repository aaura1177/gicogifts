<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            SettingSeeder::class,
            CategorySeeder::class,
            RegionSeeder::class,
            OccasionSeeder::class,
            ComponentSeeder::class,
            ArtisanSeeder::class,
            ProductSeeder::class,
            FaqSeeder::class,
            StorySeeder::class,
        ]);
    }
}
