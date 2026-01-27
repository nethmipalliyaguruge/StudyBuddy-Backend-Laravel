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

class CartControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private User $seller;
    private Module $module;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->seller = User::factory()->create();

        $school = School::create(['name' => 'Test School', 'description' => 'Test']);
        $level = Level::create(['school_id' => $school->id, 'name' => 'Level 1']);
        $this->module = Module::create(['level_id' => $level->id, 'title' => 'Module 1', 'status' => true]);
    }

    private function createNote(array $attributes = []): Note
    {
        return Note::create(array_merge([
            'user_id' => $this->seller->id,
            'module_id' => $this->module->id,
            'title' => 'Test Note',
            'description' => 'Test description',
            'price' => 100.00,
            'status' => 'approved',
        ], $attributes));
    }

    public function test_authenticated_user_can_get_cart_items(): void
    {
        Sanctum::actingAs($this->user, ['*']);

        $note1 = $this->createNote(['title' => 'Note 1', 'price' => 100.00]);
        $note2 = $this->createNote(['title' => 'Note 2', 'price' => 150.00]);

        $response = $this->postJson('/api/cart', [
            'cart_ids' => [$note1->id, $note2->id],
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('count', 2)
            ->assertJsonPath('total', '250.00')
            ->assertJsonStructure([
                'items' => [
                    '*' => ['id', 'title', 'price'],
                ],
                'total',
                'count',
            ]);
    }

    public function test_authenticated_user_can_validate_cart_item(): void
    {
        Sanctum::actingAs($this->user, ['*']);

        $note = $this->createNote();

        $response = $this->getJson("/api/cart/validate/{$note->id}");

        $response->assertStatus(200)
            ->assertJson(['valid' => true])
            ->assertJsonStructure([
                'valid',
                'note' => ['id', 'title', 'price'],
            ]);
    }

    public function test_cannot_add_own_note_to_cart(): void
    {
        Sanctum::actingAs($this->user, ['*']);

        // Create a note owned by the current user
        $note = Note::create([
            'user_id' => $this->user->id,
            'module_id' => $this->module->id,
            'title' => 'My Own Note',
            'description' => 'Test',
            'price' => 100.00,
            'status' => 'approved',
        ]);

        $response = $this->getJson("/api/cart/validate/{$note->id}");

        $response->assertStatus(422)
            ->assertJson(['valid' => false])
            ->assertJsonPath('errors.0', 'You cannot purchase your own note.');
    }

    public function test_cannot_add_already_purchased_note_to_cart(): void
    {
        Sanctum::actingAs($this->user, ['*']);

        $note = $this->createNote();

        // Create a completed purchase
        Purchase::create([
            'user_id' => $this->user->id,
            'note_id' => $note->id,
            'price' => $note->price,
            'commission' => 10.00,
            'status' => 'completed',
            'payment_method' => 'stripe',
            'paid_at' => now(),
        ]);

        $response = $this->getJson("/api/cart/validate/{$note->id}");

        $response->assertStatus(422)
            ->assertJson(['valid' => false])
            ->assertJsonPath('errors.0', 'You have already purchased this material.');
    }

    public function test_cannot_add_non_approved_note_to_cart(): void
    {
        Sanctum::actingAs($this->user, ['*']);

        $note = $this->createNote(['status' => 'pending']);

        $response = $this->getJson("/api/cart/validate/{$note->id}");

        $response->assertStatus(422)
            ->assertJson(['valid' => false])
            ->assertJsonPath('errors.0', 'This material is not available for purchase.');
    }

    public function test_cart_excludes_own_notes_and_purchased_items(): void
    {
        Sanctum::actingAs($this->user, ['*']);

        $note1 = $this->createNote(['title' => 'Available Note', 'price' => 100.00]);
        $ownNote = Note::create([
            'user_id' => $this->user->id,
            'module_id' => $this->module->id,
            'title' => 'Own Note',
            'description' => 'Test',
            'price' => 50.00,
            'status' => 'approved',
        ]);
        $purchasedNote = $this->createNote(['title' => 'Purchased Note', 'price' => 75.00]);

        // Mark one as purchased
        Purchase::create([
            'user_id' => $this->user->id,
            'note_id' => $purchasedNote->id,
            'price' => $purchasedNote->price,
            'commission' => 7.50,
            'status' => 'completed',
            'payment_method' => 'stripe',
            'paid_at' => now(),
        ]);

        $response = $this->postJson('/api/cart', [
            'cart_ids' => [$note1->id, $ownNote->id, $purchasedNote->id],
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('count', 1)
            ->assertJsonPath('total', '100.00');
    }

    public function test_unauthenticated_user_cannot_access_cart(): void
    {
        $note = $this->createNote();

        $response = $this->postJson('/api/cart', ['cart_ids' => [$note->id]]);
        $response->assertStatus(401);

        $response = $this->getJson("/api/cart/validate/{$note->id}");
        $response->assertStatus(401);

        $response = $this->postJson('/api/checkout', ['cart_ids' => [$note->id]]);
        $response->assertStatus(401);
    }

    public function test_checkout_requires_valid_cart_items(): void
    {
        Sanctum::actingAs($this->user, ['*']);

        $response = $this->postJson('/api/checkout', [
            'cart_ids' => [],
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['cart_ids']);
    }

    public function test_checkout_fails_with_no_valid_items(): void
    {
        Sanctum::actingAs($this->user, ['*']);

        // Create a note owned by the current user (not valid for purchase)
        $ownNote = Note::create([
            'user_id' => $this->user->id,
            'module_id' => $this->module->id,
            'title' => 'Own Note',
            'description' => 'Test',
            'price' => 50.00,
            'status' => 'approved',
        ]);

        $response = $this->postJson('/api/checkout', [
            'cart_ids' => [$ownNote->id],
        ]);

        $response->assertStatus(422)
            ->assertJson(['error' => 'No valid items in cart.']);
    }

    public function test_checkout_fails_when_all_items_already_purchased(): void
    {
        Sanctum::actingAs($this->user, ['*']);

        $note = $this->createNote();

        // Create a completed purchase
        Purchase::create([
            'user_id' => $this->user->id,
            'note_id' => $note->id,
            'price' => $note->price,
            'commission' => 10.00,
            'status' => 'completed',
            'payment_method' => 'stripe',
            'paid_at' => now(),
        ]);

        $response = $this->postJson('/api/checkout', [
            'cart_ids' => [$note->id],
        ]);

        $response->assertStatus(422)
            ->assertJson(['error' => 'All items have already been purchased.']);
    }
}
