<?php

namespace App\Livewire\Admin;

use App\Models\Note;
use Livewire\Component;

class MaterialManager extends Component
{
    public function approve(int $id): void
    {
        $note = Note::findOrFail($id);
        $note->update(['status' => 'approved']);
        $this->dispatch('notify', type: 'success', message: 'Material approved successfully');
    }

    public function pending(int $id): void
    {
        $note = Note::findOrFail($id);
        $note->update(['status' => 'pending']);
        $this->dispatch('notify', type: 'success', message: 'Material set to pending');
    }

    public function enable(int $id): void
    {
        $note = Note::findOrFail($id);
        $note->update(['status' => 'approved']);
        $this->dispatch('notify', type: 'success', message: 'Material enabled successfully');
    }

    public function delete(int $id): void
    {
        $note = Note::findOrFail($id);

        if ($note->hasPurchases()) {
            $note->update(['status' => 'disabled']);
            $this->dispatch('notify', type: 'success', message: 'Material disabled (has purchases)');
            return;
        }

        $note->delete();
        $this->dispatch('notify', type: 'success', message: 'Material deleted successfully');
    }

    public function render()
    {
        return view('livewire.admin.material-manager', [
            'notes' => Note::with(['user', 'module'])->latest()->get(),
        ]);
    }
}
