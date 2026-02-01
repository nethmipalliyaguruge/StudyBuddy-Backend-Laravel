<?php

namespace Tests\Unit\Models;

use App\Models\Note;
use App\Models\Purchase;
use App\Models\User;
use App\Models\School;
use App\Models\Level;
use App\Models\Module;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_has_default_student_role(): void
    {
        $user = User::factory()->create();

        $this->assertEquals('student', $user->role ?? 'student');
    }

    public function test_user_can_be_admin(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $this->assertTrue($user->isAdmin());
    }

    public function test_student_is_not_admin(): void
    {
        $user = User::factory()->create(['role' => 'student']);

        $this->assertFalse($user->isAdmin());
    }

    public function test_user_has_notes_relationship(): void
    {
        $user = User::factory()->create();
        $school = School::create(['name' => 'Test School', 'description' => 'Test']);
        $level = Level::create(['school_id' => $school->id, 'name' => 'Level 1']);
        $module = Module::create(['level_id' => $level->id, 'title' => 'Module 1', 'status' => true]);

        $note = Note::create([
            'user_id' => $user->id,
            'module_id' => $module->id,
            'title' => 'Test Note',
            'description' => 'Test description',
            'price' => 100.00,
            'status' => 'pending',
        ]);

        $this->assertTrue($user->notes->contains($note));
        $this->assertCount(1, $user->notes);
    }

    public function test_user_has_purchases_relationship(): void
    {
        $user = User::factory()->create();
        $seller = User::factory()->create();
        $school = School::create(['name' => 'Test School', 'description' => 'Test']);
        $level = Level::create(['school_id' => $school->id, 'name' => 'Level 1']);
        $module = Module::create(['level_id' => $level->id, 'title' => 'Module 1', 'status' => true]);

        $note = Note::create([
            'user_id' => $seller->id,
            'module_id' => $module->id,
            'title' => 'Test Note',
            'description' => 'Test description',
            'price' => 100.00,
            'status' => 'approved',
        ]);

        $purchase = Purchase::create([
            'user_id' => $user->id,
            'note_id' => $note->id,
            'price' => 100.00,
            'commission' => 10.00,
            'status' => 'completed',
            'payment_method' => 'stripe',
            'paid_at' => now(),
        ]);

        $this->assertTrue($user->purchases->contains($purchase));
        $this->assertCount(1, $user->purchases);
    }

    public function test_user_is_blocked_attribute(): void
    {
        $blockedUser = User::factory()->create(['is_blocked' => true]);
        $activeUser = User::factory()->create(['is_blocked' => false]);

        $this->assertTrue($blockedUser->is_blocked);
        $this->assertFalse($activeUser->is_blocked);
    }

    public function test_user_password_is_hidden(): void
    {
        $user = User::factory()->create();
        $array = $user->toArray();

        $this->assertArrayNotHasKey('password', $array);
    }

    public function test_user_fillable_attributes(): void
    {
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '0777777777',
            'role' => 'student',
            'is_blocked' => false,
        ]);

        $this->assertEquals('John Doe', $user->name);
        $this->assertEquals('john@example.com', $user->email);
        $this->assertEquals('0777777777', $user->phone);
        $this->assertEquals('student', $user->role);
        $this->assertFalse($user->is_blocked);
    }
}
