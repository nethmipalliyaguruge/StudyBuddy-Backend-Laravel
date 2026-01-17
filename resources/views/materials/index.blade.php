@php
    $isGuest = !auth()->check();
    $layout = $isGuest ? 'layouts.landing' : 'layouts.app';
@endphp

<x-dynamic-component :component="$layout">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="section-title">Study Materials</h1>
            <p class="section-subtitle">Browse and discover quality notes from fellow students</p>
        </div>

        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Mobile Filter Drawer -->
            <x-filter-drawer
                :schools="$schools ?? collect()"
                :levels="$levels ?? collect()"
                :modules="$modules ?? collect()"
                :selectedSchool="request('school')"
                :selectedLevel="request('level')"
                :selectedModule="request('module')" />

            <!-- Desktop Sidebar -->
            <div class="hidden lg:block w-72 flex-shrink-0">
                <x-filter-sidebar
                    :schools="$schools ?? collect()"
                    :levels="$levels ?? collect()"
                    :modules="$modules ?? collect()"
                    :selectedSchool="request('school')"
                    :selectedLevel="request('level')"
                    :selectedModule="request('module')"
                    class="sticky top-24" />
            </div>

            <!-- Main Content -->
            <div class="flex-grow">
                <!-- Results Header -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                    <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                        <p class="text-gray-600">
                            Showing <span class="font-semibold text-gray-900">{{ $notes->count() }}</span> results
                            @if(request('search'))
                                for "<span class="font-semibold text-gray-900">{{ request('search') }}</span>"
                            @endif
                        </p>

                        <!-- Sort Dropdown -->
                        <form method="GET" action="{{ route('materials.index') }}" class="flex items-center gap-2">
                            @foreach(request()->except('sort', 'page') as $key => $value)
                                @if($value)
                                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                @endif
                            @endforeach
                            <select name="sort" onchange="this.form.submit()" class="select text-sm py-1.5">
                                <option value="newest" {{ request('sort', 'newest') == 'newest' ? 'selected' : '' }}>Newest First</option>
                                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                                <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                                <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                            </select>
                        </form>
                    </div>

                    @auth
                        <a href="{{ route('notes.create') }}" class="btn-primary btn-sm">
                            <i class="fas fa-plus mr-2"></i>Upload Note
                        </a>
                    @endauth
                </div>

                <!-- Materials Grid -->
                @if($notes->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                        @foreach($notes as $note)
                            <x-material-card :note="$note" />
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    @if($notes->hasPages())
                        <div class="mt-8">
                            {{ $notes->withQueryString()->links() }}
                        </div>
                    @endif
                @else
                    <!-- Empty State -->
                    <div class="text-center py-16 bg-white rounded-xl border border-gray-100">
                        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-book-open text-3xl text-gray-400"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">No materials found</h3>
                        <p class="text-gray-600 mb-6">
                            @if(request()->hasAny(['school', 'level', 'module', 'search']))
                                Try adjusting your filters or search terms.
                            @else
                                Be the first to share study materials!
                            @endif
                        </p>
                        @auth
                            <a href="{{ route('notes.create') }}" class="btn-primary">
                                <i class="fas fa-upload mr-2"></i>Upload Your Notes
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="btn-primary">
                                <i class="fas fa-user-plus mr-2"></i>Join to Upload
                            </a>
                        @endauth
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-dynamic-component>
