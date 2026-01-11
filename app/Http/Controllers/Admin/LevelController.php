<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Level;
use Illuminate\Http\Request;

class LevelController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'school_id' => 'required|exists:schools,id',
            'name' => 'required|string|max:255',
        ]);

        Level::create([
            'school_id' => $request->school_id,
            'name' => $request->name,
        ]);

        return back()->with('success', 'Level added successfully');
    }

    public function update(Request $request, Level $level)
    {
        $request->validate([
            'school_id' => 'required|exists:schools,id',
            'name' => 'required|string|max:255',
        ]);

        $level->update([
            'school_id' => $request->school_id,
            'name' => $request->name,
        ]);

        return back()->with('success', 'Level updated successfully');
    }

    public function destroy(Level $level)
    {
        // later we’ll block deletion if modules exist
        $level->delete();

        return back()->with('success', 'Level deleted successfully');
    }
}
