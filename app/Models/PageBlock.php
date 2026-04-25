<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
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

    protected function contentMarkdown(): Attribute
    {
        return Attribute::make(
            set: fn (?string $value): ?string => $this->normalizeMarkdown($value),
        );
    }

    private function normalizeMarkdown(?string $markdown): ?string
    {
        if ($markdown === null) {
            return null;
        }

        return preg_replace('/^(#{1,6})(\S)/m', '$1 $2', $markdown) ?? $markdown;
    }

    /**
     * @return BelongsTo<Page, PageBlock>
     */
    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }
}
