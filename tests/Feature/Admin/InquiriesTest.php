<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\Inquiries;
use App\Models\Inquiry;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class InquiriesTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_inquiries_page(): void
    {
        $response = $this->get(route('admin.inquiries'));

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_access_inquiries_page(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('admin.inquiries'))
            ->assertOk()
            ->assertSeeLivewire(Inquiries::class);
    }

    public function test_inquiries_are_listed_latest_first(): void
    {
        $this->actingAs(User::factory()->create());

        $olderInquiry = Inquiry::factory()->create([
            'email' => 'older@example.com',
            'created_at' => now()->subDay(),
        ]);
        $newerInquiry = Inquiry::factory()->create([
            'email' => 'newer@example.com',
            'created_at' => now(),
        ]);

        $response = $this->get(route('admin.inquiries'));

        $response->assertOk();
        $response->assertSeeInOrder([$newerInquiry->email, $olderInquiry->email]);
    }

    public function test_detail_panel_shows_inquiry_customer_data_and_items(): void
    {
        $this->actingAs(User::factory()->create());

        $product = Product::factory()->create([
            'name' => 'Kuehltheke',
        ]);
        $inquiry = Inquiry::factory()->create([
            'first_name' => 'Max',
            'last_name' => 'Mustermann',
            'message' => 'Bitte Rueckruf.',
        ]);
        $inquiry->products()->attach($product->id, [
            'quantity' => 3,
            'feature_value' => 'Silber',
        ]);

        Livewire::test(Inquiries::class)
            ->call('selectInquiry', $inquiry->id)
            ->assertSee('Max Mustermann')
            ->assertSee('Bitte Rueckruf.')
            ->assertSee('Kuehltheke')
            ->assertSee('Silber')
            ->assertSee('Menge: 3');
    }

    public function test_status_can_be_updated_and_is_persisted(): void
    {
        $this->actingAs(User::factory()->create());

        $inquiry = Inquiry::factory()->create([
            'status' => 'new',
        ]);

        Livewire::test(Inquiries::class)
            ->call('updateStatus', $inquiry->id, 'quote_created')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('inquiries', [
            'id' => $inquiry->id,
            'status' => 'quote_created',
        ]);
    }

    public function test_inquiry_query_parameter_preselects_detail_panel(): void
    {
        $this->actingAs(User::factory()->create());

        $olderInquiry = Inquiry::factory()->create([
            'first_name' => 'Anna',
            'last_name' => 'Alt',
            'created_at' => now()->subDay(),
        ]);
        Inquiry::factory()->create([
            'first_name' => 'Ben',
            'last_name' => 'Neu',
            'created_at' => now(),
        ]);

        Livewire::withQueryParams(['inquiry' => $olderInquiry->id])
            ->test(Inquiries::class)
            ->assertSet('selectedInquiryId', $olderInquiry->id)
            ->assertSee('Anfrage #'.$olderInquiry->id)
            ->assertSee('Anna Alt');
    }
}
