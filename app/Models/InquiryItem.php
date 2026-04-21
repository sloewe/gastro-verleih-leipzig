<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class InquiryItem extends Pivot
{
    protected $table = 'inquiry_items';

    protected $fillable = [
        'inquiry_id',
        'product_id',
        'quantity',
    ];

    /**
     * @return BelongsTo<Inquiry, InquiryItem>
     */
    public function inquiry(): BelongsTo
    {
        return $this->belongsTo(Inquiry::class);
    }

    /**
     * @return BelongsTo<Product, InquiryItem>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
