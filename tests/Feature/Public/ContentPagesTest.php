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

    public function test_navigation_contains_only_pages_marked_for_navigation(): void
    {
        Page::query()->create([
            'title' => 'Impressum',
            'slug' => 'impressum',
            'show_in_navigation' => true,
            'navigation_label' => 'Rechtliches',
        ]);
        Page::query()->create([
            'title' => 'Datenschutz',
            'slug' => 'datenschutz',
            'show_in_navigation' => false,
            'navigation_label' => 'NichtImMenue',
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee(route('content.page', 'impressum'), false)
            ->assertSee('Rechtliches')
            ->assertDontSee('NichtImMenue');
    }

    public function test_navigation_uses_page_title_when_navigation_label_is_empty(): void
    {
        Page::query()->create([
            'title' => 'FAQ',
            'slug' => 'faq',
            'show_in_navigation' => true,
            'navigation_label' => null,
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee(route('content.page', 'faq'), false)
            ->assertSee('FAQ');
    }

    public function test_existing_category_routes_remain_accessible_with_slug_catch_all(): void
    {
        $category = Category::factory()->create(['name' => 'Audio', 'slug' => 'audio']);

        $this->get(route('category.show', $category->slug))
            ->assertOk()
            ->assertSee('Audio');
    }
}
