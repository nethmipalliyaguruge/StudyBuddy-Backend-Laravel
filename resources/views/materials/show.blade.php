@php
    $isGuest = !auth()->check();
    $layout = $isGuest ? 'layouts.landing' : 'layouts.app';
@endphp

<x-dynamic-component :component="$layout">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Breadcrumb -->
        <nav class="mb-6 text-sm">
            <ol class="flex items-center gap-2 text-gray-500">
                <li><a href="{{ route('materials.index') }}" class="hover:text-primary">Materials</a></li>
                <li><i class="fas fa-chevron-right text-xs"></i></li>
                <li class="text-gray-900 font-medium truncate">{{ $note->title }}</li>
            </ol>
        </nav>

        <div class="grid lg:grid-cols-5 gap-8">
            <!-- Main Content (3 columns) -->
            <div class="lg:col-span-3 space-y-6">
                <!-- Preview Images Gallery -->
                @if($note->getMedia('previews')->count() > 0)
                    <div class="card overflow-hidden" x-data="{ activeImage: 0 }">
                        <!-- Main Image -->
                        <div class="relative bg-gray-100">
                            @foreach($note->getMedia('previews') as $index => $media)
                                <img x-show="activeImage === {{ $index }}"
                                     src="{{ $media->getUrl() }}"
                                     alt="{{ $note->title }} - Preview {{ $index + 1 }}"
                                     class="w-full h-80 object-contain">
                            @endforeach
                        </div>

                        <!-- Thumbnails -->
                        @if($note->getMedia('previews')->count() > 1)
                            <div class="flex gap-2 p-4 overflow-x-auto">
                                @foreach($note->getMedia('previews') as $index => $media)
                                    <button @click="activeImage = {{ $index }}"
                                            :class="activeImage === {{ $index }} ? 'ring-2 ring-primary' : 'opacity-60 hover:opacity-100'"
                                            class="flex-shrink-0 w-20 h-20 rounded-lg overflow-hidden transition-all">
                                        <img src="{{ $media->getUrl() }}"
                                             alt="Thumbnail {{ $index + 1 }}"
                                             class="w-full h-full object-cover">
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @else
                    <div class="card p-12 bg-gradient-to-br from-primary/10 to-primary/5 flex items-center justify-center">
                        <div class="text-center">
                            <i class="fas fa-file-alt text-6xl text-primary/40 mb-4"></i>
                            <p class="text-gray-600">No preview images available</p>
                        </div>
                    </div>
                @endif

                <!-- Description -->
                <div class="card p-6">
                    <h2 class="font-semibold text-gray-900 mb-3">
                        <i class="fas fa-info-circle mr-2 text-primary"></i>Description
                    </h2>
                    <div class="prose prose-sm max-w-none text-gray-600">
                        {{ $note->description ?? 'No description provided.' }}
                    </div>
                </div>

                <!-- Module Info -->
                @if($note->module)
                    <div class="card p-6">
                        <h2 class="font-semibold text-gray-900 mb-3">
                            <i class="fas fa-folder mr-2 text-primary"></i>Module Information
                        </h2>
                        <div class="grid sm:grid-cols-3 gap-4 text-sm">
                            @if($note->module->level && $note->module->level->school)
                                <div>
                                    <span class="text-gray-500">School</span>
                                    <p class="font-medium text-gray-900">{{ $note->module->level->school->name }}</p>
                                </div>
                                <div>
                                    <span class="text-gray-500">Level</span>
                                    <p class="font-medium text-gray-900">{{ $note->module->level->name }}</p>
                                </div>
                            @endif
                            <div>
                                <span class="text-gray-500">Module</span>
                                <p class="font-medium text-gray-900">{{ $note->module->title }}</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar (2 columns) -->
            <div class="lg:col-span-2">
                <div class="card p-6 sticky top-24 space-y-6">
                    <!-- Title & Price -->
                    <div>
                        <h1 class="text-xl font-bold text-gray-900 mb-2">{{ $note->title }}</h1>
                        <div class="flex items-baseline gap-2">
                            <span class="text-3xl font-bold text-primary">
                                LKR {{ number_format($note->price, 2) }}
                            </span>
                        </div>
                    </div>

                    <!-- Seller Info -->
                    <div class="flex items-center gap-3 pb-4 border-b">
                        <div class="w-10 h-10 bg-primary/10 rounded-full flex items-center justify-center">
                            <i class="fas fa-user text-primary"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Uploaded by</p>
                            <p class="font-medium text-gray-900">{{ $note->user->name ?? 'Unknown' }}</p>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    @auth
                        @if(auth()->user()->purchases()->where('note_id', $note->id)->exists())
                            <!-- Already Purchased -->
                            <div class="space-y-3">
                                <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-center">
                                    <i class="fas fa-check-circle text-green-600 text-2xl mb-2"></i>
                                    <p class="font-semibold text-green-800">You own this note</p>
                                </div>
                                <a href="{{ route('purchases.index') }}" class="btn-primary w-full">
                                    <i class="fas fa-download mr-2"></i>Go to My Purchases
                                </a>
                            </div>
                        @elseif(auth()->id() === $note->user_id)
                            <!-- Own Note -->
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-center">
                                <i class="fas fa-info-circle text-blue-600 text-2xl mb-2"></i>
                                <p class="font-semibold text-blue-800">This is your note</p>
                            </div>
                            <a href="{{ route('notes.mine') }}" class="btn-secondary w-full">
                                <i class="fas fa-edit mr-2"></i>Manage Your Notes
                            </a>
                        @else
                            <!-- Purchase Options -->
                            <div class="space-y-3">
                                <form method="POST" action="{{ route('cart.add', $note) }}">
                                    @csrf
                                    <button type="submit" class="btn-outline w-full">
                                        <i class="fas fa-cart-plus mr-2"></i>Add to Cart
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('purchase.store') }}">
                                    @csrf
                                    <input type="hidden" name="note_id" value="{{ $note->id }}">
                                    <button type="submit" class="btn-primary w-full btn-lg">
                                        <i class="fas fa-bolt mr-2"></i>Buy Now
                                    </button>
                                </form>
                            </div>
                        @endif
                    @else
                        <!-- Guest -->
                        <div class="space-y-3">
                            <a href="{{ route('login') }}" class="btn-primary w-full btn-lg">
                                <i class="fas fa-sign-in-alt mr-2"></i>Login to Purchase
                            </a>
                            <p class="text-center text-sm text-gray-500">
                                Don't have an account?
                                <a href="{{ route('register') }}" class="text-primary hover:underline">Sign up</a>
                            </p>
                        </div>
                    @endauth

                    <!-- Note Info -->
                    <div class="text-sm text-gray-500 space-y-2 pt-4 border-t">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-calendar w-4"></i>
                            <span>Uploaded {{ $note->created_at->diffForHumans() }}</span>
                        </div>
                        @if($note->status === 'approved')
                            <div class="flex items-center gap-2 text-green-600">
                                <i class="fas fa-check-circle w-4"></i>
                                <span>Verified by admin</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Back Button -->
        <div class="mt-8">
            <a href="{{ route('materials.index') }}" class="text-gray-600 hover:text-primary transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>Back to Materials
            </a>
        </div>
    </div>
</x-dynamic-component>
