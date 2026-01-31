<div class="card">
    <div class="p-4 border-b">
        <h3 class="font-semibold text-gray-900">Manage Modules</h3>
    </div>
    <div class="p-4">
        <!-- Add Module Form -->
        <form wire:submit="create" class="flex flex-wrap gap-2 mb-6">
            <select wire:model.live="selectedSchool" class="select w-auto">
                <option value="">Select School</option>
                @foreach($schools as $school)
                    <option value="{{ $school->id }}">{{ $school->name }}</option>
                @endforeach
            </select>
            <select wire:model="selectedLevel" class="select w-auto" {{ $levels->isEmpty() ? 'disabled' : '' }}>
                <option value="">Select Level</option>
                @foreach($levels as $level)
                    <option value="{{ $level->id }}">{{ $level->name }}</option>
                @endforeach
            </select>
            <input
                type="text"
                wire:model="title"
                placeholder="Enter module title"
                class="input flex-grow"
            >
            <button type="submit" class="btn-primary" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="create">
                    <i class="fas fa-plus mr-2"></i>Add Module
                </span>
                <span wire:loading wire:target="create">
                    <i class="fas fa-spinner fa-spin mr-2"></i>Adding...
                </span>
            </button>
        </form>

        @error('selectedLevel')
            <p class="text-red-500 text-sm mb-2">{{ $message }}</p>
        @enderror
        @error('title')
            <p class="text-red-500 text-sm mb-4">{{ $message }}</p>
        @enderror

        <!-- Modules List -->
        <div class="space-y-2">
            @forelse($modules as $module)
                <div wire:key="module-{{ $module->id }}" class="p-3 bg-gray-50 rounded-lg">
                    @if($editingId === $module->id)
                        <!-- Edit Mode -->
                        <form wire:submit="update" class="flex items-center gap-2">
                            <input
                                type="text"
                                wire:model="editingTitle"
                                class="input flex-grow"
                                autofocus
                            >
                            <button type="submit" class="btn-primary btn-sm" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="update">
                                    <i class="fas fa-check mr-1"></i>Save
                                </span>
                                <span wire:loading wire:target="update">
                                    <i class="fas fa-spinner fa-spin"></i>
                                </span>
                            </button>
                            <button type="button" wire:click="cancelEdit" class="btn-ghost btn-sm">
                                Cancel
                            </button>
                        </form>
                        @error('editingTitle')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    @else
                        <!-- Display Mode -->
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="font-medium text-gray-900">{{ $module->title }}</span>
                                <span class="text-sm text-gray-500 ml-2">
                                    ({{ $module->level->name ?? 'Unknown' }} &bull; {{ $module->level->school->name ?? 'Unknown' }})
                                </span>
                            </div>
                            <div class="flex items-center gap-3">
                                <button
                                    type="button"
                                    wire:click="startEdit({{ $module->id }})"
                                    class="text-blue-600 hover:text-blue-700 text-sm"
                                >
                                    <i class="fas fa-edit mr-1"></i>Edit
                                </button>

                                <!-- Status Toggle -->
                                <button
                                    type="button"
                                    wire:click="toggleStatus({{ $module->id }})"
                                    class="text-sm {{ $module->status ? 'text-green-600' : 'text-gray-400' }}"
                                    wire:loading.attr="disabled"
                                >
                                    <i class="fas {{ $module->status ? 'fa-toggle-on' : 'fa-toggle-off' }} mr-1"></i>
                                    {{ $module->status ? 'Active' : 'Inactive' }}
                                </button>

                                <button
                                    type="button"
                                    wire:click="delete({{ $module->id }})"
                                    wire:confirm="Are you sure you want to delete this module?"
                                    class="text-red-600 hover:text-red-700 text-sm"
                                >
                                    <i class="fas fa-trash mr-1"></i>Delete
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            @empty
                <p class="text-gray-500 text-center py-4">No modules found. Add one above.</p>
            @endforelse
        </div>
    </div>
</div>
