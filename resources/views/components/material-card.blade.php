@props(['note', 'showActions' => true])

<div class="card-hover group">
    <!-- Preview Image -->
    <div class="relative h-48 bg-gray-100 overflow-hidden">
        @if($note->getMedia('previews')->first())
            <img src="{{ $note->getMedia('previews')->first()->getUrl() }}"
                 alt="{{ $note->title }}"
                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
        @else
            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-primary/10 to-primary/5">
                <i class="fas fa-file-alt text-4xl text-primary/40"></i>
            </div>
        @endif

        <!-- Status Badge -->
        @if(isset($note->status) && $note->status !== 'approved')
            <span class="absolute top-2 left-2 badge-warning">
                {{ ucfirst($note->status) }}
            </span>
        @endif
    </div>

    <!-- Content -->
    <div class="p-4 space-y-3">
        <!-- Title -->
        <h3 class="font-semibold text-gray-900 truncate-2 leading-tight">
            {{ $note->title }}
        </h3>

        <!-- Description -->
        @if($note->description)
            <p class="text-sm text-gray-600 truncate-2">
                {{ $note->description }}
            </p>
        @endif

        <!-- Meta Info -->
        <div class="flex items-center gap-2 text-xs text-gray-500">
            <i class="fas fa-user"></i>
            <span>{{ $note->user->name ?? 'Unknown' }}</span>
            @if($note->module)
                <span class="mx-1">&bull;</span>
                <i class="fas fa-folder"></i>
                <span>{{ $note->module->title ?? '' }}</span>
            @endif
        </div>

        <!-- Price & Actions -->
        <div class="flex items-center justify-between pt-2 border-t border-gray-100">
            <span class="text-lg font-bold text-primary">
                LKR {{ number_format($note->price, 2) }}
            </span>

            @if($showActions)
                <div class="flex gap-2">
                    <a href="{{ route('materials.show', $note) }}"
                       class="btn-sm btn-secondary">
                        <i class="fas fa-eye mr-1"></i> View
                    </a>

                    @auth
                        @if(auth()->user()->purchases()->where('note_id', $note->id)->where('status', 'completed')->exists())
                            <span class="btn-sm badge-success">
                                <i class="fas fa-check mr-1"></i> Purchased
                            </span>
                        @elseif(auth()->id() !== $note->user_id)
                            <form method="POST" action="{{ route('cart.add', $note) }}" class="inline">
                                @csrf
                                <button type="submit" class="btn-sm btn-primary">
                                    <i class="fas fa-cart-plus mr-1"></i> Add
                                </button>
                            </form>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="btn-sm btn-primary">
                            <i class="fas fa-sign-in-alt mr-1"></i> Login
                        </a>
                    @endauth
                </div>
            @endif
        </div>
    </div>
</div>
