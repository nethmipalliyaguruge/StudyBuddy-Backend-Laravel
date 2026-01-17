<?php

namespace App\Http\Controllers;

use App\Models\Note;

class BrowseController extends Controller
{
    public function index()
    {
        $notes = Note::where('status', 'approved')
            ->with(['user', 'media'])
            ->latest()
            ->get();

        return view('materials.index', compact('notes'));
    }

    public function show(Note $note)
    {
        abort_if(!$note->isApproved(), 404);

        return view('materials.show', compact('note'));
    }

}
