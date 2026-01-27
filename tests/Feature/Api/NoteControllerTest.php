<?php

namespace Tests\Feature\Api;

use App\Models\Level;
use App\Models\Module;
use App\Models\Note;
use App\Models\Purchase;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class NoteControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Module $module;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $school = School::create(['name' => 'Test School', 'description' => 'Test']);
        $level = Level::create(['school_id' => $school->id, 'name' => 'Level 1']);
        $this->module = Module::create(['level_id' => $level->id, 'title' => 'Module 1', 'status' => true]);
    }

    private function createNote(array $attributes = []): Note
    {
        return Note::create(array_merge([
            'user_id' => $this->user->id,
            'module_id' => $this->module->id,
            'title' => 'Test Note',
            'description' => 'Test description',
            'price' => 100.00,
            'status' => 'pending',
        ], $attributes));
    }

    public function test_authenticated_user_can_list_their_notes(): void
    {
        Sanctum::actingAs($this->user, ['*']);

        $this->createNote(['title' => 'Note 1']);
        $this->createNote(['title' => 'Note 2']);

        // Create another user's note (should not appear)
        $otherUser = User::factory()->create();
        Note::create([
            'user_id' => $otherUser->id,
            'module_id' => $this->module->id,
            'title' => 'Other User Note',
            'description' => 'Test',
            'price' => 50.00,
            'status' => 'approved',
        ]);

        $response = $this->getJson('/api/my-notes');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    public function test_authenticated_user_can_create_note(): void
    {
        Storage::fake('public');
        Sanctum::actingAs($this->user, ['*']);

        $response = $this->postJson('/api/notes', [
            'module_id' => $this->module->id,
            'title' => 'New Note',
            'description' => 'This is a new note',
            'price' => 150.00,
            'note_file' => UploadedFile::fake()->create('document.pdf', 1000, 'application/pdf'),
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.title', 'New Note')
            ->assertJsonPath('data.status', 'pending');

        $this->assertDatabaseHas('notes', [
            'user_id' => $this->user->id,
            'title' => 'New Note',
            'status' => 'pending',
        ]);
    }

    public function test_authenticated_user_can_update_their_note(): void
    {
        Sanctum::actingAs($this->user, ['*']);

        $note = $this->createNote(['title' => 'Original Title']);

        $response = $this->putJson("/api/notes/{$note->id}", [
            'title' => 'Updated Title',
            'description' => 'Updated description',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.title', 'Updated Title');

        $this->assertDatabaseHas('notes', [
            'id' => $note->id,
            'title' => 'Updated Title',
        ]);
    }

    public function test_authenticated_user_cannot_update_others_note(): void
    {
        $otherUser = User::factory()->create();
        Sanctum::actingAs($otherUser, ['*']);

        $note = $this->createNote(['title' => 'Original Title']);

        $response = $this->putJson("/api/notes/{$note->id}", [
            'title' => 'Hacked Title',
        ]);

        $response->assertStatus(403);
    }

    public function test_authenticated_user_can_delete_their_note(): void
    {
        Sanctum::actingAs($this->user, ['*']);

        $note = $this->createNote();

        $response = $this->deleteJson("/api/notes/{$note->id}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Note deleted successfully.']);

        $this->assertDatabaseMissing('notes', ['id' => $note->id]);
    }

    public function test_authenticated_user_cannot_delete_others_note(): void
    {
        $otherUser = User::factory()->create();
        Sanctum::actingAs($otherUser, ['*']);

        $note = $this->createNote();

        $response = $this->deleteJson("/api/notes/{$note->id}");

        $response->assertStatus(403);
    }

    public function test_cannot_delete_note_with_purchases(): void
    {
        Sanctum::actingAs($this->user, ['*']);

        $note = $this->createNote(['status' => 'approved']);

        // Create a purchase for this note
        $buyer = User::factory()->create();
        Purchase::create([
            'user_id' => $buyer->id,
            'note_id' => $note->id,
            'price' => $note->price,
            'commission' => 10.00,
            'status' => 'completed',
            'payment_method' => 'stripe',
            'paid_at' => now(),
        ]);

        $response = $this->deleteJson("/api/notes/{$note->id}");

        $response->assertStatus(403);
    }

    public function test_cannot_update_note_with_purchases(): void
    {
        Sanctum::actingAs($this->user, ['*']);

        $note = $this->createNote(['status' => 'approved']);

        // Create a purchase for this note
        $buyer = User::factory()->create();
        Purchase::create([
            'user_id' => $buyer->id,
            'note_id' => $note->id,
            'price' => $note->price,
            'commission' => 10.00,
            'status' => 'completed',
            'payment_method' => 'stripe',
            'paid_at' => now(),
        ]);

        $response = $this->putJson("/api/notes/{$note->id}", [
            'title' => 'Updated Title',
        ]);

        $response->assertStatus(403);
    }

    public function test_unauthenticated_user_cannot_access_notes(): void
    {
        $response = $this->getJson('/api/my-notes');
        $response->assertStatus(401);

        $response = $this->postJson('/api/notes', []);
        $response->assertStatus(401);

        $note = $this->createNote();

        $response = $this->putJson("/api/notes/{$note->id}", []);
        $response->assertStatus(401);

        $response = $this->deleteJson("/api/notes/{$note->id}");
        $response->assertStatus(401);
    }
}
