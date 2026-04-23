<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PageBlock extends Model
{
    use HasFactory;

    protected $fillable = [
        'page_id',
        'type',
        'content_markdown',
        'sort_order',
    ];

    /**
     * @return BelongsTo<Page, PageBlock>
     */
    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }
}
