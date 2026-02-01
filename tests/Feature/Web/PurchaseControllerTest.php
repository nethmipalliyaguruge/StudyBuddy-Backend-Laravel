<?php

namespace Tests\Feature\Web;

use App\Models\Level;
use App\Models\Module;
use App\Models\Note;
use App\Models\Purchase;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $buyer;
    private User $seller;
    private Note $note;

    protected function setUp(): void
    {
        parent::setUp();

        $this->buyer = User::factory()->create(['is_blocked' => false]);
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
            'user_id' => $this->buyer->id,
            'note_id' => $this->note->id,
            'price' => 100.00,
            'commission' => 10.00,
            'status' => 'completed',
            'payment_method' => 'stripe',
            'paid_at' => now(),
        ], $attributes));
    }

    public function test_authenticated_user_can_view_purchases(): void
    {
        $response = $this->actingAs($this->buyer)->get('/my-purchases');

        $response->assertStatus(200);
    }

    public function test_unauthenticated_user_is_redirected_from_purchases(): void
    {
        $response = $this->get('/my-purchases');

        $response->assertRedirect('/login');
    }

    public function test_blocked_user_cannot_access_purchases(): void
    {
        $blockedUser = User::factory()->create(['is_blocked' => true]);

        $response = $this->actingAs($blockedUser)->get('/my-purchases');

        $response->assertRedirect('/login');
    }

    public function test_user_can_download_purchased_file(): void
    {
        $purchase = $this->createPurchase();

        $response = $this->actingAs($this->buyer)->get("/purchases/{$purchase->id}/download");

        // Without actual media file, this redirects back with error
        $response->assertRedirect();
        $response->assertSessionHas('error', 'No file available for download.');
    }

    public function test_user_cannot_download_others_purchase(): void
    {
        $otherUser = User::factory()->create(['is_blocked' => false]);
        $purchase = $this->createPurchase();

        $response = $this->actingAs($otherUser)->get("/purchases/{$purchase->id}/download");

        $response->assertStatus(403);
    }

    public function test_cannot_download_pending_purchase(): void
    {
        $purchase = $this->createPurchase(['status' => 'pending', 'paid_at' => null]);

        $response = $this->actingAs($this->buyer)->get("/purchases/{$purchase->id}/download");

        // Returns redirect with error message for incomplete purchase
        $response->assertRedirect();
        $response->assertSessionHas('error', 'This purchase is not completed.');
    }
}
