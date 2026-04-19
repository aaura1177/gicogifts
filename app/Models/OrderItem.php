<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'sku',
        'quantity',
        'unit_price_inr',
        'line_total_inr',
        'hsn_code',
        'gst_rate',
    ];

    protected function casts(): array
    {
        return [
            'unit_price_inr' => 'decimal:2',
            'line_total_inr' => 'decimal:2',
            'gst_rate' => 'decimal:2',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
