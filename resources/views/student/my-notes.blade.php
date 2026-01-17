<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">My Notes</h2>
    </x-slot>

    <div class="p-6 space-y-4">

        {{-- Upload Button --}}
        <div class="flex justify-end mb-4">
            <a href="{{ route('notes.create') }}"
               class="bg-blue-600 text-white px-4 py-2 rounded">
                Upload New Note
            </a>
        </div>

        {{-- Success Message --}}
        @if(session('success'))
            <div class="bg-green-100 p-3 rounded">
                {{ session('success') }}
            </div>
        @endif

        {{-- Notes List --}}
        @foreach($notes as $note)
            <div class="border p-4 rounded space-y-3">

                {{-- PREVIEW IMAGES --}}
                @if($note->getMedia('previews')->count())
                    <div class="flex gap-2 flex-wrap">
                        @foreach($note->getMedia('previews') as $media)
                            <img src="{{ $media->getUrl() }}"
                                 alt="Preview image"
                                 class="w-24 h-24 object-cover border rounded">
                        @endforeach
                    </div>
                @endif

                {{-- EDIT FORM --}}
                <form method="POST"
                      action="{{ route('notes.update', $note) }}"
                      class="space-y-2">
                    @csrf
                    @method('PUT')

                    <input type="text"
                           name="title"
                           value="{{ $note->title }}"
                           class="border p-2 w-full">

                    <textarea name="description"
                              class="border p-2 w-full">{{ $note->description }}</textarea>

                    <input type="number"
                           step="0.01"
                           name="price"
                           value="{{ $note->price }}"
                           class="border p-2 w-full">

                    <div class="flex justify-between items-center mt-2">
                        <span class="text-sm text-gray-600">
                            Status: <strong>{{ $note->status }}</strong>
                        </span>

                        <button class="bg-green-600 text-white px-3 py-1 rounded">
                            Update
                        </button>
                    </div>
                </form>

                {{-- DELETE --}}
                <form method="POST"
                      action="{{ route('notes.destroy', $note) }}">
                    @csrf
                    @method('DELETE')

                    <button class="text-red-600 text-sm">
                        Delete Note
                    </button>
                </form>

            </div>
        @endforeach

    </div>
</x-app-layout>
