<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_with_valid_data(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '1234567890',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'user' => ['id', 'name', 'email'],
                'token',
                'token_type',
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'name' => 'Test User',
            'role' => 'student',
        ]);
    }

    public function test_registration_fails_with_invalid_data(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => '',
            'email' => 'invalid-email',
            'password' => '123', // Too short
            'password_confirmation' => '456', // Doesn't match
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    public function test_registration_fails_with_duplicate_email(): void
    {
        User::factory()->create(['email' => 'existing@example.com']);

        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'existing@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'role' => 'student',
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'user' => ['id', 'name', 'email'],
                'token',
                'token_type',
            ]);
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        User::factory()->create(['email' => 'test@example.com']);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_blocked_user_cannot_login(): void
    {
        User::factory()->create([
            'email' => 'blocked@example.com',
            'is_blocked' => true,
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'blocked@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_authenticated_user_can_logout(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);

        $response = $this->postJson('/api/logout');

        $response->assertStatus(200)
            ->assertJson(['message' => 'Logged out successfully']);
    }

    public function test_authenticated_user_can_logout_all_devices(): void
    {
        $user = User::factory()->create();
        $user->createToken('token-1');
        $user->createToken('token-2');
        Sanctum::actingAs($user, ['*']);

        $response = $this->postJson('/api/logout-all');

        $response->assertStatus(200)
            ->assertJson(['message' => 'Logged out from all devices successfully']);

        $this->assertCount(0, $user->fresh()->tokens);
    }

    public function test_authenticated_user_can_get_their_profile(): void
    {
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);
        Sanctum::actingAs($user, ['*']);

        $response = $this->getJson('/api/user');

        $response->assertStatus(200)
            ->assertJsonPath('user.name', 'John Doe')
            ->assertJsonPath('user.email', 'john@example.com');
    }

    public function test_unauthenticated_user_cannot_access_protected_routes(): void
    {
        $response = $this->getJson('/api/user');

        $response->assertStatus(401);
    }
}
