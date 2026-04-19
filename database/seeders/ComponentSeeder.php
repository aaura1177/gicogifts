<?php

namespace Database\Seeders;

use App\Models\Component;
use Illuminate\Database\Seeder;

class ComponentSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['sku' => 'GG-CMP-PICH-PRINT', 'name' => 'Pichwai art print (A5)', 'unit_cost_inr' => 120, 'stock_on_hand' => 200, 'reorder_threshold' => 20, 'hsn_code' => '9701'],
            ['sku' => 'GG-CMP-BRASS-DIYA', 'name' => 'Hand-finished brass diya', 'unit_cost_inr' => 180, 'stock_on_hand' => 2, 'reorder_threshold' => 5, 'hsn_code' => '8306'],
            ['sku' => 'GG-CMP-MASALA-CHAI', 'name' => 'Masala chai blend (100g)', 'unit_cost_inr' => 95, 'stock_on_hand' => 150, 'reorder_threshold' => 30, 'hsn_code' => '0902'],
            ['sku' => 'GG-CMP-BP-NAPKIN', 'name' => 'Block-print cotton napkins (set of 4)', 'unit_cost_inr' => 220, 'stock_on_hand' => 80, 'reorder_threshold' => 15, 'hsn_code' => '5208'],
            ['sku' => 'GG-CMP-BP-SCARF', 'name' => 'Block-printed cotton scarf', 'unit_cost_inr' => 260, 'stock_on_hand' => 60, 'reorder_threshold' => 12, 'hsn_code' => '5208'],
            ['sku' => 'GG-CMP-BLUE-COASTER', 'name' => 'Blue pottery coaster pair', 'unit_cost_inr' => 140, 'stock_on_hand' => 100, 'reorder_threshold' => 20, 'hsn_code' => '6913'],
            ['sku' => 'GG-CMP-DRYFRUIT', 'name' => 'Premium dry fruit mix (200g)', 'unit_cost_inr' => 210, 'stock_on_hand' => 90, 'reorder_threshold' => 18, 'hsn_code' => '0802'],
            ['sku' => 'GG-CMP-SAFFRON', 'name' => 'Saffron vial (1g)', 'unit_cost_inr' => 320, 'stock_on_hand' => 40, 'reorder_threshold' => 8, 'hsn_code' => '0910'],
            ['sku' => 'GG-CMP-TRIBAL-JEWEL', 'name' => 'Tribal silver-tone jewellery piece', 'unit_cost_inr' => 175, 'stock_on_hand' => 70, 'reorder_threshold' => 10, 'hsn_code' => '7117'],
            ['sku' => 'GG-CMP-HANDLOOM', 'name' => 'Handwoven cotton runner', 'unit_cost_inr' => 240, 'stock_on_hand' => 55, 'reorder_threshold' => 10, 'hsn_code' => '5208'],
            ['sku' => 'GG-CMP-HERBAL-TEA', 'name' => 'Herbal tea trio', 'unit_cost_inr' => 110, 'stock_on_hand' => 120, 'reorder_threshold' => 25, 'hsn_code' => '0902'],
            ['sku' => 'GG-CMP-TERRACOTTA', 'name' => 'Terracotta accent piece', 'unit_cost_inr' => 85, 'stock_on_hand' => 130, 'reorder_threshold' => 20, 'hsn_code' => '6913'],
            ['sku' => 'GG-CMP-MINIATURE', 'name' => 'Miniature painting reproduction (framed)', 'unit_cost_inr' => 450, 'stock_on_hand' => 35, 'reorder_threshold' => 6, 'hsn_code' => '9701'],
            ['sku' => 'GG-CMP-MARBLE-ACCENT', 'name' => 'Small marble inlay accent tile', 'unit_cost_inr' => 380, 'stock_on_hand' => 45, 'reorder_threshold' => 8, 'hsn_code' => '6802'],
            ['sku' => 'GG-CMP-TEA-TIN', 'name' => 'Premium tea tin (assorted)', 'unit_cost_inr' => 195, 'stock_on_hand' => 75, 'reorder_threshold' => 15, 'hsn_code' => '0902'],
            ['sku' => 'GG-CMP-NOTEBOOK', 'name' => 'Hand-bound notebook pair', 'unit_cost_inr' => 165, 'stock_on_hand' => 85, 'reorder_threshold' => 15, 'hsn_code' => '4820'],
            ['sku' => 'GG-CMP-BOOKMARK', 'name' => 'Brass bookmark', 'unit_cost_inr' => 95, 'stock_on_hand' => 150, 'reorder_threshold' => 25, 'hsn_code' => '8306'],
            ['sku' => 'GG-CMP-COTTON-POUCH', 'name' => 'Cotton pouch (packaging)', 'unit_cost_inr' => 45, 'stock_on_hand' => 300, 'reorder_threshold' => 40, 'hsn_code' => '5208'],
            ['sku' => 'GG-CMP-SPICE-SAMPLER', 'name' => 'Rajasthan spice sampler (4 jars)', 'unit_cost_inr' => 130, 'stock_on_hand' => 160, 'reorder_threshold' => 30, 'hsn_code' => '0904'],
        ];

        foreach ($rows as $row) {
            Component::query()->updateOrCreate(
                ['sku' => $row['sku']],
                [
                    'name' => $row['name'],
                    'unit_cost_inr' => $row['unit_cost_inr'],
                    'stock_on_hand' => $row['stock_on_hand'],
                    'reorder_threshold' => $row['reorder_threshold'],
                    'hsn_code' => $row['hsn_code'],
                ]
            );
        }
    }
}
