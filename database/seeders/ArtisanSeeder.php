<?php

namespace Database\Seeders;

use App\Models\Artisan;
use App\Models\Region;
use Illuminate\Database\Seeder;

class ArtisanSeeder extends Seeder
{
    public function run(): void
    {
        $mewar = Region::query()->where('slug', 'mewar')->first();
        $jaipur = Region::query()->where('slug', 'jaipur')->first();
        $tribal = Region::query()->where('slug', 'tribal-belt')->first();

        $rows = [
            ['slug' => 'govindji-pichwai', 'name' => 'Govindji', 'bio' => 'Pichwai artist based in Nathdwara, carrying forward temple miniature traditions.', 'region_id' => $mewar?->id],
            ['slug' => 'rekha-block-print', 'name' => 'Rekha Devi', 'bio' => 'Master block printer from Sanganer, working with natural dyes.', 'region_id' => $jaipur?->id],
            ['slug' => 'bhanwar-tribal-weave', 'name' => 'Bhanwar Lal', 'bio' => 'Handloom weaver from the tribal belt, specialising in wool-cotton blends.', 'region_id' => $tribal?->id],
            ['slug' => 'meera-blue-pottery', 'name' => 'Meera Soni', 'bio' => 'Blue pottery artisan crafting tableware and decor in Jaipur.', 'region_id' => $jaipur?->id],
            ['slug' => 'prakash-marble-inlay', 'name' => 'Prakash Sharma', 'bio' => 'Marble inlay craftsperson working in the Udaipur atelier.', 'region_id' => $mewar?->id],
        ];

        foreach ($rows as $row) {
            Artisan::query()->updateOrCreate(
                ['slug' => $row['slug']],
                [
                    'name' => $row['name'],
                    'bio' => $row['bio'],
                    'region_id' => $row['region_id'],
                ]
            );
        }
    }
}
