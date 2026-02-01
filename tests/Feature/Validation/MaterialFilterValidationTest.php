<?php

namespace Tests\Feature\Validation;

use App\Models\Level;
use App\Models\Module;
use App\Models\Note;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MaterialFilterValidationTest extends TestCase
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

    private function createApprovedNote(array $structure, array $attributes = []): Note
    {
        return Note::create(array_merge([
            'user_id' => $structure['user']->id,
            'module_id' => $structure['module']->id,
            'title' => 'Test Note',
            'description' => 'Test description',
            'price' => 100.00,
            'status' => 'approved',
        ], $attributes));
    }

    public function test_materials_with_invalid_school_id_returns_empty(): void
    {
        $structure = $this->createFullStructure();
        $this->createApprovedNote($structure);

        $response = $this->getJson('/api/materials?school_id=99999');

        $response->assertStatus(200)
            ->assertJsonCount(0, 'data');
    }

    public function test_materials_with_invalid_level_id_returns_empty(): void
    {
        $structure = $this->createFullStructure();
        $this->createApprovedNote($structure);

        $response = $this->getJson('/api/materials?level_id=99999');

        $response->assertStatus(200)
            ->assertJsonCount(0, 'data');
    }

    public function test_materials_with_invalid_module_id_returns_empty(): void
    {
        $structure = $this->createFullStructure();
        $this->createApprovedNote($structure);

        $response = $this->getJson('/api/materials?module_id=99999');

        $response->assertStatus(200)
            ->assertJsonCount(0, 'data');
    }

    public function test_materials_min_price_filter(): void
    {
        $structure = $this->createFullStructure();
        $this->createApprovedNote($structure, ['title' => 'Cheap', 'price' => 50.00]);
        $this->createApprovedNote($structure, ['title' => 'Medium', 'price' => 150.00]);
        $this->createApprovedNote($structure, ['title' => 'Expensive', 'price' => 300.00]);

        $response = $this->getJson('/api/materials?min_price=100');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    public function test_materials_max_price_filter(): void
    {
        $structure = $this->createFullStructure();
        $this->createApprovedNote($structure, ['title' => 'Cheap', 'price' => 50.00]);
        $this->createApprovedNote($structure, ['title' => 'Medium', 'price' => 150.00]);
        $this->createApprovedNote($structure, ['title' => 'Expensive', 'price' => 300.00]);

        $response = $this->getJson('/api/materials?max_price=100');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function test_materials_combined_price_filter(): void
    {
        $structure = $this->createFullStructure();
        $this->createApprovedNote($structure, ['title' => 'Cheap', 'price' => 50.00]);
        $this->createApprovedNote($structure, ['title' => 'Medium', 'price' => 150.00]);
        $this->createApprovedNote($structure, ['title' => 'Expensive', 'price' => 300.00]);

        $response = $this->getJson('/api/materials?min_price=100&max_price=200');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Medium');
    }

    public function test_materials_sort_by_newest(): void
    {
        $structure = $this->createFullStructure();
        $this->createApprovedNote($structure, ['title' => 'First']);
        sleep(1); // Ensure different timestamps
        $this->createApprovedNote($structure, ['title' => 'Second']);

        $response = $this->getJson('/api/materials?sort=newest');

        $response->assertStatus(200);
        $this->assertEquals('Second', $response->json('data.0.title'));
    }

    public function test_materials_sort_by_oldest(): void
    {
        $structure = $this->createFullStructure();
        $this->createApprovedNote($structure, ['title' => 'First']);
        $this->createApprovedNote($structure, ['title' => 'Second']);

        $response = $this->getJson('/api/materials?sort=oldest');

        $response->assertStatus(200);
        $this->assertEquals('First', $response->json('data.0.title'));
    }

    public function test_materials_sort_by_price_low(): void
    {
        $structure = $this->createFullStructure();
        $this->createApprovedNote($structure, ['title' => 'Expensive', 'price' => 300.00]);
        $this->createApprovedNote($structure, ['title' => 'Cheap', 'price' => 50.00]);
        $this->createApprovedNote($structure, ['title' => 'Medium', 'price' => 150.00]);

        $response = $this->getJson('/api/materials?sort=price_low');

        $response->assertStatus(200);
        $prices = array_column($response->json('data'), 'price');
        $this->assertEquals([50, 150, 300], array_map(fn($p) => (int)$p, $prices));
    }

    public function test_materials_sort_by_price_high(): void
    {
        $structure = $this->createFullStructure();
        $this->createApprovedNote($structure, ['title' => 'Cheap', 'price' => 50.00]);
        $this->createApprovedNote($structure, ['title' => 'Medium', 'price' => 150.00]);
        $this->createApprovedNote($structure, ['title' => 'Expensive', 'price' => 300.00]);

        $response = $this->getJson('/api/materials?sort=price_high');

        $response->assertStatus(200);
        $prices = array_column($response->json('data'), 'price');
        $this->assertEquals([300, 150, 50], array_map(fn($p) => (int)$p, $prices));
    }

    public function test_materials_search_by_title(): void
    {
        $structure = $this->createFullStructure();
        $this->createApprovedNote($structure, ['title' => 'Mathematics Notes']);
        $this->createApprovedNote($structure, ['title' => 'Physics Guide']);
        $this->createApprovedNote($structure, ['title' => 'Chemistry Notes']);

        $response = $this->getJson('/api/materials?search=Notes');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    public function test_materials_search_by_description(): void
    {
        $structure = $this->createFullStructure();
        $this->createApprovedNote($structure, [
            'title' => 'Note 1',
            'description' => 'Learn about algebra'
        ]);
        $this->createApprovedNote($structure, [
            'title' => 'Note 2',
            'description' => 'Physics fundamentals'
        ]);

        $response = $this->getJson('/api/materials?search=algebra');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function test_materials_pagination(): void
    {
        $structure = $this->createFullStructure();

        for ($i = 1; $i <= 25; $i++) {
            $this->createApprovedNote($structure, ['title' => "Note {$i}"]);
        }

        $response = $this->getJson('/api/materials?per_page=10');

        $response->assertStatus(200)
            ->assertJsonCount(10, 'data')
            ->assertJsonPath('meta.per_page', 10)
            ->assertJsonPath('meta.total', 25)
            ->assertJsonPath('meta.last_page', 3);

        // Test page 2
        $response = $this->getJson('/api/materials?per_page=10&page=2');

        $response->assertStatus(200)
            ->assertJsonCount(10, 'data')
            ->assertJsonPath('meta.current_page', 2);

        // Test last page
        $response = $this->getJson('/api/materials?per_page=10&page=3');

        $response->assertStatus(200)
            ->assertJsonCount(5, 'data')
            ->assertJsonPath('meta.current_page', 3);
    }
}
