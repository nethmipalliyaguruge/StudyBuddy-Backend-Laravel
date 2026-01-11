<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Level;
use App\Models\Module;
use App\Models\Note;
use App\Models\School;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'schools' => School::with('levels.modules')->get(),
            'levels' => Level::all(),
            'modules' => Module::all(),
            'notes'   => Note::with('module')->latest()->get(),
            'users'   => User::latest()->get(),
        ]);
    }
}
