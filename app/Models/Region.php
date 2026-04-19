<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Region extends Model
{
    use HasSlug;

    protected $fillable = [
        'slug',
        'name',
        'description',
        'sort_order',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function artisans(): HasMany
    {
        return $this->hasMany(Artisan::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
