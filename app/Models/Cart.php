<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class Cart extends Model
{
    protected $fillable = [
        'user_id',
        'session_id',
        'currency',
    ];

    public static function current(): self
    {
        if (Auth::check()) {
            return static::query()->firstOrCreate(
                ['user_id' => Auth::id()],
                ['currency' => 'INR', 'session_id' => null]
            );
        }

        return static::query()->firstOrCreate(
            ['session_id' => session()->getId(), 'user_id' => null],
            ['currency' => 'INR']
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function itemCount(): int
    {
        return (int) $this->items()->sum('quantity');
    }

    public function subtotalInr(): float
    {
        return (float) $this->items()
            ->get()
            ->reduce(fn (float $carry, CartItem $item) => $carry + ((float) $item->unit_price_inr * $item->quantity), 0.0);
    }
}
