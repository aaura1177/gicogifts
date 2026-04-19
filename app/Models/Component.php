<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Component extends Model
{
    protected $fillable = [
        'name',
        'sku',
        'unit_cost_inr',
        'stock_on_hand',
        'reorder_threshold',
        'supplier_name',
        'supplier_contact',
        'hsn_code',
    ];

    protected function casts(): array
    {
        return [
            'unit_cost_inr' => 'decimal:2',
        ];
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_components')
            ->withPivot(['quantity', 'notes'])
            ->withTimestamps();
    }

    public function productComponents(): HasMany
    {
        return $this->hasMany(ProductComponent::class);
    }

    public function inventoryMovements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class);
    }
}
