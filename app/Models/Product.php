<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Scout\Searchable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Product extends Model implements HasMedia
{
    use HasSlug;
    use InteractsWithMedia;
    use Searchable;

    protected $fillable = [
        'category_id',
        'region_id',
        'slug',
        'sku',
        'name',
        'subtitle',
        'story_md',
        'short_description',
        'price_inr',
        'compare_at_price_inr',
        'is_box',
        'is_active',
        'is_featured',
        'hsn_code',
        'gst_rate',
        'weight_grams',
        'length_cm',
        'width_cm',
        'height_cm',
        'meta_title',
        'meta_description',
        'sort_order',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'price_inr' => 'decimal:2',
            'compare_at_price_inr' => 'decimal:2',
            'gst_rate' => 'decimal:2',
            'is_box' => 'boolean',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'published_at' => 'datetime',
            'length_cm' => 'decimal:2',
            'width_cm' => 'decimal:2',
            'height_cm' => 'decimal:2',
        ];
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images')->useDisk('public');
    }

    public function toSearchableArray(): array
    {
        return [
            'id' => (string) $this->getKey(),
            'name' => $this->name,
            'sku' => $this->sku,
            'short_description' => $this->short_description,
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function artisans(): BelongsToMany
    {
        return $this->belongsToMany(Artisan::class, 'product_artisan')->withTimestamps();
    }

    public function occasions(): BelongsToMany
    {
        return $this->belongsToMany(Occasion::class, 'occasion_product')->withTimestamps();
    }

    public function components(): BelongsToMany
    {
        return $this->belongsToMany(Component::class, 'product_components')
            ->withPivot(['quantity', 'notes'])
            ->withTimestamps();
    }

    public function productComponents(): HasMany
    {
        return $this->hasMany(ProductComponent::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }
}
