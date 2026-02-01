<?php

namespace Tests\Feature\Integration;

use App\Models\Level;
use App\Models\Module;
use App\Models\Note;
use App\Models\Purchase;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PurchaseFlowTest extends TestCase
{
    use RefreshDatabase;

    private User $buyer;
    private User $seller;
    private Module $module;

    protected function setUp(): void
    {
        parent::setUp();

        $this->buyer = User::factory()->create(['role' => 'student']);
        $this->seller = User::factory()->create(['role' => 'student']);

        $school = School::create(['name' => 'Test School', 'description' => 'Test']);
        $level = Level::create(['school_id' => $school->id, 'name' => 'Level 1']);
        $this->module = Module::create(['level_id' => $level->id, 'title' => 'Module 1', 'status' => true]);
    }

    public function test_complete_purchase_flow(): void
    {
        // Step 1: Seller creates a note
        $note = Note::create([
            'user_id' => $this->seller->id,
            'module_id' => $this->module->id,
            'title' => 'Math Notes',
            'description' => 'Comprehensive math notes',
            'price' => 150.00,
            'status' => 'pending', // New notes start as pending
        ]);

        $this->assertEquals('pending', $note->status);
        $this->assertFalse($note->isApproved());

        // Step 2: Note gets approved (simulating admin action)
        $note->update(['status' => 'approved']);
        $note->refresh();

        $this->assertTrue($note->isApproved());

        // Step 3: Buyer browses and finds the note
        Sanctum::actingAs($this->buyer, ['*']);

        $response = $this->getJson('/api/materials');
        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');

        // Step 4: Buyer validates the note can be added to cart
        $response = $this->getJson("/api/cart/validate/{$note->id}");
        $response->assertStatus(200)
            ->assertJson(['valid' => true]);

        // Step 5: Simulate completed purchase
        $purchase = Purchase::create([
            'user_id' => $this->buyer->id,
            'note_id' => $note->id,
            'price' => $note->price,
            'commission' => $note->price * 0.10, // 10% commission
            'status' => 'completed',
            'payment_method' => 'stripe',
            'paid_at' => now(),
        ]);

        $this->assertEquals('completed', $purchase->status);
        $this->assertEquals('15.00', $purchase->commission);

        // Step 6: Buyer can view their purchases
        $response = $this->getJson('/api/my-purchases');
        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');

        // Step 7: Buyer cannot purchase the same note again
        $response = $this->getJson("/api/cart/validate/{$note->id}");
        $response->assertStatus(422)
            ->assertJson(['valid' => false]);
    }

    public function test_seller_cannot_buy_own_note(): void
    {
        $note = Note::create([
            'user_id' => $this->seller->id,
            'module_id' => $this->module->id,
            'title' => 'My Note',
            'description' => 'Test',
            'price' => 100.00,
            'status' => 'approved',
        ]);

        Sanctum::actingAs($this->seller, ['*']);

        $response = $this->getJson("/api/cart/validate/{$note->id}");
        $response->assertStatus(422)
            ->assertJson(['valid' => false]);
    }

    public function test_pending_note_not_visible_in_marketplace(): void
    {
        Note::create([
            'user_id' => $this->seller->id,
            'module_id' => $this->module->id,
            'title' => 'Pending Note',
            'description' => 'Test',
            'price' => 100.00,
            'status' => 'pending',
        ]);

        $response = $this->getJson('/api/materials');
        $response->assertStatus(200)
            ->assertJsonCount(0, 'data');
    }

    public function test_note_with_purchases_cannot_be_deleted(): void
    {
        $note = Note::create([
            'user_id' => $this->seller->id,
            'module_id' => $this->module->id,
            'title' => 'Popular Note',
            'description' => 'Test',
            'price' => 100.00,
            'status' => 'approved',
        ]);

        // Create a purchase
        Purchase::create([
            'user_id' => $this->buyer->id,
            'note_id' => $note->id,
            'price' => $note->price,
            'commission' => 10.00,
            'status' => 'completed',
            'payment_method' => 'stripe',
            'paid_at' => now(),
        ]);

        Sanctum::actingAs($this->seller, ['*']);

        $response = $this->deleteJson("/api/notes/{$note->id}");
        $response->assertStatus(403);

        $this->assertDatabaseHas('notes', ['id' => $note->id]);
    }

    public function test_commission_calculation(): void
    {
        $prices = [100.00, 250.50, 999.99, 50.00];
        $expectedCommissions = [10.00, 25.05, 100.00, 5.00]; // 10% each

        foreach ($prices as $index => $price) {
            $note = Note::create([
                'user_id' => $this->seller->id,
                'module_id' => $this->module->id,
                'title' => "Note {$index}",
                'description' => 'Test',
                'price' => $price,
                'status' => 'approved',
            ]);

            $commission = $price * 0.10;

            $purchase = Purchase::create([
                'user_id' => $this->buyer->id,
                'note_id' => $note->id,
                'price' => $price,
                'commission' => $commission,
                'status' => 'completed',
                'payment_method' => 'stripe',
                'paid_at' => now(),
            ]);

            $this->assertEquals(number_format($expectedCommissions[$index], 2), $purchase->commission);
        }
    }
}
