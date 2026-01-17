<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Study Materials</h2>
    </x-slot>

    <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6">

        @forelse($notes as $note)
            <div class="border rounded p-4 space-y-3">

                {{-- Preview Image --}}
                @if($note->getMedia('previews')->first())
                    <img src="{{ $note->getMedia('previews')->first()->getUrl() }}"
                         class="w-full h-40 object-cover rounded">
                @endif

                {{-- Title --}}
                <h3 class="font-bold text-lg">
                    {{ $note->title }}
                </h3>

                {{-- Seller --}}
                <p class="text-sm text-gray-600">
                    Seller: {{ $note->user->name }}
                </p>

                {{-- Price --}}
                <p class="text-lg font-semibold">
                    LKR {{ number_format($note->price, 2) }}
                </p>

                {{-- Actions --}}
                <div class="flex gap-2">

                    <a href="{{ route('materials.show', $note) }}"
                       class="border px-3 py-1 rounded text-sm">
                        View
                    </a>

                    @auth
                        <button class="bg-blue-600 text-white px-3 py-1 rounded text-sm">
                            Buy
                        </button>
                    @else
                        <a href="{{ route('login') }}"
                           class="bg-blue-600 text-white px-3 py-1 rounded text-sm">
                            Login to Buy
                        </a>
                    @endauth

                </div>

            </div>
        @empty
            <p>No materials available.</p>
        @endforelse

    </div>
</x-app-layout>
