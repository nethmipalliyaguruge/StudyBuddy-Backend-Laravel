<x-app-layout>
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('notes.mine') }}" class="text-sm text-gray-600 hover:text-primary mb-2 inline-block">
                <i class="fas fa-arrow-left mr-1"></i>Back to My Notes
            </a>
            <h1 class="section-title">Upload New Note</h1>
            <p class="section-subtitle">Share your study materials with fellow students</p>
        </div>

        <!-- Livewire Upload Form -->
        @livewire('upload-note-form', ['schools' => $schools])

        <!-- Tips -->
        <div class="mt-6 p-4 bg-blue-50 rounded-lg">
            <h4 class="font-medium text-blue-800 mb-2">
                <i class="fas fa-lightbulb mr-2"></i>Tips for better sales
            </h4>
            <ul class="text-sm text-blue-700 space-y-1">
                <li><i class="fas fa-check mr-2"></i>Add clear preview images of your notes</li>
                <li><i class="fas fa-check mr-2"></i>Write a detailed description</li>
                <li><i class="fas fa-check mr-2"></i>Set a fair price based on content quality</li>
                <li><i class="fas fa-check mr-2"></i>Ensure your notes are well-organized and readable</li>
            </ul>
        </div>
    </div>
</x-app-layout>
