<?php

namespace Tests\Feature\Public;

use App\Mail\InquiryAdminNotificationMail;
use App\Mail\InquiryCustomerConfirmationMail;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class InquiryCheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_route_redirects_to_inquiry_list_when_session_is_empty(): void
    {
        $response = $this->get(route('inquiry.checkout'));

        $response->assertRedirect(route('inquiry.list'));
    }

    public function test_checkout_page_can_be_opened_with_session_items(): void
    {
        $product = Product::factory()->create();

        $response = $this->withSession([
            'inquiry_list.items' => [
                [
                    'key' => $product->id.'|',
                    'product_id' => $product->id,
                    'feature_value' => '',
                    'quantity' => 1,
                ],
            ],
        ])->get(route('inquiry.checkout'));

        $response
            ->assertOk()
            ->assertSee('Anfrage absenden');
    }

    public function test_checkout_validation_is_applied_for_required_fields(): void
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

        Livewire::test('public.inquiry-checkout')
            ->set('salutation', '')
            ->set('first_name', '')
            ->set('last_name', '')
            ->set('email', 'not-an-email')
            ->set('street', '')
            ->set('postal_code', '')
            ->set('city', '')
            ->set('start_date', '')
            ->set('end_date', '')
            ->call('submit')
            ->assertHasErrors([
                'salutation' => ['required'],
                'first_name' => ['required'],
                'last_name' => ['required'],
                'email' => ['email'],
                'street' => ['required'],
                'postal_code' => ['required'],
                'city' => ['required'],
                'start_date' => ['required'],
                'end_date' => ['required'],
            ]);
    }

    public function test_checkout_submit_persists_inquiry_and_triggers_mails(): void
    {
        Mail::fake();
        $product = Product::factory()->create(['name' => 'Kuehlschrank']);

        $this->withSession([
            'inquiry_list.items' => [
                [
                    'key' => $product->id.'|Wochenende',
                    'product_id' => $product->id,
                    'feature_value' => 'Wochenende',
                    'quantity' => 2,
                ],
            ],
        ]);

        Livewire::test('public.inquiry-checkout')
            ->set('salutation', 'Herr')
            ->set('first_name', 'Max')
            ->set('last_name', 'Mustermann')
            ->set('email', 'max@example.com')
            ->set('phone', '+49123123')
            ->set('company', 'Musterfirma GmbH')
            ->set('street', 'Musterstrasse 1')
            ->set('postal_code', '04109')
            ->set('city', 'Leipzig')
            ->set('start_date', '2026-06-10')
            ->set('end_date', '2026-06-13')
            ->set('message', 'Bitte Rueckmeldung per E-Mail.')
            ->call('submit')
            ->assertRedirect(route('inquiry.thank-you'));

        $this->assertDatabaseHas('inquiries', [
            'salutation' => 'Herr',
            'first_name' => 'Max',
            'last_name' => 'Mustermann',
            'email' => 'max@example.com',
            'street' => 'Musterstrasse 1',
            'postal_code' => '04109',
            'city' => 'Leipzig',
            'start_date' => '2026-06-10 00:00:00',
            'end_date' => '2026-06-13 00:00:00',
        ]);

        $this->assertDatabaseHas('inquiry_items', [
            'product_id' => $product->id,
            'quantity' => 2,
            'feature_value' => 'Wochenende',
        ]);

        Mail::assertSent(InquiryCustomerConfirmationMail::class);
        Mail::assertSent(InquiryAdminNotificationMail::class);

        $this->assertSame([], session('inquiry_list.items', []));
    }

    public function test_checkout_submit_redirects_when_only_invalid_session_items_exist(): void
    {
        $this->withSession([
            'inquiry_list.items' => [
                [
                    'key' => '999999|',
                    'product_id' => 999999,
                    'feature_value' => '',
                    'quantity' => 1,
                ],
            ],
        ]);

        Livewire::test('public.inquiry-checkout')
            ->set('salutation', 'Herr')
            ->set('first_name', 'Max')
            ->set('last_name', 'Mustermann')
            ->set('email', 'max@example.com')
            ->set('street', 'Musterstrasse 1')
            ->set('postal_code', '04109')
            ->set('city', 'Leipzig')
            ->set('start_date', '2026-06-10')
            ->set('end_date', '2026-06-11')
            ->call('submit')
            ->assertRedirect(route('inquiry.list'));

        $this->assertDatabaseCount('inquiries', 0);
    }

    public function test_checkout_displays_net_vat_and_gross_amounts_in_sidebar(): void
    {
        $product = Product::factory()->create([
            'price' => 10.00,
            'vat_rate' => 19.00,
        ]);

        $response = $this->withSession([
            'inquiry_list.items' => [
                [
                    'key' => $product->id.'|',
                    'product_id' => $product->id,
                    'feature_value' => '',
                    'quantity' => 2,
                ],
            ],
        ])->get(route('inquiry.checkout'));

        $response
            ->assertOk()
            ->assertSee('20,00 €')
            ->assertSee('3,80 €')
            ->assertSee('23,80 €')
            ->assertSee('Mehrwertsteuer')
            ->assertSee('Bruttosumme');
    }

    public function test_checkout_validation_rejects_end_date_before_start_date(): void
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

        Livewire::test('public.inquiry-checkout')
            ->set('salutation', 'Herr')
            ->set('first_name', 'Max')
            ->set('last_name', 'Mustermann')
            ->set('email', 'max@example.com')
            ->set('street', 'Musterstrasse 1')
            ->set('postal_code', '04109')
            ->set('city', 'Leipzig')
            ->set('start_date', '2026-06-12')
            ->set('end_date', '2026-06-10')
            ->call('submit')
            ->assertHasErrors([
                'end_date' => ['after_or_equal'],
            ]);
    }
}
