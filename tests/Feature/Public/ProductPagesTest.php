<?php

namespace Tests\Feature\Public;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['filesystems.default' => 'public']);
    }

    public function test_category_page_lists_products_and_links_to_details(): void
    {
        $category = Category::factory()->create(['name' => 'Medientechnik', 'slug' => 'medientechnik']);
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'name' => 'Samsung Display QM98F',
            'slug' => 'samsung-display-qm98f',
            'price' => 270.00,
        ]);

        $this->get(route('category.show', $category->slug))
            ->assertOk()
            ->assertSee('Medientechnik')
            ->assertSee('Samsung Display QM98F')
            ->assertSee(route('product.show', $product->slug), false)
            ->assertSee(__('details'))
            ->assertSee(__('addToInquiry'));
    }

    public function test_product_detail_page_displays_keywords_and_optional_feature_values(): void
    {
        $category = Category::factory()->create(['slug' => 'medientechnik']);
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'name' => 'InFocus INL3149WU',
            'slug' => 'infocus-inl3149wu',
            'keywords' => ['projektor', 'wuxga', '5500 lumen'],
            'feature_name' => 'Laufzeit',
            'feature_values' => ['1 Tag', 'Wochenende'],
        ]);

        $this->get(route('product.show', $product->slug))
            ->assertOk()
            ->assertSee('InFocus INL3149WU')
            ->assertSee('name="keywords"', false)
            ->assertSee('projektor, wuxga, 5500 lumen')
            ->assertSee('Laufzeit')
            ->assertSee('Wochenende')
            ->assertSee(route('category.show', $category->slug), false);
    }

    public function test_home_page_links_category_tile_to_category_page(): void
    {
        $category = Category::factory()->create(['name' => 'Audio', 'slug' => 'audio']);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee(route('category.show', $category->slug), false);
    }
}
