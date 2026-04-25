<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class SetupTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test if the login page is accessible.
     */
    public function test_login_page_is_accessible(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('data-test="login-button"', false);
    }

    /**
     * Test if storage link is working.
     */
    public function test_storage_link_exists(): void
    {
        if (! file_exists(public_path('storage'))) {
            Artisan::call('storage:link');
        }

        $this->assertTrue(file_exists(public_path('storage')));
    }

    /**
     * Test if database is connected.
     */
    public function test_database_connection(): void
    {
        $this->assertTrue(\Schema::hasTable('users'));
    }
}
