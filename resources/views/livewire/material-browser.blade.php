<div>
    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Sidebar Filters -->
        <div class="w-full lg:w-72 flex-shrink-0">
            <aside class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-6 sticky top-24">
                <div class="flex items-center justify-between">
                    <h3 class="font-semibold text-gray-900">
                        <i class="fas fa-filter mr-2 text-primary"></i>Filters
                    </h3>
                    @if($this->hasFilters)
                        <button wire:click="clearFilters" class="text-sm text-primary hover:underline">
                            Clear all
                        </button>
                    @endif
                </div>

                <!-- School Filter -->
                <div class="space-y-2">
                    <label class="form-label">School</label>
                    <select wire:model.live="school" class="select">
                        <option value="">All Schools</option>
                        @foreach($schools as $s)
                            <option value="{{ $s->id }}">{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Level Filter -->
                <div class="space-y-2">
                    <label class="form-label">Level</label>
                    <select wire:model.live="level" class="select" {{ $levels->isEmpty() ? 'disabled' : '' }}>
                        <option value="">All Levels</option>
                        @foreach($levels as $l)
                            <option value="{{ $l->id }}">{{ $l->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Module Filter -->
                <div class="space-y-2">
                    <label class="form-label">Module</label>
                    <select wire:model.live="module" class="select" {{ $modules->isEmpty() ? 'disabled' : '' }}>
                        <option value="">All Modules</option>
                        @foreach($modules as $m)
                            <option value="{{ $m->id }}">{{ $m->title }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Price Range Filter -->
                <div class="space-y-2">
                    <label class="form-label">Price Range (LKR)</label>
                    <div class="flex gap-2">
                        <input type="number"
                               wire:model.live.debounce.500ms="minPrice"
                               placeholder="Min"
                               min="0"
                               class="input flex-1">
                        <span class="self-center text-gray-400">-</span>
                        <input type="number"
                               wire:model.live.debounce.500ms="maxPrice"
                               placeholder="Max"
                               min="0"
                               class="input flex-1">
                    </div>
                </div>

                <!-- Search -->
                <div class="space-y-2">
                    <label class="form-label">Search</label>
                    <div class="relative">
                        <input type="text"
                               wire:model.live.debounce.300ms="search"
                               placeholder="Search notes..."
                               class="input pl-10">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    </div>
                </div>

                <!-- Loading Indicator -->
                <div wire:loading class="text-center text-sm text-gray-500">
                    <i class="fas fa-spinner fa-spin mr-1"></i> Loading...
                </div>
            </aside>
        </div>

        <!-- Main Content -->
        <div class="flex-grow">
            <!-- Results Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                    <p class="text-gray-600">
                        <span wire:loading.remove>
                            Showing <span class="font-semibold text-gray-900">{{ $notes->total() }}</span> results
                            @if($search)
                                for "<span class="font-semibold text-gray-900">{{ $search }}</span>"
                            @endif
                        </span>
                        <span wire:loading>
                            <i class="fas fa-spinner fa-spin mr-1"></i> Searching...
                        </span>
                    </p>

                    <!-- Sort Dropdown -->
                    <select wire:model.live="sort" class="select text-sm py-1.5">
                        <option value="newest">Newest First</option>
                        <option value="oldest">Oldest First</option>
                        <option value="price_high">Price: High to Low</option>
                        <option value="price_low">Price: Low to High</option>
                    </select>
                </div>

                @auth
                    <a href="{{ route('notes.create') }}" class="btn-primary btn-sm">
                        <i class="fas fa-plus mr-2"></i>Upload Note
                    </a>
                @endauth
            </div>

            <!-- Materials Grid -->
            <div wire:loading.class="opacity-50">
                @if($notes->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                        @foreach($notes as $note)
                            <x-material-card :note="$note" />
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    @if($notes->hasPages())
                        <div class="mt-8">
                            {{ $notes->links() }}
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
                            @if($this->hasFilters)
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
</div>
