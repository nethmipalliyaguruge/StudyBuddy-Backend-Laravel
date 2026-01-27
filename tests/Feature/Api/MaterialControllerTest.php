<?php

namespace Tests\Feature\Api;

use App\Models\Level;
use App\Models\Module;
use App\Models\Note;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MaterialControllerTest extends TestCase
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

    public function test_can_list_approved_materials(): void
    {
        $structure = $this->createFullStructure();

        $this->createApprovedNote($structure, ['title' => 'Approved Note 1']);
        $this->createApprovedNote($structure, ['title' => 'Approved Note 2']);
        Note::create([
            'user_id' => $structure['user']->id,
            'module_id' => $structure['module']->id,
            'title' => 'Pending Note',
            'description' => 'Test',
            'price' => 50.00,
            'status' => 'pending',
        ]);

        $response = $this->getJson('/api/materials');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'title', 'description', 'price', 'module', 'user'],
                ],
                'links',
                'meta',
            ]);
    }

    public function test_can_filter_materials_by_school(): void
    {
        $school1 = School::create(['name' => 'School 1', 'description' => 'S1']);
        $school2 = School::create(['name' => 'School 2', 'description' => 'S2']);
        $level1 = Level::create(['school_id' => $school1->id, 'name' => 'Level 1']);
        $level2 = Level::create(['school_id' => $school2->id, 'name' => 'Level 1']);
        $module1 = Module::create(['level_id' => $level1->id, 'title' => 'Module 1', 'status' => true]);
        $module2 = Module::create(['level_id' => $level2->id, 'title' => 'Module 2', 'status' => true]);
        $user = User::factory()->create();

        Note::create([
            'user_id' => $user->id,
            'module_id' => $module1->id,
            'title' => 'School 1 Note',
            'description' => 'Test',
            'price' => 100.00,
            'status' => 'approved',
        ]);
        Note::create([
            'user_id' => $user->id,
            'module_id' => $module2->id,
            'title' => 'School 2 Note',
            'description' => 'Test',
            'price' => 100.00,
            'status' => 'approved',
        ]);

        $response = $this->getJson("/api/materials?school_id={$school1->id}");

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'School 1 Note');
    }

    public function test_can_filter_materials_by_level(): void
    {
        $school = School::create(['name' => 'Test School', 'description' => 'Test']);
        $level1 = Level::create(['school_id' => $school->id, 'name' => 'Level 1']);
        $level2 = Level::create(['school_id' => $school->id, 'name' => 'Level 2']);
        $module1 = Module::create(['level_id' => $level1->id, 'title' => 'Module 1', 'status' => true]);
        $module2 = Module::create(['level_id' => $level2->id, 'title' => 'Module 2', 'status' => true]);
        $user = User::factory()->create();

        Note::create([
            'user_id' => $user->id,
            'module_id' => $module1->id,
            'title' => 'Level 1 Note',
            'description' => 'Test',
            'price' => 100.00,
            'status' => 'approved',
        ]);
        Note::create([
            'user_id' => $user->id,
            'module_id' => $module2->id,
            'title' => 'Level 2 Note',
            'description' => 'Test',
            'price' => 100.00,
            'status' => 'approved',
        ]);

        $response = $this->getJson("/api/materials?level_id={$level1->id}");

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Level 1 Note');
    }

    public function test_can_filter_materials_by_module(): void
    {
        $structure = $this->createFullStructure();
        $module2 = Module::create(['level_id' => $structure['level']->id, 'title' => 'Module 2', 'status' => true]);

        $this->createApprovedNote($structure, ['title' => 'Module 1 Note']);
        Note::create([
            'user_id' => $structure['user']->id,
            'module_id' => $module2->id,
            'title' => 'Module 2 Note',
            'description' => 'Test',
            'price' => 100.00,
            'status' => 'approved',
        ]);

        $response = $this->getJson("/api/materials?module_id={$structure['module']->id}");

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Module 1 Note');
    }

    public function test_can_search_materials_by_title(): void
    {
        $structure = $this->createFullStructure();

        $this->createApprovedNote($structure, ['title' => 'Mathematics Notes']);
        $this->createApprovedNote($structure, ['title' => 'Physics Notes']);
        $this->createApprovedNote($structure, ['title' => 'Chemistry Guide']);

        $response = $this->getJson('/api/materials?search=Notes');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    public function test_can_filter_materials_by_price_range(): void
    {
        $structure = $this->createFullStructure();

        $this->createApprovedNote($structure, ['title' => 'Cheap Note', 'price' => 50.00]);
        $this->createApprovedNote($structure, ['title' => 'Medium Note', 'price' => 150.00]);
        $this->createApprovedNote($structure, ['title' => 'Expensive Note', 'price' => 300.00]);

        $response = $this->getJson('/api/materials?min_price=100&max_price=200');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Medium Note');
    }

    public function test_can_sort_materials(): void
    {
        $structure = $this->createFullStructure();

        $this->createApprovedNote($structure, ['title' => 'Note A', 'price' => 300.00]);
        $this->createApprovedNote($structure, ['title' => 'Note B', 'price' => 100.00]);
        $this->createApprovedNote($structure, ['title' => 'Note C', 'price' => 200.00]);

        // Sort by price low to high
        $response = $this->getJson('/api/materials?sort=price_low');

        $response->assertStatus(200);
        $prices = array_column($response->json('data'), 'price');
        $this->assertEquals([100, 200, 300], array_map(fn($p) => (int)$p, $prices));

        // Sort by price high to low
        $response = $this->getJson('/api/materials?sort=price_high');

        $response->assertStatus(200);
        $prices = array_column($response->json('data'), 'price');
        $this->assertEquals([300, 200, 100], array_map(fn($p) => (int)$p, $prices));
    }

    public function test_materials_are_paginated(): void
    {
        $structure = $this->createFullStructure();

        for ($i = 1; $i <= 20; $i++) {
            $this->createApprovedNote($structure, ['title' => "Note {$i}"]);
        }

        $response = $this->getJson('/api/materials?per_page=5');

        $response->assertStatus(200)
            ->assertJsonCount(5, 'data')
            ->assertJsonPath('meta.per_page', 5)
            ->assertJsonPath('meta.total', 20);
    }

    public function test_can_view_single_material(): void
    {
        $structure = $this->createFullStructure();
        $note = $this->createApprovedNote($structure, ['title' => 'Test Material']);

        $response = $this->getJson("/api/materials/{$note->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.title', 'Test Material')
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'description',
                    'price',
                    'module',
                    'user',
                ],
            ]);
    }

    public function test_returns_404_for_nonexistent_material(): void
    {
        $response = $this->getJson('/api/materials/99999');

        $response->assertStatus(404);
    }

    public function test_returns_404_for_non_approved_material(): void
    {
        $structure = $this->createFullStructure();
        $note = Note::create([
            'user_id' => $structure['user']->id,
            'module_id' => $structure['module']->id,
            'title' => 'Pending Note',
            'description' => 'Test',
            'price' => 100.00,
            'status' => 'pending',
        ]);

        $response = $this->getJson("/api/materials/{$note->id}");

        $response->assertStatus(404);
    }
}
