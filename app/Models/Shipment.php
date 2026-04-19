<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Shipment extends Model
{
    protected $fillable = [
        'order_id',
        'shiprocket_order_id',
        'shiprocket_shipment_id',
        'awb_code',
        'courier_name',
        'status',
        'tracking_url',
        'expected_delivery',
        'actual_delivery',
        'label_pdf_url',
        'manifest_pdf_url',
    ];

    protected function casts(): array
    {
        return [
            'expected_delivery' => 'date',
            'actual_delivery' => 'date',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
