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

        // Prevent duplicate purchases
        $alreadyPurchased = Purchase::where('user_id', auth()->id())
            ->where('note_id', $note->id)
            ->exists();

        if ($alreadyPurchased) {
            return back()->with('error', 'You already purchased this note.');
        }

        Purchase::create([
            'user_id'    => auth()->id(),
            'note_id'    => $note->id,
            'price'      => $note->price,
            'commission' => 0,
            'status'     => 'completed',
        ]);

        return redirect()
            ->route('purchases.index')
            ->with('success', 'Purchase successful. You can now download the note.');
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
