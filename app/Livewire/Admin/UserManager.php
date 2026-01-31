<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Component;

class UserManager extends Component
{
    public function toggleRole(int $id): void
    {
        $user = User::findOrFail($id);

        // Prevent admin from demoting themselves
        if (auth()->id() === $user->id) {
            $this->dispatch('notify', type: 'error', message: 'You cannot change your own role');
            return;
        }

        $user->update([
            'role' => $user->role === 'admin' ? 'student' : 'admin',
        ]);

        $this->dispatch('notify', type: 'success', message: 'User role updated successfully');
    }

    public function block(int $id): void
    {
        $user = User::findOrFail($id);

        if ($user->role === 'admin') {
            $this->dispatch('notify', type: 'error', message: 'Admin users cannot be blocked');
            return;
        }

        $user->update(['is_blocked' => true]);
        $this->dispatch('notify', type: 'success', message: 'User blocked successfully');
    }

    public function unblock(int $id): void
    {
        $user = User::findOrFail($id);
        $user->update(['is_blocked' => false]);
        $this->dispatch('notify', type: 'success', message: 'User unblocked successfully');
    }

    public function render()
    {
        return view('livewire.admin.user-manager', [
            'users' => User::latest()->get(),
        ]);
    }
}
