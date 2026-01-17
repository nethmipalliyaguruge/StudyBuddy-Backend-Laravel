<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="section-title">My Purchases</h1>
            <p class="section-subtitle">Access all your purchased study materials</p>
        </div>

        <!-- Purchases Grid -->
        @forelse($purchases as $purchase)
            <div class="card mb-4">
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row gap-6">
                        <!-- Preview -->
                        <div class="sm:w-32 flex-shrink-0">
                            @if($purchase->note && $purchase->note->getMedia('previews')->first())
                                <img src="{{ $purchase->note->getMedia('previews')->first()->getUrl() }}"
                                     alt="{{ $purchase->note->title }}"
                                     class="w-full h-24 object-cover rounded-lg">
                            @else
                                <div class="w-full h-24 bg-gray-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-file-alt text-2xl text-gray-300"></i>
                                </div>
                            @endif
                        </div>

                        <!-- Details -->
                        <div class="flex-grow">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <h3 class="font-semibold text-gray-900">
                                        {{ $purchase->note->title ?? 'Note no longer available' }}
                                    </h3>
                                    @if($purchase->note)
                                        <p class="text-sm text-gray-500 mt-1">
                                            by {{ $purchase->note->user->name ?? 'Unknown' }}
                                            @if($purchase->note->module)
                                                &bull; {{ $purchase->note->module->title }}
                                            @endif
                                        </p>
                                    @endif
                                </div>
                                <span class="badge-success flex-shrink-0">
                                    <i class="fas fa-check-circle mr-1"></i>Purchased
                                </span>
                            </div>

                            <!-- Meta -->
                            <div class="flex flex-wrap items-center gap-4 mt-3 text-sm text-gray-500">
                                <span><i class="fas fa-tag mr-1"></i>LKR {{ number_format($purchase->price, 2) }}</span>
                                <span><i class="fas fa-calendar mr-1"></i>{{ $purchase->created_at->format('M d, Y') }}</span>
                            </div>

                            <!-- Actions -->
                            <div class="mt-4 flex flex-wrap gap-3">
                                @if($purchase->note)
                                    @php
                                        $noteFile = $purchase->note->getFirstMedia('note_file');
                                    @endphp
                                    @if($noteFile)
                                        <a href="{{ $noteFile->getUrl() }}"
                                           target="_blank"
                                           class="btn-primary btn-sm">
                                            <i class="fas fa-download mr-2"></i>Download Note
                                        </a>
                                    @endif

                                    <a href="{{ route('materials.show', $purchase->note) }}"
                                       class="btn-secondary btn-sm">
                                        <i class="fas fa-eye mr-2"></i>View Details
                                    </a>
                                @else
                                    <span class="text-sm text-gray-500 italic">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                        This note is no longer available for download
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <!-- Empty State -->
            <div class="card p-12 text-center">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-shopping-bag text-3xl text-gray-400"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">No purchases yet</h3>
                <p class="text-gray-600 mb-6">Browse our study materials and find notes that help you learn!</p>
                <a href="{{ route('materials.index') }}" class="btn-primary">
                    <i class="fas fa-book mr-2"></i>Browse Materials
                </a>
            </div>
        @endforelse

        <!-- Pagination -->
        @if($purchases instanceof \Illuminate\Pagination\LengthAwarePaginator && $purchases->hasPages())
            <div class="mt-6">
                {{ $purchases->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
