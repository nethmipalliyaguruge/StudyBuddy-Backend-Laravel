<?php

namespace Tests\Unit\Models;

use App\Models\Level;
use App\Models\School;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SchoolTest extends TestCase
{
    use RefreshDatabase;

    public function test_school_has_levels_relationship(): void
    {
        $school = School::create(['name' => 'Test School', 'description' => 'Test description']);

        $level1 = Level::create(['school_id' => $school->id, 'name' => 'Level 1']);
        $level2 = Level::create(['school_id' => $school->id, 'name' => 'Level 2']);

        $this->assertCount(2, $school->levels);
        $this->assertTrue($school->levels->contains($level1));
        $this->assertTrue($school->levels->contains($level2));
    }

    public function test_school_fillable_attributes(): void
    {
        $school = School::create([
            'name' => 'University of Test',
            'description' => 'A test university',
        ]);

        $this->assertEquals('University of Test', $school->name);
        $this->assertEquals('A test university', $school->description);
    }

    public function test_school_can_be_created_without_description(): void
    {
        $school = School::create([
            'name' => 'Simple School',
            'description' => '',
        ]);

        $this->assertEquals('Simple School', $school->name);
        $this->assertEquals('', $school->description);
    }

    public function test_school_with_no_levels(): void
    {
        $school = School::create(['name' => 'Empty School', 'description' => 'No levels']);

        $this->assertCount(0, $school->levels);
    }
}
