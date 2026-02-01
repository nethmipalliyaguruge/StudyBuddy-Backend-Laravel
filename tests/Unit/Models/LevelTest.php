<?php

namespace Tests\Unit\Models;

use App\Models\Level;
use App\Models\Module;
use App\Models\School;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LevelTest extends TestCase
{
    use RefreshDatabase;

    private School $school;

    protected function setUp(): void
    {
        parent::setUp();

        $this->school = School::create(['name' => 'Test School', 'description' => 'Test']);
    }

    public function test_level_belongs_to_school(): void
    {
        $level = Level::create(['school_id' => $this->school->id, 'name' => 'Level 1']);

        $this->assertInstanceOf(School::class, $level->school);
        $this->assertEquals($this->school->id, $level->school->id);
    }

    public function test_level_has_modules_relationship(): void
    {
        $level = Level::create(['school_id' => $this->school->id, 'name' => 'Level 1']);

        $module1 = Module::create(['level_id' => $level->id, 'title' => 'Module 1', 'status' => true]);
        $module2 = Module::create(['level_id' => $level->id, 'title' => 'Module 2', 'status' => true]);

        $this->assertCount(2, $level->modules);
        $this->assertTrue($level->modules->contains($module1));
        $this->assertTrue($level->modules->contains($module2));
    }

    public function test_level_fillable_attributes(): void
    {
        $level = Level::create([
            'school_id' => $this->school->id,
            'name' => 'Bachelor Year 1',
        ]);

        $this->assertEquals($this->school->id, $level->school_id);
        $this->assertEquals('Bachelor Year 1', $level->name);
    }

    public function test_level_with_no_modules(): void
    {
        $level = Level::create(['school_id' => $this->school->id, 'name' => 'Empty Level']);

        $this->assertCount(0, $level->modules);
    }
}
