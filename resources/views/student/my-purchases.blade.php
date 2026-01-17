<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">My Purchases</h2>
    </x-slot>

    <div class="p-6 space-y-4">

        @if(session('success'))
            <div class="bg-green-100 p-3 rounded">
                {{ session('success') }}
            </div>
        @endif

        @forelse($purchases as $purchase)
            <div class="border p-4 rounded space-y-2">

                <h3 class="font-bold">
                    {{ $purchase->note->title }}
                </h3>

                <p class="text-sm text-gray-600">
                    Purchased for LKR {{ number_format($purchase->price, 2) }}
                </p>

                {{-- DOWNLOAD --}}
                <a href="{{ $purchase->note->getFirstMediaUrl('note_file') }}"
                   target="_blank"
                   class="text-blue-600 underline text-sm">
                    Download Note
                </a>

            </div>
        @empty
            <p>You have not purchased any notes yet.</p>
        @endforelse

    </div>
</x-app-layout>
