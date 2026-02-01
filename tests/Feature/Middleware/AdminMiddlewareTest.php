<?php

namespace Tests\Feature\Middleware;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_admin_routes(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertStatus(200);
    }

    public function test_student_cannot_access_admin_routes(): void
    {
        $student = User::factory()->create(['role' => 'student']);

        $response = $this->actingAs($student)->get('/admin/dashboard');

        $response->assertStatus(403);
    }

    public function test_unauthenticated_user_is_redirected_from_admin_routes(): void
    {
        $response = $this->get('/admin/dashboard');

        $response->assertRedirect('/login');
    }

    public function test_blocked_admin_cannot_access_admin_routes(): void
    {
        $blockedAdmin = User::factory()->create([
            'role' => 'admin',
            'is_blocked' => true,
        ]);

        $response = $this->actingAs($blockedAdmin)->get('/admin/dashboard');

        $response->assertRedirect('/login');
    }
}
