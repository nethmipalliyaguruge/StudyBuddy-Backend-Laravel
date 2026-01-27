<?php

namespace Tests\Feature\Api;

use App\Models\Level;
use App\Models\School;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SchoolControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_all_schools(): void
    {
        School::create(['name' => 'Engineering School', 'description' => 'Engineering']);
        School::create(['name' => 'Business School', 'description' => 'Business']);
        School::create(['name' => 'Art School', 'description' => 'Art']);

        $response = $this->getJson('/api/schools');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'description'],
                ],
            ]);
    }

    public function test_can_view_single_school_with_levels(): void
    {
        $school = School::create(['name' => 'Engineering School', 'description' => 'Engineering']);
        Level::create(['school_id' => $school->id, 'name' => 'Level 1']);
        Level::create(['school_id' => $school->id, 'name' => 'Level 2']);

        $response = $this->getJson("/api/schools/{$school->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.name', 'Engineering School')
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'description',
                    'levels' => [
                        '*' => ['id', 'name'],
                    ],
                ],
            ]);
    }

    public function test_returns_404_for_nonexistent_school(): void
    {
        $response = $this->getJson('/api/schools/99999');

        $response->assertStatus(404);
    }
}
