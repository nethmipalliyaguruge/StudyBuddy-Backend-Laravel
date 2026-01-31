<div class="card">
    <div class="p-4 border-b">
        <h3 class="font-semibold text-gray-900">Manage Users</h3>
    </div>
    <div class="p-4">
        <div class="space-y-3">
            @forelse($users as $user)
                <div wire:key="user-{{ $user->id }}" class="flex flex-col sm:flex-row sm:items-center justify-between p-4 bg-gray-50 rounded-lg gap-4">
                    <div>
                        <div class="font-medium text-gray-900">{{ $user->name }}</div>
                        <div class="text-sm text-gray-500">{{ $user->email }}</div>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="badge-{{ $user->role === 'admin' ? 'primary' : 'info' }}">
                                {{ ucfirst($user->role) }}
                            </span>
                            @if($user->is_blocked)
                                <span class="badge-danger">Blocked</span>
                            @else
                                <span class="badge-success">Active</span>
                            @endif
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <!-- Toggle Role -->
                        <button
                            type="button"
                            wire:click="toggleRole({{ $user->id }})"
                            wire:loading.attr="disabled"
                            class="btn-secondary btn-sm"
                        >
                            <span wire:loading.remove wire:target="toggleRole({{ $user->id }})">
                                <i class="fas fa-exchange-alt mr-1"></i>Toggle Role
                            </span>
                            <span wire:loading wire:target="toggleRole({{ $user->id }})">
                                <i class="fas fa-spinner fa-spin mr-1"></i>...
                            </span>
                        </button>

                        <!-- Block/Unblock -->
                        @if($user->is_blocked)
                            <button
                                type="button"
                                wire:click="unblock({{ $user->id }})"
                                wire:loading.attr="disabled"
                                class="btn-sm bg-green-600 text-white hover:bg-green-700"
                            >
                                <span wire:loading.remove wire:target="unblock({{ $user->id }})">
                                    <i class="fas fa-unlock mr-1"></i>Unblock
                                </span>
                                <span wire:loading wire:target="unblock({{ $user->id }})">
                                    <i class="fas fa-spinner fa-spin mr-1"></i>...
                                </span>
                            </button>
                        @else
                            <button
                                type="button"
                                wire:click="block({{ $user->id }})"
                                wire:confirm="Are you sure you want to block this user?"
                                wire:loading.attr="disabled"
                                class="btn-danger btn-sm"
                            >
                                <span wire:loading.remove wire:target="block({{ $user->id }})">
                                    <i class="fas fa-ban mr-1"></i>Block
                                </span>
                                <span wire:loading wire:target="block({{ $user->id }})">
                                    <i class="fas fa-spinner fa-spin mr-1"></i>...
                                </span>
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <p class="text-gray-500 text-center py-4">No users found.</p>
            @endforelse
        </div>
    </div>
</div>
