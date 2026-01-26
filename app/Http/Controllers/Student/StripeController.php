<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Note;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class StripeController extends Controller
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Create a Stripe Checkout session and redirect to Stripe
     */
    public function checkout(Request $request)
    {
        $cart = session('cart', []);
        $cart = collect($cart)->flatten()->filter(fn($id) => is_numeric($id))->map(fn($id) => (int) $id)->unique()->values()->toArray();

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $notes = Note::whereIn('id', $cart)
            ->where('status', 'approved')
            ->where('user_id', '!=', Auth::id())
            ->get();

        if ($notes->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'No valid items in cart.');
        }

        // Check for already purchased notes
        $alreadyPurchased = Purchase::where('user_id', Auth::id())
            ->whereIn('note_id', $notes->pluck('id'))
            ->where('status', 'completed')
            ->pluck('note_id')
            ->toArray();

        $notes = $notes->reject(fn($note) => in_array($note->id, $alreadyPurchased));

        if ($notes->isEmpty()) {
            session()->forget('cart');
            return redirect()->route('cart.index')->with('error', 'You have already purchased all items in your cart.');
        }

        // Build line items for Stripe
        $lineItems = [];
        foreach ($notes as $note) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'lkr',
                    'product_data' => [
                        'name' => $note->title,
                        'description' => 'Study notes by ' . ($note->user->name ?? 'Unknown'),
                    ],
                    'unit_amount' => (int) ($note->price * 100), // Stripe uses cents
                ],
                'quantity' => 1,
            ];
        }

        // Create pending purchases
        $stripeSessionId = 'pending_' . uniqid();
        $purchases = [];

        foreach ($notes as $note) {
            $commission = $note->price * 0.10; // 10% commission
            $purchases[] = Purchase::create([
                'user_id' => Auth::id(),
                'note_id' => $note->id,
                'price' => $note->price,
                'commission' => $commission,
                'status' => 'pending',
                'stripe_session_id' => $stripeSessionId,
                'payment_method' => 'stripe',
            ]);
        }

        // Create Stripe Checkout session
        $checkoutSession = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => route('stripe.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('stripe.cancel'),
            'metadata' => [
                'user_id' => Auth::id(),
                'purchase_ids' => implode(',', collect($purchases)->pluck('id')->toArray()),
            ],
        ]);

        // Update purchases with real Stripe session ID
        Purchase::whereIn('id', collect($purchases)->pluck('id'))
            ->update(['stripe_session_id' => $checkoutSession->id]);

        // Clear cart
        session()->forget('cart');

        return redirect($checkoutSession->url);
    }

    /**
     * Handle successful payment return from Stripe
     */
    public function success(Request $request)
    {
        $sessionId = $request->get('session_id');

        if (!$sessionId) {
            return redirect()->route('purchases.index')->with('error', 'Invalid payment session.');
        }

        try {
            $session = Session::retrieve($sessionId);

            if ($session->payment_status === 'paid') {
                // Update purchases to completed
                Purchase::where('stripe_session_id', $sessionId)
                    ->update([
                        'status' => 'completed',
                        'stripe_payment_intent_id' => $session->payment_intent,
                        'paid_at' => now(),
                    ]);

                return redirect()->route('purchases.index')
                    ->with('success', 'Payment successful! Your notes are now available for download.');
            }

            return redirect()->route('purchases.index')
                ->with('warning', 'Payment is being processed. Please check back later.');

        } catch (\Exception $e) {
            return redirect()->route('purchases.index')
                ->with('error', 'Error verifying payment. Please contact support if the issue persists.');
        }
    }

    /**
     * Handle cancelled payment return from Stripe
     */
    public function cancel()
    {
        // Mark pending purchases as failed
        Purchase::where('user_id', Auth::id())
            ->where('status', 'pending')
            ->whereNotNull('stripe_session_id')
            ->where('created_at', '>=', now()->subMinutes(30))
            ->update(['status' => 'failed']);

        return redirect()->route('cart.index')
            ->with('warning', 'Payment was cancelled. Your items are still in your cart.');
    }
}
