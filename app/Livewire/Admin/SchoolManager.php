<?php

namespace App\Livewire\Admin;

use App\Models\School;
use Livewire\Component;

class SchoolManager extends Component
{
    public string $name = '';
    public ?int $editingId = null;
    public string $editingName = '';

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
        ];
    }

    protected function editRules(): array
    {
        return [
            'editingName' => 'required|string|max:255',
        ];
    }

    public function create(): void
    {
        $this->validate();

        School::create(['name' => $this->name]);

        $this->reset('name');
        $this->dispatch('notify', type: 'success', message: 'School created successfully');
        $this->dispatch('schools-updated');
    }

    public function startEdit(int $id): void
    {
        $school = School::findOrFail($id);
        $this->editingId = $id;
        $this->editingName = $school->name;
    }

    public function cancelEdit(): void
    {
        $this->reset('editingId', 'editingName');
    }

    public function update(): void
    {
        $this->validate($this->editRules());

        $school = School::findOrFail($this->editingId);
        $school->update(['name' => $this->editingName]);

        $this->reset('editingId', 'editingName');
        $this->dispatch('notify', type: 'success', message: 'School updated successfully');
        $this->dispatch('schools-updated');
    }

    public function delete(int $id): void
    {
        $school = School::findOrFail($id);

        // Check for dependent levels
        if ($school->levels()->exists()) {
            $this->dispatch('notify', type: 'error', message: 'Cannot delete school with existing levels');
            return;
        }

        $school->delete();
        $this->dispatch('notify', type: 'success', message: 'School deleted successfully');
        $this->dispatch('schools-updated');
    }

    public function render()
    {
        return view('livewire.admin.school-manager', [
            'schools' => School::withCount('levels')->orderBy('name')->get(),
        ]);
    }
}
