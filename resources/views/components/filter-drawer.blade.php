@props(['schools', 'levels' => collect(), 'modules' => collect(), 'selectedSchool' => null, 'selectedLevel' => null, 'selectedModule' => null, 'minPrice' => null, 'maxPrice' => null])

<!-- Mobile Filter Drawer -->
<div x-data="{ open: false }" class="lg:hidden">
    <!-- Toggle Button -->
    <button @click="open = true"
            class="btn-outline w-full mb-4">
        <i class="fas fa-filter mr-2"></i>Filters
        @if($selectedSchool || $selectedLevel || $selectedModule || request('min_price') || request('max_price'))
            <span class="ml-2 badge-primary">Active</span>
        @endif
    </button>

    <!-- Drawer Backdrop -->
    <div x-show="open"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="open = false"
         class="fixed inset-0 bg-black/50 z-40"
         x-cloak>
    </div>

    <!-- Drawer Panel -->
    <div x-show="open"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="translate-x-full"
         class="fixed right-0 top-0 h-full w-80 bg-white shadow-xl z-50 overflow-y-auto"
         x-cloak>

        <!-- Header -->
        <div class="flex items-center justify-between p-4 border-b">
            <h3 class="font-semibold text-gray-900">
                <i class="fas fa-filter mr-2 text-primary"></i>Filters
            </h3>
            <button @click="open = false" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Filter Form -->
        <form method="GET" action="{{ route('materials.index') }}" class="p-4 space-y-4">
            <!-- School Filter -->
            <div class="space-y-2">
                <label class="form-label">School</label>
                <select name="school" class="select">
                    <option value="">All Schools</option>
                    @foreach($schools as $school)
                        <option value="{{ $school->id }}" {{ $selectedSchool == $school->id ? 'selected' : '' }}>
                            {{ $school->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Level Filter -->
            <div class="space-y-2">
                <label class="form-label">Level</label>
                <select name="level" class="select" {{ $levels->isEmpty() ? 'disabled' : '' }}>
                    <option value="">All Levels</option>
                    @foreach($levels as $level)
                        <option value="{{ $level->id }}" {{ $selectedLevel == $level->id ? 'selected' : '' }}>
                            {{ $level->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Module Filter -->
            <div class="space-y-2">
                <label class="form-label">Module</label>
                <select name="module" class="select" {{ $modules->isEmpty() ? 'disabled' : '' }}>
                    <option value="">All Modules</option>
                    @foreach($modules as $module)
                        <option value="{{ $module->id }}" {{ $selectedModule == $module->id ? 'selected' : '' }}>
                            {{ $module->title }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Price Range Filter -->
            <div class="space-y-2">
                <label class="form-label">Price Range (LKR)</label>
                <div class="flex gap-2">
                    <input type="number"
                           name="min_price"
                           value="{{ request('min_price') }}"
                           placeholder="Min"
                           min="0"
                           step="0.01"
                           class="input flex-1">
                    <span class="self-center text-gray-400">-</span>
                    <input type="number"
                           name="max_price"
                           value="{{ request('max_price') }}"
                           placeholder="Max"
                           min="0"
                           step="0.01"
                           class="input flex-1">
                </div>
            </div>

            <!-- Search -->
            <div class="space-y-2">
                <label class="form-label">Search</label>
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Search notes..."
                       class="input">
            </div>

            <!-- Actions -->
            <div class="flex gap-2 pt-4">
                @if($selectedSchool || $selectedLevel || $selectedModule || request('search') || request('min_price') || request('max_price'))
                    <a href="{{ route('materials.index') }}" class="btn-ghost flex-1">
                        Clear
                    </a>
                @endif
                <button type="submit" class="btn-primary flex-1">
                    Apply
                </button>
            </div>
        </form>
    </div>
</div>
