<?php

namespace Tests\Feature\Api;

use App\Models\Level;
use App\Models\Module;
use App\Models\School;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModuleControllerTest extends TestCase
{
    use RefreshDatabase;

    private function createSchoolLevelStructure(): array
    {
        $school = School::create(['name' => 'Test School', 'description' => 'Test']);
        $level = Level::create(['school_id' => $school->id, 'name' => 'Level 1']);

        return ['school' => $school, 'level' => $level];
    }

    public function test_can_list_active_modules(): void
    {
        $structure = $this->createSchoolLevelStructure();

        Module::create(['level_id' => $structure['level']->id, 'title' => 'Active Module 1', 'status' => true]);
        Module::create(['level_id' => $structure['level']->id, 'title' => 'Active Module 2', 'status' => true]);
        Module::create(['level_id' => $structure['level']->id, 'title' => 'Inactive Module', 'status' => false]);

        $response = $this->getJson('/api/modules');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'title', 'level'],
                ],
            ]);
    }

    public function test_can_filter_modules_by_level(): void
    {
        $school = School::create(['name' => 'Test School', 'description' => 'Test']);
        $level1 = Level::create(['school_id' => $school->id, 'name' => 'Level 1']);
        $level2 = Level::create(['school_id' => $school->id, 'name' => 'Level 2']);

        Module::create(['level_id' => $level1->id, 'title' => 'L1 Module 1', 'status' => true]);
        Module::create(['level_id' => $level1->id, 'title' => 'L1 Module 2', 'status' => true]);
        Module::create(['level_id' => $level2->id, 'title' => 'L2 Module 1', 'status' => true]);

        $response = $this->getJson("/api/modules?level_id={$level1->id}");

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');

        foreach ($response->json('data') as $module) {
            $this->assertEquals($level1->id, $module['level']['id']);
        }
    }

    public function test_can_view_single_module(): void
    {
        $structure = $this->createSchoolLevelStructure();
        $module = Module::create([
            'level_id' => $structure['level']->id,
            'title' => 'Test Module',
            'status' => true,
        ]);

        $response = $this->getJson("/api/modules/{$module->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.title', 'Test Module')
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'level' => [
                        'id',
                        'name',
                        'school',
                    ],
                ],
            ]);
    }

    public function test_returns_404_for_nonexistent_module(): void
    {
        $response = $this->getJson('/api/modules/99999');

        $response->assertStatus(404);
    }
}
