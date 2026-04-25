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
        app()->setLocale('de');
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

    public function test_home_page_uses_high_contrast_header_navigation_styles()
    {
        $this->get(route('home'))
            ->assertStatus(200)
            ->assertSee('bg-white/95')
            ->assertSee('!text-gtc-green');
    }

    public function test_home_page_includes_mobile_navigation_trigger()
    {
        $this->get(route('home'))
            ->assertStatus(200)
            ->assertSee(__('menu'))
            ->assertSee('md:hidden');
    }

    public function test_home_page_moves_login_link_to_footer(): void
    {
        $response = $this->get(route('home'));

        $response
            ->assertStatus(200)
            ->assertSeeInOrder([
                __('Impressum'),
                __('Datenschutz'),
                __('Login'),
            ]);

        $this->assertSame(1, substr_count($response->getContent(), __('Login')));
    }

    public function test_home_page_products_dropdown_includes_outside_click_close_behavior(): void
    {
        $this->get(route('home'))
            ->assertStatus(200)
            ->assertSee('data-public-products-dropdown', false)
            ->assertSee('productsDropdown.open = false;', false);
    }
}
