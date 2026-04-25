<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Inquiry;
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

        $this->assertSoftDeleted('products', ['id' => $product->id]);
        Storage::disk('public')->assertMissing('products/test.jpg');
    }

    public function test_deleting_product_linked_to_an_inquiry_soft_deletes_product_without_removing_image()
    {
        Storage::fake('public');
        $product = Product::factory()->create([
            'image_path' => 'products/linked.jpg',
        ]);
        Storage::disk('public')->put('products/linked.jpg', 'content');
        $inquiry = Inquiry::factory()->create();
        $inquiry->products()->attach($product->id, [
            'quantity' => 1,
            'feature_value' => null,
        ]);

        Livewire::test('admin.products')
            ->call('delete', $product->id)
            ->call('confirmDelete');

        $this->assertSoftDeleted('products', ['id' => $product->id]);
        Storage::disk('public')->assertExists('products/linked.jpg');
    }

    public function test_updating_product_linked_to_an_inquiry_creates_new_product_version()
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'name' => 'Originalprodukt',
            'slug' => 'originalprodukt',
        ]);
        $inquiry = Inquiry::factory()->create();
        $inquiry->products()->attach($product->id, [
            'quantity' => 2,
            'feature_value' => null,
        ]);

        Livewire::test('admin.products')
            ->call('edit', $product->id)
            ->set('name', 'Aktualisierte Version')
            ->call('save');

        $this->assertSoftDeleted('products', ['id' => $product->id]);

        $newProduct = Product::query()
            ->where('name', 'Aktualisierte Version')
            ->first();

        $this->assertNotNull($newProduct);
        $this->assertSame($product->id, $newProduct->supersedes_product_id);
        $this->assertNotSame($product->id, $newProduct->id);

        $product->refresh();
        $this->assertStringContainsString('-archived-'.$product->id, $product->slug);

        $this->assertDatabaseHas('inquiry_items', [
            'inquiry_id' => $inquiry->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);
    }

    public function test_show_inquiries_includes_current_and_previous_product_versions()
    {
        $category = Category::factory()->create();
        $previousVersion = Product::factory()->create([
            'category_id' => $category->id,
            'name' => 'Produkt v1',
            'slug' => 'produkt-v1',
        ]);
        $currentVersion = Product::factory()->create([
            'category_id' => $category->id,
            'name' => 'Produkt v2',
            'slug' => 'produkt-v2',
            'supersedes_product_id' => $previousVersion->id,
        ]);

        $previousVersion->delete();

        $oldInquiry = Inquiry::factory()->create([
            'first_name' => 'Anna',
            'last_name' => 'Alt',
            'company' => 'Alt GmbH',
            'created_at' => now()->subDays(2),
        ]);
        $oldInquiry->products()->attach($previousVersion->id, [
            'quantity' => 3,
            'feature_value' => null,
        ]);

        $newInquiry = Inquiry::factory()->create([
            'first_name' => 'Ben',
            'last_name' => 'Neu',
            'company' => 'Neu AG',
            'created_at' => now()->subDay(),
        ]);
        $newInquiry->products()->attach($currentVersion->id, [
            'quantity' => 1,
            'feature_value' => null,
        ]);

        Livewire::test('admin.products')
            ->call('showInquiries', $currentVersion->id)
            ->assertSet('inquiryHistoryProduct.id', $currentVersion->id)
            ->assertSet('productInquiries.0.inquiry_id', $newInquiry->id)
            ->assertSet('productInquiries.0.quantity', 1)
            ->assertSet('productInquiries.1.inquiry_id', $oldInquiry->id)
            ->assertSet('productInquiries.1.quantity', 3)
            ->assertSee(route('admin.inquiries', ['inquiry' => $newInquiry->id]))
            ->assertSee(route('admin.inquiries', ['inquiry' => $oldInquiry->id]))
            ->assertSee('Ben Neu')
            ->assertSee('Anna Alt');
    }
}
