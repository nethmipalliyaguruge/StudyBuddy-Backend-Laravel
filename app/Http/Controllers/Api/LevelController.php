<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LevelResource;
use App\Models\Level;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class LevelController extends Controller
{
    /**
     * List levels, optionally filtered by school.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Level::with('school');

        // Filter by school_id if provided
        if ($request->filled('school_id')) {
            $query->where('school_id', $request->school_id);
        }

        $levels = $query->orderBy('name')->get();

        return LevelResource::collection($levels);
    }

    /**
     * Get a single level with its modules.
     */
    public function show(Level $level): LevelResource
    {
        $level->load(['school', 'modules']);

        return new LevelResource($level);
    }
}
