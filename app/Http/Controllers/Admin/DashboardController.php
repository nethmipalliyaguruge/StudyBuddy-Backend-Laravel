<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Note;
use App\Models\School;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'stats' => [
                'schools' => School::count(),
                'modules' => Module::count(),
                'users' => User::count(),
                'materials' => Note::count(),
            ],
        ]);
    }
}
