<?php

namespace Tests\Feature\Public;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class HomeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['filesystems.default' => 'public']);
    }

    public function test_can_view_home_page()
    {
        $this->get(route('home'))
            ->assertStatus(200)
            ->assertSee('Entdecke unser Sortiment');
    }

    public function test_home_page_shows_categories()
    {
        $category = Category::factory()->create(['name' => 'Kaffeevollautomaten']);

        Livewire::test('home')
            ->assertSee('Kaffeevollautomaten');
    }
}
