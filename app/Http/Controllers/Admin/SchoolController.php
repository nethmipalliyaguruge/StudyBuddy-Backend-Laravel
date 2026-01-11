<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\School;
use Illuminate\Http\Request;

class SchoolController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        School::create($request->only('name', 'description'));

        return back()->with('success', 'School added successfully');
    }

    public function update(Request $request, School $school)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $school->update($request->only('name', 'description'));

        return back()->with('success', 'School updated successfully');
    }

    public function destroy(School $school)
    {
        // later we’ll protect this with dependency checks
        $school->delete();

        return back()->with('success', 'School deleted successfully');
    }

}
