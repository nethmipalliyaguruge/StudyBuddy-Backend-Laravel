<div class="card">
    <div class="p-4 border-b">
        <h3 class="font-semibold text-gray-900">Manage Schools</h3>
    </div>
    <div class="p-4">
        <!-- Add School Form -->
        <form wire:submit="create" class="flex gap-2 mb-6">
            <input
                type="text"
                wire:model="name"
                placeholder="Enter school name"
                class="input flex-grow"
            >
            <button type="submit" class="btn-primary" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="create">
                    <i class="fas fa-plus mr-2"></i>Add School
                </span>
                <span wire:loading wire:target="create">
                    <i class="fas fa-spinner fa-spin mr-2"></i>Adding...
                </span>
            </button>
        </form>

        @error('name')
            <p class="text-red-500 text-sm mb-4">{{ $message }}</p>
        @enderror

        <!-- Schools List -->
        <div class="space-y-2">
            @forelse($schools as $school)
                <div wire:key="school-{{ $school->id }}" class="p-3 bg-gray-50 rounded-lg">
                    @if($editingId === $school->id)
                        <!-- Edit Mode -->
                        <form wire:submit="update" class="flex items-center gap-2">
                            <input
                                type="text"
                                wire:model="editingName"
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
                        @error('editingName')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    @else
                        <!-- Display Mode -->
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="font-medium text-gray-900">{{ $school->name }}</span>
                                <span class="text-sm text-gray-500 ml-2">
                                    ({{ $school->levels_count }} levels)
                                </span>
                            </div>
                            <div class="flex items-center gap-3">
                                <button
                                    type="button"
                                    wire:click="startEdit({{ $school->id }})"
                                    class="text-blue-600 hover:text-blue-700 text-sm"
                                >
                                    <i class="fas fa-edit mr-1"></i>Edit
                                </button>
                                <button
                                    type="button"
                                    wire:click="delete({{ $school->id }})"
                                    wire:confirm="Are you sure you want to delete this school?"
                                    class="text-red-600 hover:text-red-700 text-sm"
                                >
                                    <i class="fas fa-trash mr-1"></i>Delete
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            @empty
                <p class="text-gray-500 text-center py-4">No schools found. Add one above.</p>
            @endforelse
        </div>
    </div>
</div>
