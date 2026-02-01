<?php

namespace Tests\Feature\Admin;

use App\Models\Level;
use App\Models\Module;
use App\Models\Note;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_dashboard(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertStatus(200);
    }

    public function test_student_cannot_access_admin_dashboard(): void
    {
        $student = User::factory()->create(['role' => 'student']);

        $response = $this->actingAs($student)->get('/admin/dashboard');

        $response->assertStatus(403);
    }

    public function test_unauthenticated_user_is_redirected(): void
    {
        $response = $this->get('/admin/dashboard');

        $response->assertRedirect('/login');
    }

    public function test_dashboard_shows_statistics(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // Create test data
        School::create(['name' => 'School 1', 'description' => 'S1']);
        School::create(['name' => 'School 2', 'description' => 'S2']);

        $school = School::first();
        $level = Level::create(['school_id' => $school->id, 'name' => 'Level 1']);
        $module = Module::create(['level_id' => $level->id, 'title' => 'Module 1', 'status' => true]);

        User::factory()->count(3)->create(['role' => 'student']);

        $seller = User::factory()->create();
        Note::create([
            'user_id' => $seller->id,
            'module_id' => $module->id,
            'title' => 'Note 1',
            'description' => 'Test',
            'price' => 100.00,
            'status' => 'approved',
        ]);

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertStatus(200);
    }

    public function test_blocked_admin_cannot_access_dashboard(): void
    {
        $blockedAdmin = User::factory()->create([
            'role' => 'admin',
            'is_blocked' => true,
        ]);

        $response = $this->actingAs($blockedAdmin)->get('/admin/dashboard');

        $response->assertRedirect('/login');
    }
}
