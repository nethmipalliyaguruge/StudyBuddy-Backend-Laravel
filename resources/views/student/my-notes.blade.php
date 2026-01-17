<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
            <div>
                <h1 class="section-title">My Notes</h1>
                <p class="section-subtitle">Manage your uploaded study materials</p>
            </div>
            <a href="{{ route('notes.create') }}" class="btn-primary">
                <i class="fas fa-plus mr-2"></i>Upload New Note
            </a>
        </div>

        <!-- Notes List -->
        @forelse($notes as $note)
            <div class="card mb-6">
                <div class="p-6">
                    <div class="flex flex-col lg:flex-row gap-6">
                        <!-- Preview Images -->
                        <div class="lg:w-48 flex-shrink-0">
                            @if($note->getMedia('previews')->count())
                                <div class="grid grid-cols-3 lg:grid-cols-2 gap-2">
                                    @foreach($note->getMedia('previews')->take(4) as $media)
                                        <img src="{{ $media->getUrl() }}"
                                             alt="Preview"
                                             class="w-full h-20 object-cover rounded-lg border">
                                    @endforeach
                                </div>
                            @else
                                <div class="w-full h-32 bg-gray-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-image text-3xl text-gray-300"></i>
                                </div>
                            @endif
                        </div>

                        <!-- Note Details -->
                        <div class="flex-grow">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ $note->title }}</h3>
                                    <p class="text-sm text-gray-600 truncate-2 mb-3">{{ $note->description }}</p>
                                </div>
                                <span class="badge-{{ $note->status === 'approved' ? 'success' : ($note->status === 'pending' ? 'warning' : 'danger') }} flex-shrink-0">
                                    {{ ucfirst($note->status) }}
                                </span>
                            </div>

                            <!-- Meta Info -->
                            <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500 mb-4">
                                <span><i class="fas fa-tag mr-1"></i>LKR {{ number_format($note->price, 2) }}</span>
                                @if($note->module)
                                    <span><i class="fas fa-folder mr-1"></i>{{ $note->module->title }}</span>
                                @endif
                                <span><i class="fas fa-calendar mr-1"></i>{{ $note->created_at->format('M d, Y') }}</span>
                                <span><i class="fas fa-shopping-cart mr-1"></i>{{ $note->purchases->count() }} sales</span>
                            </div>

                            <!-- Edit Form -->
                            <form method="POST" action="{{ route('notes.update', $note) }}" class="space-y-4">
                                @csrf
                                @method('PUT')

                                <div class="grid sm:grid-cols-3 gap-4">
                                    <div>
                                        <label class="form-label">Title</label>
                                        <input type="text"
                                               name="title"
                                               value="{{ $note->title }}"
                                               class="input"
                                               required>
                                    </div>
                                    <div>
                                        <label class="form-label">Price (LKR)</label>
                                        <input type="number"
                                               step="0.01"
                                               name="price"
                                               value="{{ $note->price }}"
                                               class="input"
                                               required>
                                    </div>
                                    <div class="flex items-end">
                                        <button type="submit" class="btn-primary btn-sm w-full sm:w-auto">
                                            <i class="fas fa-save mr-2"></i>Update
                                        </button>
                                    </div>
                                </div>

                                <div>
                                    <label class="form-label">Description</label>
                                    <textarea name="description"
                                              rows="2"
                                              class="input">{{ $note->description }}</textarea>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Actions Footer -->
                <div class="px-6 py-4 bg-gray-50 border-t flex items-center justify-between">
                    <a href="{{ route('materials.show', $note) }}" class="text-sm text-primary hover:underline">
                        <i class="fas fa-eye mr-1"></i>View Public Page
                    </a>

                    <form method="POST" action="{{ route('notes.destroy', $note) }}"
                          onsubmit="return confirm('Are you sure you want to delete this note?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-sm text-red-600 hover:text-red-700">
                            <i class="fas fa-trash mr-1"></i>Delete
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <!-- Empty State -->
            <div class="card p-12 text-center">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-file-alt text-3xl text-gray-400"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">No notes yet</h3>
                <p class="text-gray-600 mb-6">Start sharing your knowledge by uploading your first note!</p>
                <a href="{{ route('notes.create') }}" class="btn-primary">
                    <i class="fas fa-upload mr-2"></i>Upload Your First Note
                </a>
            </div>
        @endforelse
    </div>
</x-app-layout>
