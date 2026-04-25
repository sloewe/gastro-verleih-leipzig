<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\Categories;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CategoriesTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        config(['filesystems.default' => 'public']);
        $this->user = User::factory()->create();
    }

    public function test_can_render_categories_page(): void
    {
        $this->withoutExceptionHandling();
        $this->actingAs($this->user)
            ->get(route('admin.categories'))
            ->assertStatus(200)
            ->assertSeeLivewire(Categories::class);
    }

    public function test_can_create_category(): void
    {
        Livewire::actingAs($this->user)
            ->test(Categories::class)
            ->set('name', 'Test Category')
            ->set('slug', 'test-category')
            ->call('save')
            ->assertHasNoErrors()
            ->assertDispatched('modal-close', name: 'category-modal');

        $this->assertDatabaseHas('categories', [
            'name' => 'Test Category',
            'slug' => 'test-category',
        ]);
    }

    public function test_slug_is_automatically_generated(): void
    {
        Livewire::actingAs($this->user)
            ->test(Categories::class)
            ->set('name', 'My New Category')
            ->assertSet('slug', 'my-new-category');
    }

    public function test_can_edit_category(): void
    {
        $category = Category::factory()->create([
            'name' => 'Old Name',
            'slug' => 'old-name',
        ]);

        Livewire::actingAs($this->user)
            ->test(Categories::class)
            ->call('edit', $category->id)
            ->set('name', 'Updated Name')
            ->set('slug', 'updated-name')
            ->call('save')
            ->assertHasNoErrors()
            ->assertDispatched('modal-close', name: 'category-modal');

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Updated Name',
            'slug' => 'updated-name',
        ]);
    }

    public function test_can_delete_category(): void
    {
        $category = Category::factory()->create();

        Livewire::actingAs($this->user)
            ->test(Categories::class)
            ->call('delete', $category->id)
            ->call('confirmDelete')
            ->assertDispatched('modal-close', name: 'delete-confirmation');

        $this->assertDatabaseMissing('categories', [
            'id' => $category->id,
        ]);
    }

    public function test_cannot_delete_category_when_products_are_assigned(): void
    {
        $category = Category::factory()->create();
        Product::factory()->create(['category_id' => $category->id]);

        Livewire::actingAs($this->user)
            ->test(Categories::class)
            ->call('delete', $category->id)
            ->call('confirmDelete')
            ->assertDispatched('modal-close', name: 'delete-confirmation');

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
        ]);
    }

    public function test_validation_works(): void
    {
        Livewire::actingAs($this->user)
            ->test(Categories::class)
            ->set('name', '')
            ->set('slug', '')
            ->call('save')
            ->assertHasErrors(['name' => 'required', 'slug' => 'required']);
    }
}
