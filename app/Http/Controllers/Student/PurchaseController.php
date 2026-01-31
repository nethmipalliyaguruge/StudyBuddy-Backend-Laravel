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

    public function download(Purchase $purchase)
    {
        // Ensure user owns this purchase
        if ($purchase->user_id !== auth()->id()) {
            abort(403, 'You can only download your own purchases.');
        }

        // Ensure purchase is completed
        if ($purchase->status !== 'completed') {
            return back()->with('error', 'This purchase is not completed.');
        }

        // Get the note file
        $noteFile = $purchase->note->getFirstMedia('note_file');

        if (!$noteFile) {
            return back()->with('error', 'No file available for download.');
        }

        // Force download with proper headers
        return response()->download(
            $noteFile->getPath(),
            $noteFile->file_name,
            ['Content-Type' => $noteFile->mime_type]
        );
    }
}
