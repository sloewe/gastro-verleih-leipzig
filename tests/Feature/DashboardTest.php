<?php

namespace Tests\Feature;

use App\Livewire\Admin\Dashboard;
use App\Models\Inquiry;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Livewire\Livewire;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app()->setLocale('de');
    }

    public function test_guests_are_redirected_to_the_login_page(): void
    {
        $response = $this->get(route('dashboard'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_users_can_visit_the_dashboard(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('dashboard'));
        $response->assertOk();
    }

    public function test_dashboard_displays_widget_sections(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('dashboard'));

        $response->assertOk();
        $response->assertSeeText('Neue Anfragen (24h)');
        $response->assertSeeText('Aktive Anfragen');
        $response->assertSeeText('Neueste Anfragen');
        $response->assertSeeText('Anfragen pro Monat (12 Monate)');
        $response->assertSeeText('Top Produkte (30 Tage)');
        $response->assertSeeText('Umsatzkennzahlen');
        $response->assertSeeText('Aktive Anfragen nach Status');
        $response->assertSeeText('Sortiment-Impulse');
        $response->assertDontSeeText('Abschlussquote (30 Tage)');
        $response->assertDontSeeText('Pipeline-Effizienz (30 Tage)');
    }

    public function test_dashboard_calculates_revenue_and_top_product_metrics(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $completedProduct = Product::factory()->create(['name' => 'Klapptisch Premium', 'price' => 100]);
        $quoteProduct = Product::factory()->create(['name' => 'Biertisch Standard', 'price' => 50]);

        $completedInquiry = Inquiry::factory()->create([
            'status' => 'completed',
            'created_at' => now()->subDays(2),
            'updated_at' => now(),
        ]);
        $completedInquiry->products()->attach($completedProduct->id, ['quantity' => 3, 'feature_value' => null]);

        $quoteInquiry = Inquiry::factory()->create([
            'status' => 'quote_created',
            'created_at' => now()->subDay(),
        ]);
        $quoteInquiry->products()->attach($quoteProduct->id, ['quantity' => 2, 'feature_value' => null]);

        $response = $this->get(route('dashboard'));

        $response->assertOk();
        $response->assertSeeText('300,00 EUR');
        $response->assertSeeText('100,00 EUR');
        $response->assertSeeText('Klapptisch Premium');
    }

    public function test_dashboard_displays_monthly_inquiry_counts_across_months(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 4, 15, 10, 0, 0));

        try {
            $user = User::factory()->create();
            $this->actingAs($user);

            Inquiry::factory()->create(['created_at' => Carbon::now()->subMonths(2)->startOfMonth()->addDays(2)]);
            Inquiry::factory()->create(['created_at' => Carbon::now()->subMonths(2)->startOfMonth()->addDays(10)]);
            Inquiry::factory()->create(['created_at' => Carbon::now()->subMonth()->startOfMonth()->addDays(5)]);

            $response = $this->get(route('dashboard'));

            $response->assertOk();
            $response->assertSeeTextInOrder(['04/2026', '03/2026', '02/2026']);
            $response->assertSee('>2<', false);
            $response->assertSee('>1<', false);
        } finally {
            Carbon::setTestNow();
        }
    }

    public function test_dashboard_displays_monthly_revenue_with_latest_month_first(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 4, 15, 10, 0, 0));

        try {
            $user = User::factory()->create();
            $this->actingAs($user);

            $product = Product::factory()->create(['price' => 100]);

            $currentMonthInquiry = Inquiry::factory()->create([
                'status' => 'completed',
                'created_at' => Carbon::now()->startOfMonth()->addDays(2),
            ]);
            $currentMonthInquiry->products()->attach($product->id, ['quantity' => 1, 'feature_value' => null]);

            $lastMonthInquiry = Inquiry::factory()->create([
                'status' => 'completed',
                'created_at' => Carbon::now()->subMonth()->startOfMonth()->addDays(2),
            ]);
            $lastMonthInquiry->products()->attach($product->id, ['quantity' => 1, 'feature_value' => null]);

            $twoMonthsAgoInquiry = Inquiry::factory()->create([
                'status' => 'completed',
                'created_at' => Carbon::now()->subMonths(2)->startOfMonth()->addDays(2),
            ]);
            $twoMonthsAgoInquiry->products()->attach($product->id, ['quantity' => 1, 'feature_value' => null]);

            $response = $this->get(route('dashboard'));

            $response->assertOk();
            $response->assertSeeInOrder([
                'monthly-revenue-04/2026',
                'monthly-revenue-03/2026',
                'monthly-revenue-02/2026',
            ], false);
        } finally {
            Carbon::setTestNow();
        }
    }

    public function test_dashboard_opens_selected_inquiry_details_in_modal(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $product = Product::factory()->create(['name' => 'Stehtisch Classic']);

        $inquiry = Inquiry::factory()->create([
            'first_name' => 'Max',
            'last_name' => 'Mustermann',
            'email' => 'max@example.test',
            'message' => 'Bitte mit Lieferung.',
        ]);
        $inquiry->products()->attach($product->id, ['quantity' => 2, 'feature_value' => 'Buche']);

        Livewire::test(Dashboard::class)
            ->call('selectInquiry', $inquiry->id)
            ->assertSet('selectedInquiryId', $inquiry->id)
            ->assertSeeText('Anfrage #'.$inquiry->id)
            ->assertSeeText('Max Mustermann')
            ->assertSeeText('Bitte mit Lieferung.')
            ->assertSeeText('Stehtisch Classic')
            ->assertDispatched('modal-show', name: 'dashboard-inquiry-details-modal');
    }
}
