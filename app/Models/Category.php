<?php

namespace App\Models;

use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class Category extends Model
{
    /** @use HasFactory<CategoryFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'image_path',
    ];

    protected static function booted(): void
    {
        static::created(fn () => self::flushNavigationCache());
        static::updated(fn () => self::flushNavigationCache());
    }

    private static function flushNavigationCache(): void
    {
        Cache::forget('navigation.categories');
        Cache::forget('navigation.pages');
    }

    /**
     * @return HasMany<Product, Category>
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
