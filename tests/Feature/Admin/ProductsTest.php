<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ProductsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['filesystems.default' => 'public']);
        $this->actingAs(User::factory()->create());
    }

    public function test_can_view_products_page()
    {
        $this->get(route('admin.products'))
            ->assertStatus(200);
    }

    public function test_can_create_product()
    {
        Storage::fake('public');
        $category = Category::factory()->create();
        $image = UploadedFile::fake()->image('product.jpg');

        Livewire::test('admin.products')
            ->set('category_id', $category->id)
            ->set('name', 'Test Produkt')
            ->set('price', 19.99)
            ->set('vat_rate', 7.00)
            ->set('description', 'Beschreibung')
            ->set('keywords', 'key1, key2')
            ->set('image', $image)
            ->set('feature_name', 'Farbe')
            ->set('feature_values', 'Rot, Blau')
            ->call('save');

        $this->assertDatabaseHas('products', [
            'name' => 'Test Produkt',
            'price' => 19.99,
            'vat_rate' => 7.00,
            'category_id' => $category->id,
            'feature_name' => 'Farbe',
        ]);

        $product = Product::where('name', 'Test Produkt')->first();
        $this->assertEquals(['key1', 'key2'], $product->keywords);
        $this->assertEquals(['Rot', 'Blau'], $product->feature_values);
        $this->assertNotNull($product->image_path);
        Storage::disk('public')->assertExists($product->image_path);
    }

    public function test_can_update_product()
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);

        Livewire::test('admin.products')
            ->call('edit', $product->id)
            ->set('name', 'Aktualisiertes Produkt')
            ->call('save');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Aktualisiertes Produkt',
        ]);
    }

    public function test_can_delete_product()
    {
        Storage::fake('public');
        $product = Product::factory()->create([
            'image_path' => 'products/test.jpg',
        ]);
        Storage::disk('public')->put('products/test.jpg', 'content');

        Livewire::test('admin.products')
            ->call('delete', $product->id)
            ->call('confirmDelete');

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
        Storage::disk('public')->assertMissing('products/test.jpg');
    }
}
