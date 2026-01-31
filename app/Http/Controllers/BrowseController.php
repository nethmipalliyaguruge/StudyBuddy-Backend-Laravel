<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;

class BrowseController extends Controller
{
    public function index()
    {
        // Livewire component handles all filtering
        return view('materials.index');
    }

    public function show(Note $note)
    {
        abort_if(!$note->isApproved(), 404);

        // Load relationships
        $note->load(['user', 'module.level.school', 'media']);

        return view('materials.show', compact('note'));
    }
}
