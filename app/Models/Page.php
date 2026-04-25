<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class Page extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'show_in_navigation',
        'navigation_label',
        'meta_title',
        'meta_description',
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
     * @return HasMany<PageBlock, Page>
     */
    public function blocks(): HasMany
    {
        return $this->hasMany(PageBlock::class)->orderBy('sort_order');
    }
}
