<?php

namespace Tests\Feature\Public;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class InquiryListTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_add_product_to_inquiry_list_from_product_detail_page(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'feature_name' => 'Laufzeit',
            'feature_values' => ['1 Tag', 'Wochenende'],
        ]);

        Livewire::test('public.product-details', ['slug' => $product->slug])
            ->set('selectedFeatureValue', 'Wochenende')
            ->call('addToInquiryList')
            ->assertDispatched('inquiry-list-updated');

        $this->assertEquals([
            [
                'key' => $product->id.'|Wochenende',
                'product_id' => $product->id,
                'feature_value' => 'Wochenende',
                'quantity' => 1,
            ],
        ], session('inquiry_list.items'));
    }

    public function test_product_detail_page_shows_responsive_sections_content(): void
    {
        $category = Category::factory()->create(['name' => 'Medien & Videotechnik']);
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'name' => 'Samsung Display QM98F',
            'price' => 270.00,
            'vat_rate' => 19.00,
            'description' => "Display & Bild\nDiagonale: 98\"",
        ]);

        $response = $this->get(route('product.show', $product->slug));

        $response
            ->assertOk()
            ->assertSee('Medien &amp; Videotechnik', false)
            ->assertSee('Samsung Display QM98F')
            ->assertSee('270,00 €')
            ->assertSee('Beschreibung')
            ->assertSee('Display &amp; Bild', false);
    }

    public function test_guest_can_add_product_to_inquiry_list_from_category_page(): void
    {
        $category = Category::factory()->create(['slug' => 'medientechnik']);
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'feature_name' => null,
            'feature_values' => null,
        ]);

        Livewire::test('public.category-products', ['slug' => $category->slug])
            ->call('addToInquiryList', $product->id)
            ->assertDispatched('inquiry-list-updated');

        $this->assertEquals([
            [
                'key' => $product->id.'|',
                'product_id' => $product->id,
                'feature_value' => '',
                'quantity' => 1,
            ],
        ], session('inquiry_list.items'));
    }

    public function test_inquiry_list_quantity_can_be_changed_and_item_can_be_removed(): void
    {
        $product = Product::factory()->create();

        $this->withSession([
            'inquiry_list.items' => [
                [
                    'key' => $product->id.'|',
                    'product_id' => $product->id,
                    'feature_value' => '',
                    'quantity' => 1,
                ],
            ],
        ]);

        Livewire::test('public.inquiry-list')
            ->call('increaseQuantity', $product->id.'|')
            ->assertDispatched('inquiry-list-updated');

        $this->assertEquals(2, session('inquiry_list.items.0.quantity'));

        Livewire::test('public.inquiry-list')
            ->call('decreaseQuantity', $product->id.'|')
            ->assertDispatched('inquiry-list-updated');

        $this->assertEquals(1, session('inquiry_list.items.0.quantity'));

        Livewire::test('public.inquiry-list')
            ->call('removeItem', $product->id.'|')
            ->assertDispatched('inquiry-list-updated');

        $this->assertSame([], session('inquiry_list.items'));
    }

    public function test_inquiry_list_page_renders_session_items_for_guests(): void
    {
        $product = Product::factory()->create([
            'name' => 'Mietkuehlschrank',
            'slug' => 'mietkuehlschrank',
            'price' => 39.90,
            'vat_rate' => 19.00,
        ]);

        $this->withSession([
            'inquiry_list.items' => [
                [
                    'key' => $product->id.'|',
                    'product_id' => $product->id,
                    'feature_value' => '',
                    'quantity' => 2,
                ],
            ],
        ]);

        $response = $this->get(route('inquiry.list'));

        $response
            ->assertOk()
            ->assertSee('Ihre Anfrageliste')
            ->assertSee('Mietkuehlschrank')
            ->assertSee('79,80 €')
            ->assertSee('15,16 €')
            ->assertSee('94,96 €');
    }

    public function test_inquiry_list_summary_uses_product_specific_vat_rates(): void
    {
        $firstProduct = Product::factory()->create([
            'price' => 100.00,
            'vat_rate' => 7.00,
        ]);
        $secondProduct = Product::factory()->create([
            'price' => 50.00,
            'vat_rate' => 19.00,
        ]);

        $this->withSession([
            'inquiry_list.items' => [
                [
                    'key' => $firstProduct->id.'|',
                    'product_id' => $firstProduct->id,
                    'feature_value' => '',
                    'quantity' => 1,
                ],
                [
                    'key' => $secondProduct->id.'|',
                    'product_id' => $secondProduct->id,
                    'feature_value' => '',
                    'quantity' => 2,
                ],
            ],
        ]);

        $response = $this->get(route('inquiry.list'));

        $response
            ->assertOk()
            ->assertSee('Nettopreis')
            ->assertSee('200,00 €')
            ->assertSee('26,00 €')
            ->assertSee('226,00 €');
    }

    public function test_header_shows_inquiry_list_badge_with_total_item_quantity(): void
    {
        $firstProduct = Product::factory()->create();
        $secondProduct = Product::factory()->create();

        $response = $this->withSession([
            'inquiry_list.items' => [
                [
                    'key' => $firstProduct->id.'|',
                    'product_id' => $firstProduct->id,
                    'feature_value' => '',
                    'quantity' => 2,
                ],
                [
                    'key' => $secondProduct->id.'|',
                    'product_id' => $secondProduct->id,
                    'feature_value' => '',
                    'quantity' => 3,
                ],
            ],
        ])->get(route('home'));

        $response
            ->assertOk()
            ->assertSee('data-inquiry-count-badge', false)
            ->assertSee('data-inquiry-count="5"', false);
    }

    public function test_header_hides_inquiry_list_badge_when_inquiry_list_is_empty(): void
    {
        $response = $this->get(route('home'));

        $response
            ->assertOk()
            ->assertDontSee('data-inquiry-count-badge', false);
    }

    public function test_inquiry_list_badge_component_recalculates_count_when_update_event_is_dispatched(): void
    {
        $firstProduct = Product::factory()->create();
        $secondProduct = Product::factory()->create();

        $this->withSession([
            'inquiry_list.items' => [
                [
                    'key' => $firstProduct->id.'|',
                    'product_id' => $firstProduct->id,
                    'feature_value' => '',
                    'quantity' => 1,
                ],
            ],
        ]);

        Livewire::test('public.inquiry-list-badge')
            ->assertSet('count', 1);

        session()->put('inquiry_list.items', [
            [
                'key' => $firstProduct->id.'|',
                'product_id' => $firstProduct->id,
                'feature_value' => '',
                'quantity' => 2,
            ],
            [
                'key' => $secondProduct->id.'|',
                'product_id' => $secondProduct->id,
                'feature_value' => '',
                'quantity' => 3,
            ],
        ]);

        Livewire::test('public.inquiry-list-badge')
            ->dispatch('inquiry-list-updated')
            ->assertSet('count', 5)
            ->assertSee('data-inquiry-count="5"', false);
    }
}
