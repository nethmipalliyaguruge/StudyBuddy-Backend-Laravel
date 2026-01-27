<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MaterialResource;
use App\Http\Resources\PurchaseResource;
use App\Models\Note;
use App\Models\Purchase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;

class CartController extends Controller
{
    /**
     * Get cart items for the authenticated user.
     * Cart is stored in a dedicated user_carts table or session-like storage.
     * For API, we'll use a simple JSON column approach or return based on request.
     */
    public function index(Request $request): JsonResponse
    {
        // For API, cart items are passed in the request or stored server-side
        // Here we assume cart is managed client-side and validated on checkout
        $cartIds = $request->input('cart_ids', []);

        if (!is_array($cartIds)) {
            $cartIds = [];
        }

        $items = Note::with(['module.level.school', 'user', 'media'])
            ->whereIn('id', $cartIds)
            ->where('status', 'approved')
            ->get();

        // Filter out already purchased items
        $purchasedIds = Purchase::where('user_id', Auth::id())
            ->where('status', 'completed')
            ->pluck('note_id')
            ->toArray();

        // Filter out user's own notes
        $items = $items->filter(function ($note) use ($purchasedIds) {
            return !in_array($note->id, $purchasedIds) && $note->user_id !== Auth::id();
        });

        $total = $items->sum('price');

        return response()->json([
            'items' => MaterialResource::collection($items),
            'total' => number_format($total, 2),
            'count' => $items->count(),
        ]);
    }

    /**
     * Validate if a note can be added to cart.
     */
    public function validateItem(Note $note): JsonResponse
    {
        $errors = [];

        if ($note->status !== 'approved') {
            $errors[] = 'This material is not available for purchase.';
        }

        if ($note->user_id === Auth::id()) {
            $errors[] = 'You cannot purchase your own note.';
        }

        $alreadyPurchased = Purchase::where('user_id', Auth::id())
            ->where('note_id', $note->id)
            ->where('status', 'completed')
            ->exists();

        if ($alreadyPurchased) {
            $errors[] = 'You have already purchased this material.';
        }

        if (count($errors) > 0) {
            return response()->json([
                'valid' => false,
                'errors' => $errors,
            ], 422);
        }

        return response()->json([
            'valid' => true,
            'note' => new MaterialResource($note->load(['module.level.school', 'user', 'media'])),
        ]);
    }

    /**
     * Process checkout and create Stripe session.
     */
    public function checkout(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'cart_ids' => ['required', 'array', 'min:1'],
            'cart_ids.*' => ['integer', 'exists:notes,id'],
        ]);

        $cartIds = $validated['cart_ids'];
        $user = Auth::user();

        // Get valid notes
        $notes = Note::whereIn('id', $cartIds)
            ->where('status', 'approved')
            ->where('user_id', '!=', $user->id)
            ->get();

        if ($notes->isEmpty()) {
            return response()->json([
                'error' => 'No valid items in cart.',
            ], 422);
        }

        // Filter out already purchased
        $purchasedIds = Purchase::where('user_id', $user->id)
            ->where('status', 'completed')
            ->pluck('note_id')
            ->toArray();

        $notes = $notes->filter(fn($note) => !in_array($note->id, $purchasedIds));

        if ($notes->isEmpty()) {
            return response()->json([
                'error' => 'All items have already been purchased.',
            ], 422);
        }

        // Create pending purchases
        $purchases = [];
        $lineItems = [];
        $commissionRate = 0.10; // 10% commission

        foreach ($notes as $note) {
            $commission = $note->price * $commissionRate;

            $purchase = Purchase::create([
                'user_id' => $user->id,
                'note_id' => $note->id,
                'price' => $note->price,
                'commission' => $commission,
                'status' => 'pending',
                'payment_method' => 'stripe',
            ]);

            $purchases[] = $purchase;

            $lineItems[] = [
                'price_data' => [
                    'currency' => 'lkr',
                    'product_data' => [
                        'name' => $note->title,
                        'description' => substr($note->description, 0, 100),
                    ],
                    'unit_amount' => (int)($note->price * 100), // Convert to cents
                ],
                'quantity' => 1,
            ];
        }

        // Create Stripe checkout session
        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            $session = StripeSession::create([
                'payment_method_types' => ['card'],
                'line_items' => $lineItems,
                'mode' => 'payment',
                'success_url' => $request->input('success_url', config('app.url') . '/api/checkout/success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => $request->input('cancel_url', config('app.url') . '/api/checkout/cancel'),
                'customer_email' => $user->email,
                'metadata' => [
                    'user_id' => $user->id,
                    'purchase_ids' => implode(',', array_map(fn($p) => $p->id, $purchases)),
                ],
            ]);

            // Update purchases with session ID
            foreach ($purchases as $purchase) {
                $purchase->update(['stripe_session_id' => $session->id]);
            }

            return response()->json([
                'checkout_url' => $session->url,
                'session_id' => $session->id,
            ]);
        } catch (\Exception $e) {
            // Clean up pending purchases on failure
            foreach ($purchases as $purchase) {
                $purchase->update(['status' => 'failed']);
            }

            return response()->json([
                'error' => 'Failed to create checkout session.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Verify checkout success (called by client after Stripe redirect).
     */
    public function verifyCheckout(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'session_id' => ['required', 'string'],
        ]);

        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            $session = StripeSession::retrieve($validated['session_id']);

            if ($session->payment_status !== 'paid') {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment not completed.',
                ], 422);
            }

            // Update purchases
            $purchases = Purchase::where('stripe_session_id', $session->id)
                ->where('user_id', Auth::id())
                ->get();

            foreach ($purchases as $purchase) {
                $purchase->update([
                    'status' => 'completed',
                    'stripe_payment_intent_id' => $session->payment_intent,
                    'paid_at' => now(),
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Payment verified successfully.',
                'purchases' => PurchaseResource::collection($purchases->load(['note.module.level.school', 'note.media'])),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to verify payment.',
            ], 500);
        }
    }
}
