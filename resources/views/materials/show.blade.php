<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">{{ $note->title }}</h2>
    </x-slot>

    <div class="p-6 max-w-4xl space-y-4">

        {{-- Previews --}}
        <div class="flex gap-3">
            @foreach($note->getMedia('previews') as $media)
                <img src="{{ $media->getUrl() }}"
                     class="w-32 h-32 object-cover rounded border">
            @endforeach
        </div>

        {{-- Description --}}
        <p>{{ $note->description }}</p>

        {{-- Price --}}
        <p class="text-xl font-bold">
            LKR {{ number_format($note->price, 2) }}
        </p>

        {{-- Buy --}}
        @auth
            @if(auth()->user()->purchases()->where('note_id', $note->id)->exists())
                <span class="text-green-600 font-semibold">
            Already Purchased
        </span>
            @else
                <form method="POST" action="{{ route('purchase.store') }}">
                    @csrf
                    <input type="hidden" name="note_id" value="{{ $note->id }}">
                    <button class="bg-blue-600 text-white px-6 py-2 rounded">
                        Buy Now
                    </button>
                </form>
            @endif
        @else
            <a href="{{ route('login') }}"
               class="bg-blue-600 text-white px-6 py-2 rounded">
                Login to Buy
            </a>
        @endauth

    </div>
</x-app-layout>
