<?php

namespace Tests\Feature\Public;

use App\Models\Category;
use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContentPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_slug_route_renders_markdown_blocks(): void
    {
        $page = Page::query()->create([
            'title' => 'Impressum',
            'slug' => 'impressum',
        ]);

        $page->blocks()->createMany([
            ['type' => 'markdown', 'content_markdown' => '# Firmenangaben', 'sort_order' => 1],
            ['type' => 'markdown', 'content_markdown' => 'Musterstraße 1', 'sort_order' => 2],
        ]);

        $this->get('/impressum')
            ->assertOk()
            ->assertSee('Impressum')
            ->assertSee('Firmenangaben')
            ->assertSee('Musterstraße 1');
    }

    public function test_footer_links_point_to_cms_slug_routes(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee(route('content.page', 'impressum'), false)
            ->assertSee(route('content.page', 'datenschutz'), false);
    }

    public function test_existing_category_routes_remain_accessible_with_slug_catch_all(): void
    {
        $category = Category::factory()->create(['name' => 'Audio', 'slug' => 'audio']);

        $this->get(route('category.show', $category->slug))
            ->assertOk()
            ->assertSee('Audio');
    }
}
