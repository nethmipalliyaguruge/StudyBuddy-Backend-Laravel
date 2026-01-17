<?php

namespace App\Http\Controllers;

use App\Models\Level;
use App\Models\Module;
use App\Models\Note;
use App\Models\School;
use Illuminate\Http\Request;

class BrowseController extends Controller
{
    public function index(Request $request)
    {
        // Get all schools for filter dropdown
        $schools = School::orderBy('name')->get();

        // Initialize empty collections for levels and modules
        $levels = collect();
        $modules = collect();

        // Build query
        $query = Note::where('status', 'approved')
            ->with(['user', 'media', 'module.level.school']);

        // Filter by school
        if ($request->filled('school')) {
            $levels = Level::where('school_id', $request->school)->orderBy('name')->get();

            $query->whereHas('module.level', function ($q) use ($request) {
                $q->where('school_id', $request->school);
            });
        }

        // Filter by level
        if ($request->filled('level')) {
            $modules = Module::where('level_id', $request->level)
                ->where('status', true)
                ->orderBy('title')
                ->get();

            $query->whereHas('module', function ($q) use ($request) {
                $q->where('level_id', $request->level);
            });
        }

        // Filter by module
        if ($request->filled('module')) {
            $query->where('module_id', $request->module);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Get results with pagination
        $notes = $query->latest()->paginate(12);

        return view('materials.index', compact('notes', 'schools', 'levels', 'modules'));
    }

    public function show(Note $note)
    {
        abort_if(!$note->isApproved(), 404);

        // Load relationships
        $note->load(['user', 'module.level.school', 'media']);

        return view('materials.show', compact('note'));
    }
}
