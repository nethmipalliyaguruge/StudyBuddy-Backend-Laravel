@props(['schools', 'levels' => collect(), 'modules' => collect(), 'selectedSchool' => null, 'selectedLevel' => null, 'selectedModule' => null, 'minPrice' => null, 'maxPrice' => null])

<aside {{ $attributes->merge(['class' => 'bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-6']) }}>
    <div class="flex items-center justify-between">
        <h3 class="font-semibold text-gray-900">
            <i class="fas fa-filter mr-2 text-primary"></i>Filters
        </h3>
        @if($selectedSchool || $selectedLevel || $selectedModule || request('min_price') || request('max_price'))
            <a href="{{ route('materials.index') }}" class="text-sm text-primary hover:underline">
                Clear all
            </a>
        @endif
    </div>

    <form method="GET" action="{{ route('materials.index') }}" id="filterForm">
        <!-- School Filter -->
        <div class="space-y-2">
            <label class="form-label">School</label>
            <select name="school" id="schoolFilter" class="select" onchange="this.form.submit()">
                <option value="">All Schools</option>
                @foreach($schools as $school)
                    <option value="{{ $school->id }}" {{ $selectedSchool == $school->id ? 'selected' : '' }}>
                        {{ $school->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Level Filter -->
        <div class="space-y-2 mt-4">
            <label class="form-label">Level</label>
            <select name="level" id="levelFilter" class="select" {{ $levels->isEmpty() ? 'disabled' : '' }} onchange="this.form.submit()">
                <option value="">All Levels</option>
                @foreach($levels as $level)
                    <option value="{{ $level->id }}" {{ $selectedLevel == $level->id ? 'selected' : '' }}>
                        {{ $level->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Module Filter -->
        <div class="space-y-2 mt-4">
            <label class="form-label">Module</label>
            <select name="module" id="moduleFilter" class="select" {{ $modules->isEmpty() ? 'disabled' : '' }} onchange="this.form.submit()">
                <option value="">All Modules</option>
                @foreach($modules as $module)
                    <option value="{{ $module->id }}" {{ $selectedModule == $module->id ? 'selected' : '' }}>
                        {{ $module->title }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Price Range Filter -->
        <div class="space-y-2 mt-4">
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
        <div class="space-y-2 mt-4">
            <label class="form-label">Search</label>
            <div class="relative">
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Search notes..."
                       class="input pl-10">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            </div>
        </div>

        <button type="submit" class="btn-primary w-full mt-6">
            <i class="fas fa-search mr-2"></i>Apply Filters
        </button>
    </form>
</aside>
