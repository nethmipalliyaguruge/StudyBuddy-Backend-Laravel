<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Welcome Section -->
        <div class="mb-8">
            <h1 class="section-title">Welcome back, {{ auth()->user()->name }}!</h1>
            <p class="section-subtitle">Here's an overview of your StudyBuddy activity</p>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <x-stats-card
                title="My Notes"
                :value="auth()->user()->notes()->count()"
                icon="fa-file-alt"
                color="primary" />

            <x-stats-card
                title="Approved Notes"
                :value="auth()->user()->notes()->where('status', 'approved')->count()"
                icon="fa-check-circle"
                color="success" />

            <x-stats-card
                title="My Purchases"
                :value="auth()->user()->purchases()->count()"
                icon="fa-shopping-bag"
                color="info" />

            <x-stats-card
                title="Total Earnings"
                :value="'LKR ' . number_format(auth()->user()->notes()->whereHas('purchases')->with('purchases')->get()->sum(fn($note) => $note->purchases->sum('price')), 2)"
                icon="fa-coins"
                color="warning" />
        </div>

        <!-- Quick Actions -->
        <div class="grid md:grid-cols-2 gap-6 mb-8">
            <!-- Upload Notes Card -->
            <div class="card p-6">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-upload text-xl text-primary"></i>
                    </div>
                    <div class="flex-grow">
                        <h3 class="font-semibold text-gray-900 mb-1">Upload New Notes</h3>
                        <p class="text-sm text-gray-600 mb-4">Share your knowledge and start earning from your notes.</p>
                        <a href="{{ route('notes.create') }}" class="btn-primary btn-sm">
                            <i class="fas fa-plus mr-2"></i>Upload Note
                        </a>
                    </div>
                </div>
            </div>

            <!-- Browse Materials Card -->
            <div class="card p-6">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-search text-xl text-blue-600"></i>
                    </div>
                    <div class="flex-grow">
                        <h3 class="font-semibold text-gray-900 mb-1">Browse Materials</h3>
                        <p class="text-sm text-gray-600 mb-4">Discover quality study materials from fellow students.</p>
                        <a href="{{ route('materials.index') }}" class="btn-secondary btn-sm">
                            <i class="fas fa-book mr-2"></i>Browse Now
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="grid lg:grid-cols-2 gap-6">
            <!-- My Recent Notes -->
            <div class="card">
                <div class="p-4 border-b flex items-center justify-between">
                    <h3 class="font-semibold text-gray-900">
                        <i class="fas fa-file-alt mr-2 text-primary"></i>My Recent Notes
                    </h3>
                    <a href="{{ route('notes.mine') }}" class="text-sm text-primary hover:underline">View all</a>
                </div>
                <div class="p-4">
                    @php
                        $recentNotes = auth()->user()->notes()->latest()->take(5)->get();
                    @endphp

                    @forelse($recentNotes as $note)
                        <div class="flex items-center justify-between py-3 {{ !$loop->last ? 'border-b' : '' }}">
                            <div class="flex-grow min-w-0">
                                <p class="font-medium text-gray-900 truncate">{{ $note->title }}</p>
                                <p class="text-sm text-gray-500">LKR {{ number_format($note->price, 2) }}</p>
                            </div>
                            <span class="badge-{{ $note->status === 'approved' ? 'success' : ($note->status === 'pending' ? 'warning' : 'danger') }} ml-2">
                                {{ ucfirst($note->status) }}
                            </span>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-file-alt text-3xl mb-2 opacity-50"></i>
                            <p>No notes yet. Start uploading!</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Recent Purchases -->
            <div class="card">
                <div class="p-4 border-b flex items-center justify-between">
                    <h3 class="font-semibold text-gray-900">
                        <i class="fas fa-shopping-bag mr-2 text-primary"></i>Recent Purchases
                    </h3>
                    <a href="{{ route('purchases.index') }}" class="text-sm text-primary hover:underline">View all</a>
                </div>
                <div class="p-4">
                    @php
                        $recentPurchases = auth()->user()->purchases()->with('note')->latest()->take(5)->get();
                    @endphp

                    @forelse($recentPurchases as $purchase)
                        <div class="flex items-center justify-between py-3 {{ !$loop->last ? 'border-b' : '' }}">
                            <div class="flex-grow min-w-0">
                                <p class="font-medium text-gray-900 truncate">{{ $purchase->note->title ?? 'Deleted Note' }}</p>
                                <p class="text-sm text-gray-500">{{ $purchase->created_at->diffForHumans() }}</p>
                            </div>
                            <span class="text-sm font-medium text-gray-900">LKR {{ number_format($purchase->price, 2) }}</span>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-shopping-bag text-3xl mb-2 opacity-50"></i>
                            <p>No purchases yet. Browse materials!</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
