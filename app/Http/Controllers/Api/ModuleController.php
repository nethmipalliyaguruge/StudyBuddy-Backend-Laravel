<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ModuleResource;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ModuleController extends Controller
{
    /**
     * List modules, optionally filtered by level.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Module::with('level.school')
            ->where('status', true); // Only active modules

        // Filter by level_id if provided
        if ($request->filled('level_id')) {
            $query->where('level_id', $request->level_id);
        }

        $modules = $query->orderBy('title')->get();

        return ModuleResource::collection($modules);
    }

    /**
     * Get a single module with its details.
     */
    public function show(Module $module): ModuleResource
    {
        $module->load('level.school');

        return new ModuleResource($module);
    }
}
