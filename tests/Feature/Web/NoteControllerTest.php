<?php

namespace Tests\Feature\Web;

use App\Models\Level;
use App\Models\Module;
use App\Models\Note;
use App\Models\Purchase;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class NoteControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Module $module;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['is_blocked' => false]);
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

    public function test_authenticated_user_can_view_my_notes(): void
    {
        $response = $this->actingAs($this->user)->get('/my-notes');

        $response->assertStatus(200);
    }

    public function test_authenticated_user_can_view_upload_form(): void
    {
        $response = $this->actingAs($this->user)->get('/upload-note');

        $response->assertStatus(200);
    }

    public function test_unauthenticated_user_is_redirected_from_my_notes(): void
    {
        $response = $this->get('/my-notes');

        $response->assertRedirect('/login');
    }

    public function test_blocked_user_cannot_access_my_notes(): void
    {
        $blockedUser = User::factory()->create(['is_blocked' => true]);

        $response = $this->actingAs($blockedUser)->get('/my-notes');

        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_create_note(): void
    {
        if (!function_exists('imagecreatetruecolor')) {
            $this->markTestSkipped('GD extension is not installed.');
        }

        Storage::fake('public');

        $response = $this->actingAs($this->user)->post('/notes', [
            'module_id' => $this->module->id,
            'title' => 'New Note',
            'description' => 'This is a new note',
            'price' => 150.00,
            'note_file' => UploadedFile::fake()->create('document.pdf', 1000, 'application/pdf'),
            'previews' => [
                UploadedFile::fake()->image('preview1.jpg', 800, 600),
            ],
        ]);

        $response->assertRedirect();

        // Web controller sets status to 'approved' unlike API which sets 'pending'
        $this->assertDatabaseHas('notes', [
            'user_id' => $this->user->id,
            'title' => 'New Note',
            'status' => 'approved',
        ]);
    }

    public function test_authenticated_user_can_update_own_note(): void
    {
        $note = $this->createNote();

        $response = $this->actingAs($this->user)->put("/notes/{$note->id}", [
            'title' => 'Updated Title',
            'description' => 'Updated description',
            'price' => 200.00,
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('notes', [
            'id' => $note->id,
            'title' => 'Updated Title',
        ]);
    }

    public function test_authenticated_user_can_delete_own_note(): void
    {
        $note = $this->createNote();

        $response = $this->actingAs($this->user)->delete("/notes/{$note->id}");

        $response->assertRedirect();

        $this->assertDatabaseMissing('notes', ['id' => $note->id]);
    }

    public function test_note_with_purchases_is_disabled_not_deleted(): void
    {
        $note = $this->createNote(['status' => 'approved']);
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

        $response = $this->actingAs($this->user)->delete("/notes/{$note->id}");

        $response->assertRedirect();

        // Note should be disabled, not deleted
        $this->assertDatabaseHas('notes', [
            'id' => $note->id,
            'status' => 'disabled',
        ]);
    }

    public function test_user_cannot_update_others_note(): void
    {
        $otherUser = User::factory()->create(['is_blocked' => false]);
        $note = $this->createNote();

        $response = $this->actingAs($otherUser)->put("/notes/{$note->id}", [
            'title' => 'Hacked Title',
            'description' => 'Hacked description',
            'price' => 999.00,
        ]);

        $response->assertStatus(403);
    }

    public function test_user_cannot_delete_others_note(): void
    {
        $otherUser = User::factory()->create();
        $note = $this->createNote();

        $response = $this->actingAs($otherUser)->delete("/notes/{$note->id}");

        $response->assertStatus(403);
    }
}
