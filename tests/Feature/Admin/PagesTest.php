<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\Pages;
use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
            ->call('save')
            ->assertHasNoErrors();

        $page->refresh();

        $this->assertSame('Datenschutz aktualisiert', $page->title);
        $this->assertSame(
            ['Zweiter Block', 'Erster Block'],
            $page->blocks()->orderBy('sort_order')->pluck('content_markdown')->all()
        );
    }
}
