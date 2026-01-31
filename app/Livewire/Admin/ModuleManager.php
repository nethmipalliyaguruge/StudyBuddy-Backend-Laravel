<?php

namespace App\Livewire\Admin;

use App\Models\Level;
use App\Models\Module;
use App\Models\School;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Livewire\Component;

class ModuleManager extends Component
{
    // Form fields for create
    public string $selectedSchool = '';
    public string $selectedLevel = '';
    public string $title = '';

    // Cascading dropdown data
    public Collection $levels;

    // Edit state
    public ?int $editingId = null;
    public string $editingTitle = '';

    protected function rules(): array
    {
        return [
            'selectedLevel' => 'required|exists:levels,id',
            'title' => 'required|string|max:255',
        ];
    }

    protected function editRules(): array
    {
        return [
            'editingTitle' => 'required|string|max:255',
        ];
    }

    public function mount(): void
    {
        $this->levels = collect();
    }

    #[On('schools-updated')]
    #[On('levels-updated')]
    public function refreshData(): void
    {
        // Reset selection if the selected items no longer exist
        if ($this->selectedSchool && !School::find($this->selectedSchool)) {
            $this->selectedSchool = '';
            $this->selectedLevel = '';
            $this->levels = collect();
        }

        if ($this->selectedLevel && !Level::find($this->selectedLevel)) {
            $this->selectedLevel = '';
        }

        // Refresh levels for current school
        if ($this->selectedSchool) {
            $this->levels = Level::where('school_id', $this->selectedSchool)->get();
        }
    }

    public function updatedSelectedSchool($value): void
    {
        $this->selectedLevel = '';
        $this->levels = collect();

        if ($value) {
            $this->levels = Level::where('school_id', $value)->get();
        }
    }

    public function create(): void
    {
        $this->validate();

        Module::create([
            'level_id' => $this->selectedLevel,
            'title' => $this->title,
            'status' => true,
        ]);

        $this->reset('title');
        $this->dispatch('notify', type: 'success', message: 'Module created successfully');
    }

    public function startEdit(int $id): void
    {
        $module = Module::findOrFail($id);
        $this->editingId = $id;
        $this->editingTitle = $module->title;
    }

    public function cancelEdit(): void
    {
        $this->reset('editingId', 'editingTitle');
    }

    public function update(): void
    {
        $this->validate($this->editRules());

        $module = Module::findOrFail($this->editingId);
        $module->update(['title' => $this->editingTitle]);

        $this->reset('editingId', 'editingTitle');
        $this->dispatch('notify', type: 'success', message: 'Module updated successfully');
    }

    public function toggleStatus(int $id): void
    {
        $module = Module::findOrFail($id);
        $module->update(['status' => !$module->status]);

        $status = $module->status ? 'activated' : 'deactivated';
        $this->dispatch('notify', type: 'success', message: "Module {$status} successfully");
    }

    public function delete(int $id): void
    {
        $module = Module::findOrFail($id);

        // Check for dependent notes
        if ($module->notes()->exists()) {
            $this->dispatch('notify', type: 'error', message: 'Cannot delete module with existing materials');
            return;
        }

        $module->delete();
        $this->dispatch('notify', type: 'success', message: 'Module deleted successfully');
    }

    public function render()
    {
        return view('livewire.admin.module-manager', [
            'schools' => School::orderBy('name')->get(),
            'modules' => Module::with('level.school')->orderBy('title')->get(),
        ]);
    }
}
