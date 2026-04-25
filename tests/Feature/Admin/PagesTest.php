<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\Pages;
use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;
use Tests\TestCase;

class PagesTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_render_pages_admin_screen(): void
    {
        $this->actingAs($this->user)
            ->get(route('admin.pages'))
            ->assertOk()
            ->assertSeeLivewire(Pages::class);
    }

    public function test_can_create_page_with_multiple_markdown_blocks(): void
    {
        Livewire::actingAs($this->user)
            ->test(Pages::class)
            ->set('title', 'Impressum')
            ->set('slug', 'impressum')
            ->set('show_in_navigation', true)
            ->set('navigation_label', 'Rechtliches')
            ->set('blocks', [
                ['id' => null, 'type' => 'markdown', 'content_markdown' => '# Abschnitt 1'],
                ['id' => null, 'type' => 'markdown', 'content_markdown' => 'Text für Abschnitt 2'],
            ])
            ->call('save')
            ->assertHasNoErrors();

        $page = Page::query()->where('slug', 'impressum')->firstOrFail();

        $this->assertDatabaseHas('pages', [
            'id' => $page->id,
            'title' => 'Impressum',
            'slug' => 'impressum',
            'show_in_navigation' => true,
            'navigation_label' => 'Rechtliches',
        ]);
        $this->assertDatabaseHas('page_blocks', [
            'page_id' => $page->id,
            'sort_order' => 1,
            'type' => 'markdown',
            'content_markdown' => '# Abschnitt 1',
        ]);
        $this->assertDatabaseHas('page_blocks', [
            'page_id' => $page->id,
            'sort_order' => 2,
            'type' => 'markdown',
            'content_markdown' => 'Text für Abschnitt 2',
        ]);
    }

    public function test_can_update_page_and_reorder_blocks(): void
    {
        $page = Page::query()->create([
            'title' => 'Datenschutz',
            'slug' => 'datenschutz',
            'show_in_navigation' => true,
            'navigation_label' => 'Datenschutz',
        ]);

        $page->blocks()->createMany([
            ['type' => 'markdown', 'content_markdown' => 'Erster Block', 'sort_order' => 1],
            ['type' => 'markdown', 'content_markdown' => 'Zweiter Block', 'sort_order' => 2],
        ]);

        Livewire::actingAs($this->user)
            ->test(Pages::class)
            ->call('edit', $page->id)
            ->call('moveBlockUp', 1)
            ->set('title', 'Datenschutz aktualisiert')
            ->set('show_in_navigation', false)
            ->set('navigation_label', 'Datenschutzmenü')
            ->call('save')
            ->assertHasNoErrors();

        $page->refresh();

        $this->assertSame('Datenschutz aktualisiert', $page->title);
        $this->assertFalse((bool) $page->show_in_navigation);
        $this->assertNull($page->navigation_label);
        $this->assertSame(
            ['Zweiter Block', 'Erster Block'],
            $page->blocks()->orderBy('sort_order')->pluck('content_markdown')->all()
        );
    }

    public function test_markdown_headings_are_normalized_when_saving_blocks(): void
    {
        Livewire::actingAs($this->user)
            ->test(Pages::class)
            ->set('title', 'Impressum')
            ->set('slug', 'impressum')
            ->set('blocks', [
                ['id' => null, 'type' => 'markdown', 'content_markdown' => '#Firmenangaben'],
            ])
            ->call('save')
            ->assertHasNoErrors();

        $page = Page::query()->where('slug', 'impressum')->firstOrFail();

        $this->assertDatabaseHas('page_blocks', [
            'page_id' => $page->id,
            'sort_order' => 1,
            'type' => 'markdown',
            'content_markdown' => '# Firmenangaben',
        ]);
    }

    public function test_editor_modal_is_opened_for_create_and_edit_actions(): void
    {
        $page = Page::query()->create([
            'title' => 'Impressum',
            'slug' => 'impressum',
        ]);

        Livewire::actingAs($this->user)
            ->test(Pages::class)
            ->call('create')
            ->assertDispatched('modal-show', name: 'page-editor-modal')
            ->call('edit', $page->id)
            ->assertDispatched('modal-show', name: 'page-editor-modal');
    }

    public function test_navigation_cache_is_cleared_when_page_is_created(): void
    {
        Cache::put('navigation.categories', collect(['cached']));
        Cache::put('navigation.pages', collect(['cached']));

        Livewire::actingAs($this->user)
            ->test(Pages::class)
            ->set('title', 'Kontakt')
            ->set('slug', 'kontakt')
            ->set('show_in_navigation', true)
            ->set('navigation_label', 'Kontakt')
            ->set('blocks', [
                ['id' => null, 'type' => 'markdown', 'content_markdown' => 'Kontakttext'],
            ])
            ->call('save')
            ->assertHasNoErrors();

        $this->assertFalse(Cache::has('navigation.categories'));
        $this->assertFalse(Cache::has('navigation.pages'));
    }

    public function test_navigation_cache_is_cleared_when_page_is_updated(): void
    {
        $page = Page::query()->create([
            'title' => 'Kontakt',
            'slug' => 'kontakt',
            'show_in_navigation' => true,
            'navigation_label' => 'Kontakt',
        ]);

        $page->blocks()->create([
            'type' => 'markdown',
            'content_markdown' => 'Alt',
            'sort_order' => 1,
        ]);

        Cache::put('navigation.categories', collect(['cached']));
        Cache::put('navigation.pages', collect(['cached']));

        Livewire::actingAs($this->user)
            ->test(Pages::class)
            ->call('edit', $page->id)
            ->set('title', 'Kontakt Neu')
            ->set('slug', 'kontakt-neu')
            ->set('show_in_navigation', true)
            ->set('navigation_label', 'Kontakt Neu')
            ->set('blocks', [
                ['id' => null, 'type' => 'markdown', 'content_markdown' => 'Neu'],
            ])
            ->call('save')
            ->assertHasNoErrors();

        $this->assertFalse(Cache::has('navigation.categories'));
        $this->assertFalse(Cache::has('navigation.pages'));
    }
}
