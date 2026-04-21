<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\Users;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class UsersTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_users_list(): void
    {
        $admin = User::factory()->create();
        $user = User::factory()->create(['name' => 'John Doe']);

        $this->actingAs($admin)
            ->get(route('admin.users'))
            ->assertStatus(200)
            ->assertSee('John Doe');
    }

    public function test_admin_can_create_user(): void
    {
        $admin = User::factory()->create();

        Livewire::actingAs($admin)
            ->test(Users::class)
            ->set('name', 'Jane Doe')
            ->set('email', 'jane@example.com')
            ->set('password', 'password123')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('users', [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
        ]);
    }

    public function test_admin_can_edit_user(): void
    {
        $admin = User::factory()->create();
        $user = User::factory()->create();

        Livewire::actingAs($admin)
            ->test(Users::class)
            ->call('edit', $user->id)
            ->set('name', 'Updated Name')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
        ]);
    }

    public function test_admin_can_delete_user(): void
    {
        $admin = User::factory()->create();
        $user = User::factory()->create();

        Livewire::actingAs($admin)
            ->test(Users::class)
            ->call('delete', $user->id)
            ->call('confirmDelete')
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }

    public function test_guest_cannot_access_admin_users(): void
    {
        $this->get(route('admin.users'))
            ->assertRedirect(route('login'));
    }
}
