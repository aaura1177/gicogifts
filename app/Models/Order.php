<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'user_id',
        'email',
        'phone',
        'status',
        'subtotal_inr',
        'shipping_inr',
        'discount_inr',
        'gst_inr',
        'total_inr',
        'shipping_address_id',
        'billing_address_id',
        'razorpay_order_id',
        'razorpay_payment_id',
        'stripe_payment_intent_id',
        'payment_gateway',
        'coupon_code',
        'notes',
        'shipping_snapshot',
        'is_gift',
        'gift_message',
        'paid_at',
        'packed_at',
        'shipped_at',
        'delivered_at',
    ];

    protected function casts(): array
    {
        return [
            'shipping_snapshot' => 'array',
            'subtotal_inr' => 'decimal:2',
            'shipping_inr' => 'decimal:2',
            'discount_inr' => 'decimal:2',
            'gst_inr' => 'decimal:2',
            'total_inr' => 'decimal:2',
            'is_gift' => 'boolean',
            'paid_at' => 'datetime',
            'packed_at' => 'datetime',
            'shipped_at' => 'datetime',
            'delivered_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function shippingAddress(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'shipping_address_id');
    }

    public function billingAddress(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'billing_address_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusHistory(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function shipments(): HasMany
    {
        return $this->hasMany(Shipment::class);
    }
}
