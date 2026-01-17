<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Note;
use App\Models\Purchase;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Display the cart.
     */
    public function index()
    {
        return view('cart.index');
    }

    /**
     * Add a note to cart.
     */
    public function add(Note $note)
    {
        // Check if user owns the note
        if ($note->user_id === auth()->id()) {
            return back()->with('error', 'You cannot add your own note to cart.');
        }

        // Check if already purchased
        if (auth()->user()->purchases()->where('note_id', $note->id)->exists()) {
            return back()->with('error', 'You have already purchased this note.');
        }

        // Get current cart and sanitize (defensive against corrupted session data)
        $cart = session('cart', []);
        $cart = collect($cart)->flatten()->filter(fn($id) => is_numeric($id))->map(fn($id) => (int) $id)->unique()->values()->toArray();

        // Check if already in cart
        if (in_array($note->id, $cart)) {
            return back()->with('error', 'This note is already in your cart.');
        }

        // Add to cart
        $cart[] = $note->id;
        session(['cart' => $cart]);

        return back()->with('success', 'Note added to cart!');
    }

    /**
     * Remove a note from cart.
     */
    public function remove(Note $note)
    {
        $cart = session('cart', []);
        // Sanitize cart and remove the specified note
        $cart = collect($cart)->flatten()->filter(fn($id) => is_numeric($id) && (int) $id !== $note->id)->map(fn($id) => (int) $id)->unique()->values()->toArray();
        session(['cart' => $cart]);

        return back()->with('success', 'Note removed from cart.');
    }

    /**
     * Checkout and purchase all items in cart.
     */
    public function checkout()
    {
        $cart = session('cart', []);
        // Ensure cart is a flat array of integers (defensive against corrupted session data)
        $cart = collect($cart)->flatten()->filter(fn($id) => is_numeric($id))->map(fn($id) => (int) $id)->unique()->values()->toArray();

        if (empty($cart)) {
            return back()->with('error', 'Your cart is empty.');
        }

        $notes = Note::whereIn('id', $cart)->get();
        $purchased = 0;

        foreach ($notes as $note) {
            // Skip if already purchased or own note
            if (auth()->user()->purchases()->where('note_id', $note->id)->exists()) {
                continue;
            }

            if ($note->user_id === auth()->id()) {
                continue;
            }

            // Create purchase
            Purchase::create([
                'user_id' => auth()->id(),
                'note_id' => $note->id,
                'price' => $note->price,
            ]);

            $purchased++;
        }

        // Clear cart
        session()->forget('cart');

        if ($purchased > 0) {
            return redirect()->route('purchases.index')
                ->with('success', "Successfully purchased {$purchased} note(s)!");
        }

        return back()->with('error', 'No notes were purchased.');
    }
}
