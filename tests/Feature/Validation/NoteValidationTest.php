<?php

namespace Tests\Feature\Validation;

use App\Models\Level;
use App\Models\Module;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class NoteValidationTest extends TestCase
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

    public function test_note_requires_module_id(): void
    {
        Storage::fake('public');
        Sanctum::actingAs($this->user, ['*']);

        $response = $this->postJson('/api/notes', [
            'title' => 'Test Note',
            'description' => 'Test description',
            'price' => 100.00,
            'note_file' => UploadedFile::fake()->create('document.pdf', 1000, 'application/pdf'),
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['module_id']);
    }

    public function test_note_requires_title(): void
    {
        Storage::fake('public');
        Sanctum::actingAs($this->user, ['*']);

        $response = $this->postJson('/api/notes', [
            'module_id' => $this->module->id,
            'description' => 'Test description',
            'price' => 100.00,
            'note_file' => UploadedFile::fake()->create('document.pdf', 1000, 'application/pdf'),
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title']);
    }

    public function test_note_requires_description(): void
    {
        Storage::fake('public');
        Sanctum::actingAs($this->user, ['*']);

        $response = $this->postJson('/api/notes', [
            'module_id' => $this->module->id,
            'title' => 'Test Note',
            'price' => 100.00,
            'note_file' => UploadedFile::fake()->create('document.pdf', 1000, 'application/pdf'),
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['description']);
    }

    public function test_note_requires_price(): void
    {
        Storage::fake('public');
        Sanctum::actingAs($this->user, ['*']);

        $response = $this->postJson('/api/notes', [
            'module_id' => $this->module->id,
            'title' => 'Test Note',
            'description' => 'Test description',
            'note_file' => UploadedFile::fake()->create('document.pdf', 1000, 'application/pdf'),
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['price']);
    }

    public function test_note_price_must_be_positive(): void
    {
        Storage::fake('public');
        Sanctum::actingAs($this->user, ['*']);

        $response = $this->postJson('/api/notes', [
            'module_id' => $this->module->id,
            'title' => 'Test Note',
            'description' => 'Test description',
            'price' => -50.00,
            'note_file' => UploadedFile::fake()->create('document.pdf', 1000, 'application/pdf'),
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['price']);
    }

    public function test_note_requires_file(): void
    {
        Storage::fake('public');
        Sanctum::actingAs($this->user, ['*']);

        $response = $this->postJson('/api/notes', [
            'module_id' => $this->module->id,
            'title' => 'Test Note',
            'description' => 'Test description',
            'price' => 100.00,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['note_file']);
    }

    public function test_note_module_must_exist(): void
    {
        Storage::fake('public');
        Sanctum::actingAs($this->user, ['*']);

        $response = $this->postJson('/api/notes', [
            'module_id' => 99999,
            'title' => 'Test Note',
            'description' => 'Test description',
            'price' => 100.00,
            'note_file' => UploadedFile::fake()->create('document.pdf', 1000, 'application/pdf'),
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['module_id']);
    }

    public function test_valid_note_creation(): void
    {
        Storage::fake('public');
        Sanctum::actingAs($this->user, ['*']);

        $response = $this->postJson('/api/notes', [
            'module_id' => $this->module->id,
            'title' => 'Valid Note',
            'description' => 'This is a valid note description',
            'price' => 150.00,
            'note_file' => UploadedFile::fake()->create('document.pdf', 1000, 'application/pdf'),
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.title', 'Valid Note')
            ->assertJsonPath('data.status', 'pending');
    }
}
