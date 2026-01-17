<x-layouts.admin>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="section-title">Admin Dashboard</h1>
            <p class="section-subtitle">Manage schools, levels, modules, users, and materials</p>
        </div>

        <!-- Stats Overview -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <x-stats-card
                title="Schools"
                :value="$schools->count()"
                icon="fa-school"
                color="primary" />

            <x-stats-card
                title="Modules"
                :value="$modules->count()"
                icon="fa-book"
                color="info" />

            <x-stats-card
                title="Users"
                :value="$users->count()"
                icon="fa-users"
                color="success" />

            <x-stats-card
                title="Materials"
                :value="$notes->count()"
                icon="fa-file-alt"
                color="warning" />
        </div>

        <!-- Tabs Navigation -->
        <div x-data="{ activeTab: 'schools' }" class="space-y-6">
            <div class="flex flex-wrap gap-2 border-b pb-4">
                <button @click="activeTab = 'schools'"
                        :class="activeTab === 'schools' ? 'btn-primary' : 'btn-ghost'"
                        class="btn-sm">
                    <i class="fas fa-school mr-2"></i>Schools
                </button>
                <button @click="activeTab = 'levels'"
                        :class="activeTab === 'levels' ? 'btn-primary' : 'btn-ghost'"
                        class="btn-sm">
                    <i class="fas fa-layer-group mr-2"></i>Levels
                </button>
                <button @click="activeTab = 'modules'"
                        :class="activeTab === 'modules' ? 'btn-primary' : 'btn-ghost'"
                        class="btn-sm">
                    <i class="fas fa-book mr-2"></i>Modules
                </button>
                <button @click="activeTab = 'users'"
                        :class="activeTab === 'users' ? 'btn-primary' : 'btn-ghost'"
                        class="btn-sm">
                    <i class="fas fa-users mr-2"></i>Users
                </button>
                <button @click="activeTab = 'materials'"
                        :class="activeTab === 'materials' ? 'btn-primary' : 'btn-ghost'"
                        class="btn-sm">
                    <i class="fas fa-file-alt mr-2"></i>Materials
                </button>
            </div>

            <!-- Schools Tab -->
            <div x-show="activeTab === 'schools'" x-cloak>
                <div class="card">
                    <div class="p-4 border-b">
                        <h3 class="font-semibold text-gray-900">Manage Schools</h3>
                    </div>
                    <div class="p-4">
                        <!-- Add School Form -->
                        <form method="POST" action="{{ route('admin.schools.store') }}" class="flex gap-2 mb-6">
                            @csrf
                            <input type="text"
                                   name="name"
                                   placeholder="Enter school name"
                                   required
                                   class="input flex-grow">
                            <button type="submit" class="btn-primary">
                                <i class="fas fa-plus mr-2"></i>Add School
                            </button>
                        </form>

                        <!-- Schools List -->
                        <div class="space-y-2">
                            @foreach($schools as $school)
                                <div x-data="{ editing: false }" class="p-3 bg-gray-50 rounded-lg">
                                    <!-- Display Mode -->
                                    <div x-show="!editing" class="flex items-center justify-between">
                                        <div>
                                            <span class="font-medium text-gray-900">{{ $school->name }}</span>
                                            <span class="text-sm text-gray-500 ml-2">
                                                ({{ $school->levels->count() }} levels)
                                            </span>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <button @click="editing = true" type="button" class="text-blue-600 hover:text-blue-700 text-sm">
                                                <i class="fas fa-edit mr-1"></i>Edit
                                            </button>
                                            <form method="POST" action="{{ route('admin.schools.destroy', $school) }}"
                                                  onsubmit="return confirm('Delete this school?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-700 text-sm">
                                                    <i class="fas fa-trash mr-1"></i>Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>

                                    <!-- Edit Mode -->
                                    <form x-show="editing" x-cloak method="POST" action="{{ route('admin.schools.update', $school) }}" class="flex items-center gap-2">
                                        @csrf
                                        @method('PUT')
                                        <input type="text" name="name" value="{{ $school->name }}" required class="input flex-grow">
                                        <button type="submit" class="btn-primary btn-sm">
                                            <i class="fas fa-check mr-1"></i>Save
                                        </button>
                                        <button type="button" @click="editing = false" class="btn-ghost btn-sm">
                                            Cancel
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Levels Tab -->
            <div x-show="activeTab === 'levels'" x-cloak>
                <div class="card">
                    <div class="p-4 border-b">
                        <h3 class="font-semibold text-gray-900">Manage Levels</h3>
                    </div>
                    <div class="p-4">
                        <!-- Add Level Form -->
                        <form method="POST" action="{{ route('admin.levels.store') }}" class="flex flex-wrap gap-2 mb-6">
                            @csrf
                            <select name="school_id" required class="select w-auto">
                                <option value="">Select School</option>
                                @foreach($schools as $school)
                                    <option value="{{ $school->id }}">{{ $school->name }}</option>
                                @endforeach
                            </select>
                            <input type="text"
                                   name="name"
                                   placeholder="Enter level name"
                                   required
                                   class="input flex-grow">
                            <button type="submit" class="btn-primary">
                                <i class="fas fa-plus mr-2"></i>Add Level
                            </button>
                        </form>

                        <!-- Levels List -->
                        <div class="space-y-2">
                            @foreach($levels as $level)
                                <div x-data="{ editing: false }" class="p-3 bg-gray-50 rounded-lg">
                                    <!-- Display Mode -->
                                    <div x-show="!editing" class="flex items-center justify-between">
                                        <div>
                                            <span class="font-medium text-gray-900">{{ $level->name }}</span>
                                            <span class="text-sm text-gray-500 ml-2">
                                                ({{ $level->school->name ?? 'Unknown' }})
                                            </span>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <button @click="editing = true" type="button" class="text-blue-600 hover:text-blue-700 text-sm">
                                                <i class="fas fa-edit mr-1"></i>Edit
                                            </button>
                                            <form method="POST" action="{{ route('admin.levels.destroy', $level) }}"
                                                  onsubmit="return confirm('Delete this level?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-700 text-sm">
                                                    <i class="fas fa-trash mr-1"></i>Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>

                                    <!-- Edit Mode -->
                                    <form x-show="editing" x-cloak method="POST" action="{{ route('admin.levels.update', $level) }}" class="flex items-center gap-2">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="school_id" value="{{ $level->school_id }}">
                                        <input type="text" name="name" value="{{ $level->name }}" required class="input flex-grow">
                                        <button type="submit" class="btn-primary btn-sm">
                                            <i class="fas fa-check mr-1"></i>Save
                                        </button>
                                        <button type="button" @click="editing = false" class="btn-ghost btn-sm">
                                            Cancel
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modules Tab -->
            <div x-show="activeTab === 'modules'" x-cloak>
                <div class="card">
                    <div class="p-4 border-b">
                        <h3 class="font-semibold text-gray-900">Manage Modules</h3>
                    </div>
                    <div class="p-4">
                        <!-- Add Module Form -->
                        <form method="POST" action="{{ route('admin.modules.store') }}" class="flex flex-wrap gap-2 mb-6">
                            @csrf
                            <select name="level_id" required class="select w-auto">
                                <option value="">Select Level</option>
                                @foreach($levels as $level)
                                    <option value="{{ $level->id }}">
                                        {{ $level->name }} ({{ $level->school->name ?? '' }})
                                    </option>
                                @endforeach
                            </select>
                            <input type="text"
                                   name="title"
                                   placeholder="Enter module title"
                                   required
                                   class="input flex-grow">
                            <button type="submit" class="btn-primary">
                                <i class="fas fa-plus mr-2"></i>Add Module
                            </button>
                        </form>

                        <!-- Modules List -->
                        <div class="space-y-2">
                            @foreach($modules as $module)
                                <div x-data="{ editing: false }" class="p-3 bg-gray-50 rounded-lg">
                                    <!-- Display Mode -->
                                    <div x-show="!editing" class="flex items-center justify-between">
                                        <div>
                                            <span class="font-medium text-gray-900">{{ $module->title }}</span>
                                            <span class="text-sm text-gray-500 ml-2">
                                                ({{ $module->level->name ?? 'Unknown' }})
                                            </span>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <button @click="editing = true" type="button" class="text-blue-600 hover:text-blue-700 text-sm">
                                                <i class="fas fa-edit mr-1"></i>Edit
                                            </button>

                                            <!-- Status Toggle -->
                                            <form method="POST" action="{{ route('admin.modules.update', $module) }}">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="level_id" value="{{ $module->level_id }}">
                                                <input type="hidden" name="title" value="{{ $module->title }}">
                                                <input type="hidden" name="status" value="{{ $module->status ? '0' : '1' }}">
                                                <button type="submit"
                                                        class="text-sm {{ $module->status ? 'text-green-600' : 'text-gray-400' }}">
                                                    <i class="fas {{ $module->status ? 'fa-toggle-on' : 'fa-toggle-off' }} mr-1"></i>
                                                    {{ $module->status ? 'Active' : 'Inactive' }}
                                                </button>
                                            </form>

                                            <form method="POST" action="{{ route('admin.modules.destroy', $module) }}"
                                                  onsubmit="return confirm('Delete this module?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-700 text-sm">
                                                    <i class="fas fa-trash mr-1"></i>Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>

                                    <!-- Edit Mode -->
                                    <form x-show="editing" x-cloak method="POST" action="{{ route('admin.modules.update', $module) }}" class="flex items-center gap-2">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="level_id" value="{{ $module->level_id }}">
                                        <input type="hidden" name="status" value="{{ $module->status }}">
                                        <input type="text" name="title" value="{{ $module->title }}" required class="input flex-grow">
                                        <button type="submit" class="btn-primary btn-sm">
                                            <i class="fas fa-check mr-1"></i>Save
                                        </button>
                                        <button type="button" @click="editing = false" class="btn-ghost btn-sm">
                                            Cancel
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Users Tab -->
            <div x-show="activeTab === 'users'" x-cloak>
                <div class="card">
                    <div class="p-4 border-b">
                        <h3 class="font-semibold text-gray-900">Manage Users</h3>
                    </div>
                    <div class="p-4">
                        <div class="space-y-3">
                            @foreach($users as $user)
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between p-4 bg-gray-50 rounded-lg gap-4">
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
                                        <form method="POST" action="{{ route('admin.users.role', $user) }}">
                                            @csrf
                                            <button type="submit" class="btn-secondary btn-sm">
                                                <i class="fas fa-exchange-alt mr-1"></i>Toggle Role
                                            </button>
                                        </form>

                                        <!-- Block/Unblock -->
                                        @if($user->is_blocked)
                                            <form method="POST" action="{{ route('admin.users.unblock', $user) }}">
                                                @csrf
                                                <button type="submit" class="btn-sm bg-green-600 text-white hover:bg-green-700">
                                                    <i class="fas fa-unlock mr-1"></i>Unblock
                                                </button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('admin.users.block', $user) }}">
                                                @csrf
                                                <button type="submit" class="btn-danger btn-sm">
                                                    <i class="fas fa-ban mr-1"></i>Block
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Materials Tab -->
            <div x-show="activeTab === 'materials'" x-cloak>
                <div class="card">
                    <div class="p-4 border-b">
                        <h3 class="font-semibold text-gray-900">Manage Materials</h3>
                    </div>
                    <div class="p-4">
                        <div class="space-y-3">
                            @foreach($notes as $note)
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between p-4 bg-gray-50 rounded-lg gap-4">
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
                                        <!-- Approve -->
                                        @if($note->status !== 'approved')
                                            <form method="POST" action="{{ route('admin.materials.approve', $note) }}">
                                                @csrf
                                                <button type="submit" class="btn-sm bg-green-600 text-white hover:bg-green-700">
                                                    <i class="fas fa-check mr-1"></i>Approve
                                                </button>
                                            </form>
                                        @endif

                                        <!-- Set Pending -->
                                        @if($note->status === 'approved')
                                            <form method="POST" action="{{ route('admin.materials.pending', $note) }}">
                                                @csrf
                                                <button type="submit" class="btn-secondary btn-sm">
                                                    <i class="fas fa-clock mr-1"></i>Pending
                                                </button>
                                            </form>
                                        @endif

                                        <!-- Delete -->
                                        <form method="POST" action="{{ route('admin.materials.destroy', $note) }}"
                                              onsubmit="return confirm('Delete this material?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-danger btn-sm">
                                                <i class="fas fa-trash mr-1"></i>Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
