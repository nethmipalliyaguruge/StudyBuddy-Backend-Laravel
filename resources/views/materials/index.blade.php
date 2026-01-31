@php
    $isGuest = !auth()->check();
    $layout = $isGuest ? 'layouts.landing' : 'layouts.app';
@endphp

<x-dynamic-component :component="$layout">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="section-title">Study Materials</h1>
            <p class="section-subtitle">Browse and discover quality notes from fellow students</p>
        </div>

        <!-- Livewire Material Browser -->
        @livewire('material-browser')
    </div>
</x-dynamic-component>
