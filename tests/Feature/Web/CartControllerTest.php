<?php

namespace Tests\Feature\Web;

use App\Models\Level;
use App\Models\Module;
use App\Models\Note;
use App\Models\Purchase;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private User $seller;
    private Module $module;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['is_blocked' => false]);
        $this->seller = User::factory()->create();

        $school = School::create(['name' => 'Test School', 'description' => 'Test']);
        $level = Level::create(['school_id' => $school->id, 'name' => 'Level 1']);
        $this->module = Module::create(['level_id' => $level->id, 'title' => 'Module 1', 'status' => true]);
    }

    private function createNote(array $attributes = []): Note
    {
        return Note::create(array_merge([
            'user_id' => $this->seller->id,
            'module_id' => $this->module->id,
            'title' => 'Test Note',
            'description' => 'Test description',
            'price' => 100.00,
            'status' => 'approved',
        ], $attributes));
    }

    public function test_authenticated_user_can_view_cart(): void
    {
        $response = $this->actingAs($this->user)->get('/cart');

        $response->assertStatus(200);
    }

    public function test_unauthenticated_user_is_redirected_from_cart(): void
    {
        $response = $this->get('/cart');

        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_add_to_cart(): void
    {
        $note = $this->createNote();

        $response = $this->actingAs($this->user)->post("/cart/add/{$note->id}");

        $response->assertRedirect();
    }

    public function test_authenticated_user_can_remove_from_cart(): void
    {
        $note = $this->createNote();

        $response = $this->actingAs($this->user)->post("/cart/remove/{$note->id}");

        $response->assertRedirect();
    }

    public function test_blocked_user_cannot_access_cart(): void
    {
        $blockedUser = User::factory()->create(['is_blocked' => true]);

        $response = $this->actingAs($blockedUser)->get('/cart');

        $response->assertRedirect('/login');
    }
}
