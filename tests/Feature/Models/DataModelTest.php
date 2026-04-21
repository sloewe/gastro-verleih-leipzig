<?php

namespace Tests\Feature\Models;

use App\Models\Category;
use App\Models\Inquiry;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DataModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_it_creates_categories_products_and_inquiries()
    {
        $category = Category::factory()
            ->has(Product::factory()->count(3))
            ->create(['name' => 'Test Category']);

        $this->assertDatabaseHas('categories', ['name' => 'Test Category']);
        $this->assertCount(3, $category->products);

        $product = $category->products->first();
        $inquiry = Inquiry::factory()->create();
        $inquiry->products()->attach($product->id, ['quantity' => 2]);

        $this->assertCount(1, $inquiry->products);
        $this->assertEquals(2, $inquiry->products->first()->pivot->quantity);
    }

    /** @test */
    public function test_product_has_correct_casts()
    {
        $product = Product::factory()->create([
            'keywords' => ['tag1', 'tag2'],
            'feature_values' => ['value1', 'value2'],
            'price' => 123.45,
        ]);

        $this->assertIsArray($product->keywords);
        $this->assertIsArray($product->feature_values);
        $this->assertEquals('123.45', $product->price);
    }
}
