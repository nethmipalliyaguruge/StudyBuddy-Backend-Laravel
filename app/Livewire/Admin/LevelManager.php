<?php

namespace App\Livewire\Admin;

use App\Models\Level;
use App\Models\School;
use Livewire\Attributes\On;
use Livewire\Component;

class LevelManager extends Component
{
    public string $schoolId = '';
    public string $name = '';
    public ?int $editingId = null;
    public string $editingName = '';

    protected function rules(): array
    {
        return [
            'schoolId' => 'required|exists:schools,id',
            'name' => 'required|string|max:255',
        ];
    }

    protected function editRules(): array
    {
        return [
            'editingName' => 'required|string|max:255',
        ];
    }

    #[On('schools-updated')]
    public function refreshSchools(): void
    {
        // This will trigger a re-render with updated schools
    }

    public function create(): void
    {
        $this->validate();

        Level::create([
            'school_id' => $this->schoolId,
            'name' => $this->name,
        ]);

        $this->reset('name');
        $this->dispatch('notify', type: 'success', message: 'Level created successfully');
        $this->dispatch('levels-updated');
    }

    public function startEdit(int $id): void
    {
        $level = Level::findOrFail($id);
        $this->editingId = $id;
        $this->editingName = $level->name;
    }

    public function cancelEdit(): void
    {
        $this->reset('editingId', 'editingName');
    }

    public function update(): void
    {
        $this->validate($this->editRules());

        $level = Level::findOrFail($this->editingId);
        $level->update(['name' => $this->editingName]);

        $this->reset('editingId', 'editingName');
        $this->dispatch('notify', type: 'success', message: 'Level updated successfully');
        $this->dispatch('levels-updated');
    }

    public function delete(int $id): void
    {
        $level = Level::findOrFail($id);

        // Check for dependent modules
        if ($level->modules()->exists()) {
            $this->dispatch('notify', type: 'error', message: 'Cannot delete level with existing modules');
            return;
        }

        $level->delete();
        $this->dispatch('notify', type: 'success', message: 'Level deleted successfully');
        $this->dispatch('levels-updated');
    }

    public function render()
    {
        return view('livewire.admin.level-manager', [
            'schools' => School::orderBy('name')->get(),
            'levels' => Level::with('school')->withCount('modules')->orderBy('name')->get(),
        ]);
    }
}
