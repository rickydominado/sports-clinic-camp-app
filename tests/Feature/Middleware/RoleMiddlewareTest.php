<?php

namespace Tests\Feature\Middleware;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Tests\TestCase;

class RoleMiddlewareTest extends TestCase
{
    private User $user;
    private User $admin;

    public function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['is_admin' => true]);
        $this->user = User::factory()->create();
    }

    /** @test */
    public function admin_cannot_access_user_dashboard()
    {
        $this->actingAs($this->admin)->get(route('dashboard'))
            ->assertStatus(403);
    }

    /** @test */
    public function user_cannot_access_admin_dashboard()
    {
        $this->actingAs($this->user)->get(route('admin.dashboard'))
            ->assertStatus(403);
    }

    /** @test */
    public function admin_is_redirected_to_admin_dashboard_upon_successful_authentication()
    {
        $response = $this->post('/login', [
            'email' => $this->admin->email,
            'password' => 'password',
        ]);

        $response
            ->assertStatus(302)
            ->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticated();
    }

    /** @test */
    public function user_is_redirected_to_user_dashboard_upon_successful_authentication()
    {
        $response = $this->post('/login', [
            'email' => $this->user->email,
            'password' => 'password',
        ]);

        $response
            ->assertStatus(302)
            ->assertRedirect(RouteServiceProvider::HOME);

        $this->assertAuthenticated();
    }
}
