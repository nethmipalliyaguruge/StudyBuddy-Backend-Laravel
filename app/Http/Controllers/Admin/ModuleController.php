<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Module;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'level_id' => 'required|exists:levels,id',
            'title' => 'required|string|max:255',
        ]);

        Module::create([
            'level_id' => $request->level_id,
            'title' => $request->title,
            'status' => true,
        ]);

        return back()->with('success', 'Module added successfully');
    }

    public function update(Request $request, Module $module)
    {
        $request->validate([
            'level_id' => 'required|exists:levels,id',
            'title' => 'required|string|max:255',
            'status' => 'nullable|boolean',
        ]);

        $data = [
            'level_id' => $request->level_id,
            'title' => $request->title,
        ];

        // Only update status if admin explicitly sent it
        if ($request->has('status')) {
            $data['status'] = $request->boolean('status');
        }

        $module->update($data);

        return back()->with('success', 'Module updated successfully');
    }


    public function destroy(Module $module)
    {
        // Later: prevent delete if notes exist
        $module->delete();

        return back()->with('success', 'Module deleted successfully');
    }
}
