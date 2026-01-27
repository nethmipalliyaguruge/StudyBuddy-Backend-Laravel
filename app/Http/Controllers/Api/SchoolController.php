<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SchoolResource;
use App\Models\School;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SchoolController extends Controller
{
    /**
     * List all schools.
     */
    public function index(): AnonymousResourceCollection
    {
        $schools = School::orderBy('name')->get();

        return SchoolResource::collection($schools);
    }

    /**
     * Get a single school with its levels.
     */
    public function show(School $school): SchoolResource
    {
        $school->load('levels');

        return new SchoolResource($school);
    }
}
