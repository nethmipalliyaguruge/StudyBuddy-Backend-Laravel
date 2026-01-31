<div class="card">
    <div class="p-4 border-b">
        <h3 class="font-semibold text-gray-900">Manage Materials</h3>
    </div>
    <div class="p-4">
        <div class="space-y-3">
            @forelse($notes as $note)
                <div wire:key="note-{{ $note->id }}" class="flex flex-col sm:flex-row sm:items-center justify-between p-4 bg-gray-50 rounded-lg gap-4">
                    <div>
                        <div class="font-medium text-gray-900">{{ $note->title }}</div>
                        <div class="text-sm text-gray-500">
                            by {{ $note->user->name ?? 'Unknown' }}
                            &bull; {{ $note->module->title ?? 'No module' }}
                        </div>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="badge-{{ $note->status === 'approved' ? 'success' : ($note->status === 'pending' ? 'warning' : 'danger') }}">
                                {{ ucfirst($note->status) }}
                            </span>
                            <span class="text-sm text-gray-500">
                                LKR {{ number_format($note->price, 2) }}
                            </span>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <!-- Enable (for disabled materials) -->
                        @if($note->status === 'disabled')
                            <button
                                type="button"
                                wire:click="enable({{ $note->id }})"
                                wire:loading.attr="disabled"
                                class="btn-sm bg-green-600 text-white hover:bg-green-700"
                            >
                                <span wire:loading.remove wire:target="enable({{ $note->id }})">
                                    <i class="fas fa-check mr-1"></i>Enable
                                </span>
                                <span wire:loading wire:target="enable({{ $note->id }})">
                                    <i class="fas fa-spinner fa-spin mr-1"></i>...
                                </span>
                            </button>
                        @endif

                        <!-- Approve (for pending materials) -->
                        @if($note->status === 'pending')
                            <button
                                type="button"
                                wire:click="approve({{ $note->id }})"
                                wire:loading.attr="disabled"
                                class="btn-sm bg-green-600 text-white hover:bg-green-700"
                            >
                                <span wire:loading.remove wire:target="approve({{ $note->id }})">
                                    <i class="fas fa-check mr-1"></i>Approve
                                </span>
                                <span wire:loading wire:target="approve({{ $note->id }})">
                                    <i class="fas fa-spinner fa-spin mr-1"></i>...
                                </span>
                            </button>
                        @endif

                        <!-- Set Pending -->
                        @if($note->status === 'approved')
                            <button
                                type="button"
                                wire:click="pending({{ $note->id }})"
                                wire:loading.attr="disabled"
                                class="btn-secondary btn-sm"
                            >
                                <span wire:loading.remove wire:target="pending({{ $note->id }})">
                                    <i class="fas fa-clock mr-1"></i>Pending
                                </span>
                                <span wire:loading wire:target="pending({{ $note->id }})">
                                    <i class="fas fa-spinner fa-spin mr-1"></i>...
                                </span>
                            </button>
                        @endif

                        <!-- Delete -->
                        <button
                            type="button"
                            wire:click="delete({{ $note->id }})"
                            wire:confirm="Are you sure you want to delete this material?"
                            wire:loading.attr="disabled"
                            class="btn-danger btn-sm"
                        >
                            <span wire:loading.remove wire:target="delete({{ $note->id }})">
                                <i class="fas fa-trash mr-1"></i>Delete
                            </span>
                            <span wire:loading wire:target="delete({{ $note->id }})">
                                <i class="fas fa-spinner fa-spin mr-1"></i>...
                            </span>
                        </button>
                    </div>
                </div>
            @empty
                <p class="text-gray-500 text-center py-4">No materials found.</p>
            @endforelse
        </div>
    </div>
</div>
