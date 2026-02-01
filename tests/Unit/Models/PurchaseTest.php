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

class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    private User $buyer;
    private User $seller;
    private Note $note;

    protected function setUp(): void
    {
        parent::setUp();

        $this->buyer = User::factory()->create();
        $this->seller = User::factory()->create();
        $school = School::create(['name' => 'Test School', 'description' => 'Test']);
        $level = Level::create(['school_id' => $school->id, 'name' => 'Level 1']);
        $module = Module::create(['level_id' => $level->id, 'title' => 'Module 1', 'status' => true]);

        $this->note = Note::create([
            'user_id' => $this->seller->id,
            'module_id' => $module->id,
            'title' => 'Test Note',
            'description' => 'Test description',
            'price' => 100.00,
            'status' => 'approved',
        ]);
    }

    private function createPurchase(array $attributes = []): Purchase
    {
        return Purchase::create(array_merge([
            'user_id' => $this->buyer->id,
            'note_id' => $this->note->id,
            'price' => 100.00,
            'commission' => 10.00,
            'status' => 'pending',
            'payment_method' => 'stripe',
        ], $attributes));
    }

    public function test_purchase_belongs_to_user(): void
    {
        $purchase = $this->createPurchase();

        $this->assertInstanceOf(User::class, $purchase->user);
        $this->assertEquals($this->buyer->id, $purchase->user->id);
    }

    public function test_purchase_belongs_to_note(): void
    {
        $purchase = $this->createPurchase();

        $this->assertInstanceOf(Note::class, $purchase->note);
        $this->assertEquals($this->note->id, $purchase->note->id);
    }

    public function test_purchase_status_values(): void
    {
        $pendingPurchase = $this->createPurchase(['status' => 'pending']);
        $completedPurchase = $this->createPurchase(['status' => 'completed', 'paid_at' => now()]);
        $failedPurchase = $this->createPurchase(['status' => 'failed']);

        $this->assertEquals('pending', $pendingPurchase->status);
        $this->assertEquals('completed', $completedPurchase->status);
        $this->assertEquals('failed', $failedPurchase->status);
    }

    public function test_purchase_price_is_cast_to_decimal(): void
    {
        $purchase = $this->createPurchase(['price' => 150.50]);

        $this->assertEquals('150.50', $purchase->price);
    }

    public function test_purchase_commission_is_cast_to_decimal(): void
    {
        $purchase = $this->createPurchase(['commission' => 15.05]);

        $this->assertEquals('15.05', $purchase->commission);
    }

    public function test_purchase_paid_at_is_cast_to_datetime(): void
    {
        $paidAt = now();
        $purchase = $this->createPurchase(['status' => 'completed', 'paid_at' => $paidAt]);

        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $purchase->paid_at);
    }

    public function test_purchase_fillable_attributes(): void
    {
        $purchase = $this->createPurchase([
            'price' => 200.00,
            'commission' => 20.00,
            'status' => 'completed',
            'stripe_session_id' => 'cs_test_123',
            'stripe_payment_intent_id' => 'pi_test_123',
            'payment_method' => 'stripe',
            'paid_at' => now(),
        ]);

        $this->assertEquals($this->buyer->id, $purchase->user_id);
        $this->assertEquals($this->note->id, $purchase->note_id);
        $this->assertEquals('200.00', $purchase->price);
        $this->assertEquals('20.00', $purchase->commission);
        $this->assertEquals('completed', $purchase->status);
        $this->assertEquals('cs_test_123', $purchase->stripe_session_id);
        $this->assertEquals('pi_test_123', $purchase->stripe_payment_intent_id);
        $this->assertEquals('stripe', $purchase->payment_method);
    }

    public function test_purchase_commission_calculation(): void
    {
        $price = 100.00;
        $commissionRate = 0.10; // 10%
        $expectedCommission = $price * $commissionRate;

        $purchase = $this->createPurchase([
            'price' => $price,
            'commission' => $expectedCommission,
        ]);

        $this->assertEquals('10.00', $purchase->commission);
    }
}
