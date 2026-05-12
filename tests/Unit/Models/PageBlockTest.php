<?php

namespace Tests\Unit\Models;

use App\Models\PageBlock;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PageBlockTest extends TestCase
{
    use RefreshDatabase;

    #[DataProvider('markdownNormalizationProvider')]
    public function test_it_normalizes_markdown_content(?string $input, ?string $expected): void
    {
        $pageBlock = new PageBlock;

        // We need to use reflection or just call the private method if we want to test it in isolation,
        // but since it's used in the 'set' attribute, we can just set the attribute.
        // However, PageBlock is an Eloquent model, so we should probably mock the database or just use a non-persisted instance.

        $pageBlock->content_markdown = $input;

        $this->assertEquals($expected, $pageBlock->content_markdown);
    }

    public static function markdownNormalizationProvider(): array
    {
        return [
            '##2 -> ## 2' => ['##2', '## 2'],
            '## 2 -> ## 2' => ['## 2', '## 2'],
            '#2 -> # 2' => ['#2', '# 2'],
            '# 2 -> 2' => ['# 2', '# 2'],
            '##A -> ## A' => ['##A', '## A'],
            '## A -> ## A' => ['## A', '## A'],
            '#A -> # A' => ['#A', '# A'],
            '# A -> A' => ['# A', '# A'],
            '## should not change' => ['##', '##'],
            '###3 -> ### 3' => ['###3', '### 3'],
            'null remains null' => [null, null], // wait, provider needs strings but can handle null if type hinted
        ];
    }
}
