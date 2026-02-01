<?php

namespace Tests\Feature\Integration;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_complete_registration_and_login_flow(): void
    {
        // Step 1: Register a new user
        $response = $this->postJson('/api/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '1234567890',
            'password' => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'user' => ['id', 'name', 'email'],
                'token',
                'token_type',
            ]);

        $token = $response->json('token');

        // Step 2: Access protected route with token
        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/user');

        $response->assertStatus(200)
            ->assertJsonPath('user.name', 'John Doe')
            ->assertJsonPath('user.email', 'john@example.com');

        // Step 3: Logout
        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/logout');

        $response->assertStatus(200)
            ->assertJson(['message' => 'Logged out successfully']);

        // Step 4: Login again
        $response = $this->postJson('/api/login', [
            'email' => 'john@example.com',
            'password' => 'SecurePass123!',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['message', 'user', 'token', 'token_type']);
    }

    public function test_blocked_user_flow(): void
    {
        // Create and block a user
        $user = User::factory()->create([
            'email' => 'blocked@example.com',
            'is_blocked' => true,
        ]);

        // Attempt to login
        $response = $this->postJson('/api/login', [
            'email' => 'blocked@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_invalid_credentials_flow(): void
    {
        User::factory()->create([
            'email' => 'user@example.com',
        ]);

        // Wrong password
        $response = $this->postJson('/api/login', [
            'email' => 'user@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);

        // Non-existent email
        $response = $this->postJson('/api/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_registration_validation(): void
    {
        // Missing required fields
        $response = $this->postJson('/api/register', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password']);

        // Invalid email format
        $response = $this->postJson('/api/register', [
            'name' => 'Test',
            'email' => 'invalid-email',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);

        // Password mismatch
        $response = $this->postJson('/api/register', [
            'name' => 'Test',
            'email' => 'test@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'DifferentPassword!',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_logout_all_devices(): void
    {
        $user = User::factory()->create();

        // Create multiple tokens (simulating multiple devices)
        $token1 = $user->createToken('device-1')->plainTextToken;
        $user->createToken('device-2');
        $user->createToken('device-3');

        $this->assertCount(3, $user->tokens);

        // Logout from all devices
        $response = $this->withHeader('Authorization', "Bearer {$token1}")
            ->postJson('/api/logout-all');

        $response->assertStatus(200)
            ->assertJson(['message' => 'Logged out from all devices successfully']);

        // Verify all tokens are revoked
        $this->assertCount(0, $user->fresh()->tokens);
    }
}
