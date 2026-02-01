<?php

namespace Tests\Feature\Middleware;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlockedMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_user_can_access_protected_routes(): void
    {
        $user = User::factory()->create(['is_blocked' => false]);

        // Use a route with 'blocked' middleware (/cart uses auth + blocked)
        $response = $this->actingAs($user)->get('/cart');

        $response->assertStatus(200);
    }

    public function test_blocked_user_is_logged_out_and_redirected(): void
    {
        $user = User::factory()->create(['is_blocked' => true]);

        // Use a route with 'blocked' middleware
        $response = $this->actingAs($user)->get('/cart');

        $response->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_blocked_user_sees_error_message(): void
    {
        $user = User::factory()->create(['is_blocked' => true]);

        // Use a route with 'blocked' middleware
        $response = $this->actingAs($user)->get('/cart');

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors(['email']);
    }

    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        // Use a route with 'auth' middleware
        $response = $this->get('/cart');

        $response->assertRedirect('/login');
    }
}
