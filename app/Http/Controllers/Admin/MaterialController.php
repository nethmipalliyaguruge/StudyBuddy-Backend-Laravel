<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Note;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    public function approve(Note $note)
    {
        $note->update([
            'status' => 'approved',
        ]);

        return back()->with('success', 'Material approved successfully');
    }

    public function pending(Note $note)
    {
        $note->update([
            'status' => 'pending',
        ]);

        return back()->with('success', 'Material set to pending');
    }

    public function destroy(Note $note)
    {
        if ($note->hasPurchases()) {
            $note->update(['status' => 'disabled']);

            return back()->with('success', 'Material disabled (has purchases).');
        }

        $note->delete();

        return back()->with('success', 'Material deleted successfully');
    }

    public function enable(Note $note)
    {
        $note->update(['status' => 'approved']);

        return back()->with('success', 'Material enabled successfully');
    }
}
