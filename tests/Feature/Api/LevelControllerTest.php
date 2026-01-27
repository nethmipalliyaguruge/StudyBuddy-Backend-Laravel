<?php

namespace Tests\Feature\Api;

use App\Models\Level;
use App\Models\Module;
use App\Models\School;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LevelControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_all_levels(): void
    {
        $school = School::create(['name' => 'Test School', 'description' => 'Test']);
        Level::create(['school_id' => $school->id, 'name' => 'Level 1']);
        Level::create(['school_id' => $school->id, 'name' => 'Level 2']);

        $response = $this->getJson('/api/levels');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'school'],
                ],
            ]);
    }

    public function test_can_filter_levels_by_school(): void
    {
        $school1 = School::create(['name' => 'School 1', 'description' => 'School 1']);
        $school2 = School::create(['name' => 'School 2', 'description' => 'School 2']);

        Level::create(['school_id' => $school1->id, 'name' => 'S1 Level 1']);
        Level::create(['school_id' => $school1->id, 'name' => 'S1 Level 2']);
        Level::create(['school_id' => $school2->id, 'name' => 'S2 Level 1']);

        $response = $this->getJson("/api/levels?school_id={$school1->id}");

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');

        foreach ($response->json('data') as $level) {
            $this->assertEquals($school1->id, $level['school']['id']);
        }
    }

    public function test_can_view_single_level_with_modules(): void
    {
        $school = School::create(['name' => 'Test School', 'description' => 'Test']);
        $level = Level::create(['school_id' => $school->id, 'name' => 'Level 1']);
        Module::create(['level_id' => $level->id, 'title' => 'Module 1', 'status' => true]);
        Module::create(['level_id' => $level->id, 'title' => 'Module 2', 'status' => true]);

        $response = $this->getJson("/api/levels/{$level->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.name', 'Level 1')
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'school',
                    'modules' => [
                        '*' => ['id', 'title'],
                    ],
                ],
            ]);
    }

    public function test_returns_404_for_nonexistent_level(): void
    {
        $response = $this->getJson('/api/levels/99999');

        $response->assertStatus(404);
    }
}
