<?php

namespace Tests\Unit\Models;

use App\Models\Level;
use App\Models\Module;
use App\Models\Note;
use App\Models\Purchase;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NoteTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Module $module;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $school = School::create(['name' => 'Test School', 'description' => 'Test']);
        $level = Level::create(['school_id' => $school->id, 'name' => 'Level 1']);
        $this->module = Module::create(['level_id' => $level->id, 'title' => 'Module 1', 'status' => true]);
    }

    private function createNote(array $attributes = []): Note
    {
        return Note::create(array_merge([
            'user_id' => $this->user->id,
            'module_id' => $this->module->id,
            'title' => 'Test Note',
            'description' => 'Test description',
            'price' => 100.00,
            'status' => 'pending',
        ], $attributes));
    }

    public function test_note_belongs_to_user(): void
    {
        $note = $this->createNote();

        $this->assertInstanceOf(User::class, $note->user);
        $this->assertEquals($this->user->id, $note->user->id);
    }

    public function test_note_belongs_to_module(): void
    {
        $note = $this->createNote();

        $this->assertInstanceOf(Module::class, $note->module);
        $this->assertEquals($this->module->id, $note->module->id);
    }

    public function test_note_is_approved_returns_true_for_approved_notes(): void
    {
        $approvedNote = $this->createNote(['status' => 'approved']);
        $pendingNote = $this->createNote(['status' => 'pending']);
        $disabledNote = $this->createNote(['status' => 'disabled']);

        $this->assertTrue($approvedNote->isApproved());
        $this->assertFalse($pendingNote->isApproved());
        $this->assertFalse($disabledNote->isApproved());
    }

    public function test_note_has_purchases_relationship(): void
    {
        $note = $this->createNote(['status' => 'approved']);
        $buyer = User::factory()->create();

        $purchase = Purchase::create([
            'user_id' => $buyer->id,
            'note_id' => $note->id,
            'price' => $note->price,
            'commission' => 10.00,
            'status' => 'completed',
            'payment_method' => 'stripe',
            'paid_at' => now(),
        ]);

        $this->assertTrue($note->purchases->contains($purchase));
        $this->assertCount(1, $note->purchases);
    }

    public function test_note_has_purchases_returns_true_when_purchases_exist(): void
    {
        $note = $this->createNote(['status' => 'approved']);
        $buyer = User::factory()->create();

        $this->assertFalse($note->hasPurchases());

        Purchase::create([
            'user_id' => $buyer->id,
            'note_id' => $note->id,
            'price' => $note->price,
            'commission' => 10.00,
            'status' => 'completed',
            'payment_method' => 'stripe',
            'paid_at' => now(),
        ]);

        $this->assertTrue($note->fresh()->hasPurchases());
    }

    public function test_note_price_is_cast_to_decimal(): void
    {
        $note = $this->createNote(['price' => 150.50]);

        $this->assertEquals('150.50', $note->price);
    }

    public function test_note_fillable_attributes(): void
    {
        $note = $this->createNote([
            'title' => 'Math Notes',
            'description' => 'Comprehensive math notes',
            'price' => 250.00,
            'status' => 'approved',
        ]);

        $this->assertEquals('Math Notes', $note->title);
        $this->assertEquals('Comprehensive math notes', $note->description);
        $this->assertEquals('250.00', $note->price);
        $this->assertEquals('approved', $note->status);
    }

    public function test_note_status_values(): void
    {
        $pendingNote = $this->createNote(['status' => 'pending']);
        $approvedNote = $this->createNote(['status' => 'approved']);
        $disabledNote = $this->createNote(['status' => 'disabled']);

        $this->assertEquals('pending', $pendingNote->status);
        $this->assertEquals('approved', $approvedNote->status);
        $this->assertEquals('disabled', $disabledNote->status);
    }
}
