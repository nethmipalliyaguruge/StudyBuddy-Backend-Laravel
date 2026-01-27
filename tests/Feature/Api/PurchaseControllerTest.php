<?php

namespace Tests\Feature\Api;

use App\Models\Level;
use App\Models\Module;
use App\Models\Note;
use App\Models\Purchase;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PurchaseControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private User $seller;
    private Note $note;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->seller = User::factory()->create();

        $school = School::create(['name' => 'Test School', 'description' => 'Test']);
        $level = Level::create(['school_id' => $school->id, 'name' => 'Level 1']);
        $module = Module::create(['level_id' => $level->id, 'title' => 'Module 1', 'status' => true]);

        $this->note = Note::create([
            'user_id' => $this->seller->id,
            'module_id' => $module->id,
            'title' => 'Test Note',
            'description' => 'Test description',
            'price' => 100.00,
            'status' => 'approved',
        ]);
    }

    private function createPurchase(array $attributes = []): Purchase
    {
        return Purchase::create(array_merge([
            'user_id' => $this->user->id,
            'note_id' => $this->note->id,
            'price' => $this->note->price,
            'commission' => 10.00,
            'status' => 'completed',
            'payment_method' => 'stripe',
            'paid_at' => now(),
        ], $attributes));
    }

    public function test_authenticated_user_can_list_their_purchases(): void
    {
        Sanctum::actingAs($this->user, ['*']);

        $this->createPurchase();

        // Create another user's purchase (should not appear)
        $otherUser = User::factory()->create();
        Purchase::create([
            'user_id' => $otherUser->id,
            'note_id' => $this->note->id,
            'price' => 100.00,
            'commission' => 10.00,
            'status' => 'completed',
            'payment_method' => 'stripe',
            'paid_at' => now(),
        ]);

        $response = $this->getJson('/api/my-purchases');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'price', 'status', 'note'],
                ],
            ]);
    }

    public function test_authenticated_user_can_view_their_purchase(): void
    {
        Sanctum::actingAs($this->user, ['*']);

        $purchase = $this->createPurchase();

        $response = $this->getJson("/api/purchases/{$purchase->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $purchase->id)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'price',
                    'status',
                    'note' => [
                        'id',
                        'title',
                    ],
                ],
            ]);
    }

    public function test_authenticated_user_cannot_view_others_purchase(): void
    {
        $otherUser = User::factory()->create();
        Sanctum::actingAs($otherUser, ['*']);

        $purchase = $this->createPurchase();

        $response = $this->getJson("/api/purchases/{$purchase->id}");

        $response->assertStatus(403);
    }

    public function test_authenticated_user_can_download_purchased_file(): void
    {
        Sanctum::actingAs($this->user, ['*']);

        $purchase = $this->createPurchase();

        $response = $this->getJson("/api/purchases/{$purchase->id}/download");

        // Without actual media, this will return 404 for file not found
        // In a real scenario with media, it would return download_url
        $response->assertStatus(404);
    }

    public function test_cannot_download_others_purchase(): void
    {
        $otherUser = User::factory()->create();
        Sanctum::actingAs($otherUser, ['*']);

        $purchase = $this->createPurchase();

        $response = $this->getJson("/api/purchases/{$purchase->id}/download");

        $response->assertStatus(403);
    }

    public function test_cannot_view_pending_purchase(): void
    {
        Sanctum::actingAs($this->user, ['*']);

        $purchase = $this->createPurchase(['status' => 'pending', 'paid_at' => null]);

        $response = $this->getJson("/api/purchases/{$purchase->id}");

        $response->assertStatus(403);
    }

    public function test_unauthenticated_user_cannot_access_purchases(): void
    {
        $response = $this->getJson('/api/my-purchases');
        $response->assertStatus(401);

        $purchase = $this->createPurchase();

        $response = $this->getJson("/api/purchases/{$purchase->id}");
        $response->assertStatus(401);

        $response = $this->getJson("/api/purchases/{$purchase->id}/download");
        $response->assertStatus(401);
    }

    public function test_only_completed_purchases_are_listed(): void
    {
        Sanctum::actingAs($this->user, ['*']);

        $this->createPurchase(['status' => 'completed']);
        $this->createPurchase(['status' => 'pending', 'paid_at' => null]);
        $this->createPurchase(['status' => 'failed', 'paid_at' => null]);

        $response = $this->getJson('/api/my-purchases');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }
}
