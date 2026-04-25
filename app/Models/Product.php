<?php

namespace App\Models;

use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;

    use SoftDeletes;

    protected $fillable = [
        'category_id',
        'supersedes_product_id',
        'name',
        'slug',
        'description',
        'keywords',
        'image_path',
        'price',
        'vat_rate',
        'feature_name',
        'feature_values',
    ];

    protected $casts = [
        'keywords' => 'array',
        'feature_values' => 'array',
        'price' => 'decimal:2',
        'vat_rate' => 'decimal:2',
    ];

    /**
     * @return BelongsTo<Category, Product>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @return BelongsTo<Product, Product>
     */
    public function supersededProduct(): BelongsTo
    {
        return $this->belongsTo(self::class, 'supersedes_product_id');
    }

    /**
     * @return BelongsToMany<Inquiry, Product>
     */
    public function inquiries(): BelongsToMany
    {
        return $this->belongsToMany(Inquiry::class, 'inquiry_items')
            ->withPivot('quantity', 'feature_value')
            ->withTimestamps();
    }
}
