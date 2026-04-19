<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'site.tagline' => 'Premium hyper-local artisan gift boxes from Rajasthan',
            'shipping.flat_rate_inr' => '99',
            'shipping.free_above_inr' => '2000',
        ];

        foreach ($defaults as $key => $value) {
            Setting::query()->updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
