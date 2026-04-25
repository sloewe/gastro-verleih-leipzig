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

    protected function setUp(): void
    {
        parent::setUp();

        app()->setLocale('de');
    }

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

    public function test_inquiry_details_show_customer_data_and_items(): void
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

    public function test_inquiry_id_is_visible_in_table(): void
    {
        $this->actingAs(User::factory()->create());

        $inquiry = Inquiry::factory()->create();

        $this->get(route('admin.inquiries'))
            ->assertOk()
            ->assertSee('#'.$inquiry->id);
    }

    public function test_inquiry_period_is_visible_in_table(): void
    {
        $this->actingAs(User::factory()->create());

        Inquiry::factory()->create([
            'start_date' => '2026-06-01',
            'end_date' => '2026-06-03',
        ]);

        $this->get(route('admin.inquiries'))
            ->assertOk()
            ->assertSee((string) __('inquiryPeriod'))
            ->assertSee('01.06.2026 - 03.06.2026');
    }

    public function test_sorting_can_be_toggled_for_received_date(): void
    {
        $this->actingAs(User::factory()->create());

        $olderInquiry = Inquiry::factory()->create([
            'email' => 'older-toggle@example.com',
            'created_at' => now()->subDay(),
        ]);
        $newerInquiry = Inquiry::factory()->create([
            'email' => 'newer-toggle@example.com',
            'created_at' => now(),
        ]);

        Livewire::test(Inquiries::class)
            ->assertSeeInOrder([$newerInquiry->email, $olderInquiry->email])
            ->call('sortBy', 'created_at')
            ->assertSeeInOrder([$olderInquiry->email, $newerInquiry->email])
            ->call('sortBy', 'created_at')
            ->assertSeeInOrder([$newerInquiry->email, $olderInquiry->email]);
    }

    public function test_sorting_by_inquiry_period_uses_start_date(): void
    {
        $this->actingAs(User::factory()->create());

        $earlierPeriodInquiry = Inquiry::factory()->create([
            'email' => 'earlier-period@example.com',
            'start_date' => '2026-05-01',
            'end_date' => '2026-05-03',
            'created_at' => now(),
        ]);
        $laterPeriodInquiry = Inquiry::factory()->create([
            'email' => 'later-period@example.com',
            'start_date' => '2026-07-01',
            'end_date' => '2026-07-03',
            'created_at' => now()->subDay(),
        ]);

        Livewire::test(Inquiries::class)
            ->call('sortBy', 'start_date')
            ->assertSeeInOrder([$laterPeriodInquiry->email, $earlierPeriodInquiry->email])
            ->call('sortBy', 'start_date')
            ->assertSeeInOrder([$earlierPeriodInquiry->email, $laterPeriodInquiry->email]);
    }

    public function test_inquiry_query_parameter_preselects_inquiry_details(): void
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
            ->assertDispatched('modal-show', name: 'inquiry-details-modal')
            ->assertSee('Anfrage #'.$olderInquiry->id)
            ->assertSee('Anna Alt');
    }
}
