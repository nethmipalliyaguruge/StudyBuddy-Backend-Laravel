<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Note;
use App\Models\Purchase;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'note_id' => 'required|exists:notes,id',
        ]);

        $note = Note::findOrFail($request->note_id);

        // Prevent purchasing own note
        if ($note->user_id === auth()->id()) {
            return back()->with('error', 'You cannot purchase your own note.');
        }

        // Prevent duplicate purchases (only check completed)
        $alreadyPurchased = Purchase::where('user_id', auth()->id())
            ->where('note_id', $note->id)
            ->where('status', 'completed')
            ->exists();

        if ($alreadyPurchased) {
            return back()->with('error', 'You already purchased this note.');
        }

        // Add to cart (reuse cart logic)
        $cart = session('cart', []);
        $cart = collect($cart)->flatten()->filter(fn($id) => is_numeric($id))->map(fn($id) => (int) $id)->unique()->values()->toArray();

        // Add note if not already in cart
        if (!in_array($note->id, $cart)) {
            $cart[] = $note->id;
            session(['cart' => $cart]);
        }

        // Redirect to cart page for payment
        return redirect()->route('cart.index')
            ->with('success', 'Note added to cart. Please complete your payment.');
    }

    public function index()
    {
        $purchases = Purchase::where('user_id', auth()->id())
            ->with('note.media')
            ->latest()
            ->get();

        return view('student.my-purchases', compact('purchases'));
    }
}
