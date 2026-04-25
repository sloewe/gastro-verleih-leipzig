<?php

namespace Tests\Feature\Settings;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppearanceRemovalTest extends TestCase
{
    use RefreshDatabase;

    public function test_appearance_settings_route_is_not_available(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/settings/appearance')
            ->assertNotFound();
    }

    public function test_appearance_navigation_item_is_not_visible_in_settings(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('profile.edit'))
            ->assertOk()
            ->assertDontSeeText('Appearance');
    }

    public function test_admin_area_is_always_rendered_in_dark_mode(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $this->assertMatchesRegularExpression('/<html[^>]*class="dark"/', $response->getContent());
    }

    public function test_public_auth_pages_are_rendered_in_dark_mode(): void
    {
        $response = $this->get(route('login'));

        $response->assertOk();
        $this->assertMatchesRegularExpression('/<html[^>]*class="dark"/', $response->getContent());
    }
}
