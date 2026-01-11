<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            Admin Dashboard
        </h2>
    </x-slot>

    <div class="p-6 space-y-12">

        {{-- SUCCESS MESSAGE --}}
        @if(session('success'))
            <div class="bg-green-100 border border-green-300 text-green-800 p-3 rounded">
                {{ session('success') }}
            </div>
        @endif

        {{-- ========================================================= --}}
        {{-- SCHOOLS --}}
        {{-- ========================================================= --}}
        <section>
            <h3 class="text-lg font-bold mb-3">Schools</h3>

            <form method="POST" action="{{ route('admin.schools.store') }}" class="flex gap-2 mb-4">
                @csrf
                <input type="text" name="name" placeholder="School name" required class="border p-2 rounded">
                <button class="bg-blue-600 text-white px-4 rounded">Add</button>
            </form>

            <ul class="space-y-2">
                @foreach($schools as $school)
                    <li class="flex justify-between items-center border p-2 rounded">
                        {{ $school->name }}

                        <form method="POST" action="{{ route('admin.schools.destroy', $school) }}">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-600">Delete</button>
                        </form>
                    </li>
                @endforeach
            </ul>
        </section>

        {{-- ========================================================= --}}
        {{-- LEVELS --}}
        {{-- ========================================================= --}}
        <section>
            <h3 class="text-lg font-bold mb-3">Levels</h3>

            <form method="POST" action="{{ route('admin.levels.store') }}" class="flex gap-2 mb-4">
                @csrf
                <select name="school_id" required class="border p-2 rounded">
                    @foreach($schools as $school)
                        <option value="{{ $school->id }}">{{ $school->name }}</option>
                    @endforeach
                </select>

                <input type="text" name="name" placeholder="Level name" required class="border p-2 rounded">

                <button class="bg-blue-600 text-white px-4 rounded">Add</button>
            </form>

            <ul class="space-y-2">
                @foreach($levels as $level)
                    <li class="border p-2 rounded">
                        {{ $level->name }}
                        <span class="text-sm text-gray-500">(School ID: {{ $level->school_id }})</span>
                    </li>
                @endforeach
            </ul>
        </section>

        {{-- ========================================================= --}}
        {{-- MODULES --}}
        {{-- ========================================================= --}}
        <section>
            <h3 class="text-lg font-bold mb-3">Modules</h3>

            <form method="POST" action="{{ route('admin.modules.store') }}" class="flex gap-2 mb-4">
                @csrf

                <select name="level_id" required class="border p-2 rounded">
                    @foreach($levels as $level)
                        <option value="{{ $level->id }}">{{ $level->name }}</option>
                    @endforeach
                </select>

                <input type="text" name="title" placeholder="Module title" required class="border p-2 rounded">

                <button class="bg-blue-600 text-white px-4 rounded">Add</button>
            </form>

            <ul class="space-y-2">
                @foreach($modules as $module)
                    <li class="flex justify-between items-center border p-2 rounded">

                        <div>
                            <strong>{{ $module->title }}</strong>
                            <span class="text-sm text-gray-500">
                                (Level ID: {{ $module->level_id }})
                            </span>
                        </div>

                        <div class="flex items-center gap-4">

                            {{-- STATUS TOGGLE --}}
                            <form method="POST" action="{{ route('admin.modules.update', $module) }}">
                                @csrf
                                @method('PUT')

                                <input type="hidden" name="level_id" value="{{ $module->level_id }}">
                                <input type="hidden" name="title" value="{{ $module->title }}">

                                <input type="hidden" name="status" value="0">
                                <label class="flex items-center gap-1">
                                    <input type="checkbox" name="status" value="1"
                                           {{ $module->status ? 'checked' : '' }}
                                           onchange="this.form.submit()">
                                    <span class="text-sm">Active</span>
                                </label>
                            </form>

                            {{-- DELETE --}}
                            <form method="POST" action="{{ route('admin.modules.destroy', $module) }}">
                                @csrf
                                @method('DELETE')
                                <button class="text-red-600">Delete</button>
                            </form>

                        </div>
                    </li>
                @endforeach
            </ul>
        </section>

        {{-- ========================================================= --}}
        {{-- USER MANAGEMENT --}}
        {{-- ========================================================= --}}
        <section>
            <h3 class="text-lg font-bold mb-3">Users</h3>

            <ul class="space-y-3">
                @foreach($users as $user)
                    <li class="border p-3 rounded flex justify-between items-center">

                        <div>
                            <strong>{{ $user->name }}</strong>
                            <div class="text-sm text-gray-600">
                                {{ $user->email }} |
                                Role: <span class="font-semibold">{{ $user->role }}</span> |
                                Status:
                                @if($user->is_blocked)
                                    <span class="text-red-600 font-semibold">Blocked</span>
                                @else
                                    <span class="text-green-600 font-semibold">Active</span>
                                @endif
                            </div>
                        </div>

                        <div class="flex gap-2">

                            {{-- CHANGE ROLE --}}
                            <form method="POST"
                                  action="{{ route('admin.users.role', $user) }}">
                                @csrf
                                <button class="bg-indigo-600 text-white px-3 py-1 rounded">
                                    Toggle Role
                                </button>
                            </form>

                            {{-- BLOCK / UNBLOCK --}}
                            @if($user->is_blocked)
                                <form method="POST"
                                      action="{{ route('admin.users.unblock', $user) }}">
                                    @csrf
                                    <button class="bg-green-600 text-white px-3 py-1 rounded">
                                        Unblock
                                    </button>
                                </form>
                            @else
                                <form method="POST"
                                      action="{{ route('admin.users.block', $user) }}">
                                    @csrf
                                    <button class="bg-red-600 text-white px-3 py-1 rounded">
                                        Block
                                    </button>
                                </form>
                            @endif

                        </div>
                    </li>
                @endforeach
            </ul>
        </section>

        {{-- ========================================================= --}}
        {{-- MATERIALS / NOTES --}}
        {{-- ========================================================= --}}
        <section>
            <h3 class="text-lg font-bold mb-3">Materials</h3>

            <ul class="space-y-3">
                @foreach($notes as $note)
                    <li class="border p-3 rounded flex justify-between items-center">

                        <div>
                            <strong>{{ $note->title }}</strong>
                            <div class="text-sm text-gray-600">
                                Module ID: {{ $note->module_id }} |
                                Status: <span class="font-semibold">{{ $note->status }}</span>
                            </div>
                        </div>

                        <div class="flex gap-2">

                            {{-- APPROVE --}}
                            @if($note->status !== 'approved')
                                <form method="POST"
                                      action="{{ route('admin.materials.approve', $note) }}">
                                    @csrf
                                    <button class="bg-green-600 text-white px-3 py-1 rounded">
                                        Approve
                                    </button>
                                </form>
                            @endif

                            {{-- PENDING --}}
                            @if($note->status === 'approved')
                                <form method="POST"
                                      action="{{ route('admin.materials.pending', $note) }}">
                                    @csrf
                                    <button class="bg-yellow-500 text-white px-3 py-1 rounded">
                                        Pending
                                    </button>
                                </form>
                            @endif

                            {{-- DELETE --}}
                            <form method="POST"
                                  action="{{ route('admin.materials.destroy', $note) }}">
                                @csrf
                                @method('DELETE')
                                <button class="bg-red-600 text-white px-3 py-1 rounded">
                                    Delete
                                </button>
                            </form>

                        </div>
                    </li>
                @endforeach
            </ul>
        </section>


    </div>
</x-app-layout>
