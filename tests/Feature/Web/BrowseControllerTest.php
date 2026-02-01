<?php

namespace Tests\Feature\Web;

use App\Models\Level;
use App\Models\Module;
use App\Models\Note;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BrowseControllerTest extends TestCase
{
    use RefreshDatabase;

    private function createFullStructure(): array
    {
        $user = User::factory()->create();
        $school = School::create(['name' => 'Test School', 'description' => 'Test']);
        $level = Level::create(['school_id' => $school->id, 'name' => 'Level 1']);
        $module = Module::create(['level_id' => $level->id, 'title' => 'Module 1', 'status' => true]);

        return compact('user', 'school', 'level', 'module');
    }

    public function test_can_access_materials_browse_page(): void
    {
        $response = $this->get('/materials');

        $response->assertStatus(200);
    }

    public function test_can_view_approved_material(): void
    {
        $structure = $this->createFullStructure();
        $note = Note::create([
            'user_id' => $structure['user']->id,
            'module_id' => $structure['module']->id,
            'title' => 'Test Material',
            'description' => 'Test description',
            'price' => 100.00,
            'status' => 'approved',
        ]);

        $response = $this->get("/materials/{$note->id}");

        $response->assertStatus(200);
        $response->assertSee('Test Material');
    }

    public function test_cannot_view_pending_material(): void
    {
        $structure = $this->createFullStructure();
        $note = Note::create([
            'user_id' => $structure['user']->id,
            'module_id' => $structure['module']->id,
            'title' => 'Pending Material',
            'description' => 'Test description',
            'price' => 100.00,
            'status' => 'pending',
        ]);

        $response = $this->get("/materials/{$note->id}");

        $response->assertStatus(404);
    }

    public function test_cannot_view_disabled_material(): void
    {
        $structure = $this->createFullStructure();
        $note = Note::create([
            'user_id' => $structure['user']->id,
            'module_id' => $structure['module']->id,
            'title' => 'Disabled Material',
            'description' => 'Test description',
            'price' => 100.00,
            'status' => 'disabled',
        ]);

        $response = $this->get("/materials/{$note->id}");

        $response->assertStatus(404);
    }

    public function test_returns_404_for_nonexistent_material(): void
    {
        $response = $this->get('/materials/99999');

        $response->assertStatus(404);
    }
}
