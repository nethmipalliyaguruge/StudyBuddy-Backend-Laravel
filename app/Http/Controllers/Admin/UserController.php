<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function updateRole(User $user)
    {
        // Prevent admin from demoting themselves accidentally
        if (auth()->id() === $user->id) {
            return back()->withErrors('You cannot change your own role.');
        }

        $user->update([
            'role' => $user->role === 'admin' ? 'student' : 'admin',
        ]);

        return back()->with('success', 'User role updated successfully');
    }

    public function block(User $user)
    {
        if ($user->role === 'admin') {
            return back()->withErrors('Admin users cannot be blocked.');
        }

        $user->update([
            'is_blocked' => true,
        ]);

        return back()->with('success', 'User blocked successfully');
    }

    public function unblock(User $user)
    {
        $user->update([
            'is_blocked' => false,
        ]);

        return back()->with('success', 'User unblocked successfully');
    }
}
