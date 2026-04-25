<?php

namespace App\Models;

use Database\Factories\InquiryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Inquiry extends Model
{
    /** @use HasFactory<InquiryFactory> */
    use HasFactory;

    protected $fillable = [
        'salutation',
        'first_name',
        'last_name',
        'email',
        'phone',
        'message',
        'company',
        'address',
        'street',
        'postal_code',
        'city',
        'start_date',
        'end_date',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * @return BelongsToMany<Product, Inquiry>
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'inquiry_items')
            ->withPivot('quantity', 'feature_value')
            ->withTimestamps();
    }
}
