<?php

namespace Tests\Unit\Models;

use App\Models\Level;
use App\Models\Module;
use App\Models\Note;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModuleTest extends TestCase
{
    use RefreshDatabase;

    private Level $level;

    protected function setUp(): void
    {
        parent::setUp();

        $school = School::create(['name' => 'Test School', 'description' => 'Test']);
        $this->level = Level::create(['school_id' => $school->id, 'name' => 'Level 1']);
    }

    public function test_module_belongs_to_level(): void
    {
        $module = Module::create(['level_id' => $this->level->id, 'title' => 'Module 1', 'status' => true]);

        $this->assertInstanceOf(Level::class, $module->level);
        $this->assertEquals($this->level->id, $module->level->id);
    }

    public function test_module_has_notes_relationship(): void
    {
        $user = User::factory()->create();
        $module = Module::create(['level_id' => $this->level->id, 'title' => 'Module 1', 'status' => true]);

        $note1 = Note::create([
            'user_id' => $user->id,
            'module_id' => $module->id,
            'title' => 'Note 1',
            'description' => 'Test',
            'price' => 100.00,
            'status' => 'approved',
        ]);
        $note2 = Note::create([
            'user_id' => $user->id,
            'module_id' => $module->id,
            'title' => 'Note 2',
            'description' => 'Test',
            'price' => 150.00,
            'status' => 'pending',
        ]);

        $this->assertCount(2, $module->notes);
        $this->assertTrue($module->notes->contains($note1));
        $this->assertTrue($module->notes->contains($note2));
    }

    public function test_module_status_is_cast_to_boolean(): void
    {
        $activeModule = Module::create(['level_id' => $this->level->id, 'title' => 'Active', 'status' => true]);
        $inactiveModule = Module::create(['level_id' => $this->level->id, 'title' => 'Inactive', 'status' => false]);

        $this->assertTrue($activeModule->status);
        $this->assertFalse($inactiveModule->status);
        $this->assertIsBool($activeModule->status);
        $this->assertIsBool($inactiveModule->status);
    }

    public function test_module_fillable_attributes(): void
    {
        $module = Module::create([
            'level_id' => $this->level->id,
            'title' => 'Mathematics',
            'status' => true,
        ]);

        $this->assertEquals($this->level->id, $module->level_id);
        $this->assertEquals('Mathematics', $module->title);
        $this->assertTrue($module->status);
    }

    public function test_module_with_no_notes(): void
    {
        $module = Module::create(['level_id' => $this->level->id, 'title' => 'Empty Module', 'status' => true]);

        $this->assertCount(0, $module->notes);
    }
}
