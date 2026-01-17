@props(['title', 'value', 'icon' => 'fa-chart-bar', 'color' => 'primary', 'trend' => null, 'trendUp' => true])

@php
    $colorClasses = match($color) {
        'primary' => 'bg-primary/10 text-primary',
        'success' => 'bg-green-100 text-green-600',
        'warning' => 'bg-yellow-100 text-yellow-600',
        'danger' => 'bg-red-100 text-red-600',
        'info' => 'bg-blue-100 text-blue-600',
        default => 'bg-gray-100 text-gray-600',
    };
@endphp

<div class="stats-card">
    <div class="flex items-start justify-between">
        <div>
            <p class="text-sm text-gray-500 font-medium">{{ $title }}</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $value }}</p>

            @if($trend)
                <p class="text-xs mt-2 flex items-center gap-1 {{ $trendUp ? 'text-green-600' : 'text-red-600' }}">
                    <i class="fas {{ $trendUp ? 'fa-arrow-up' : 'fa-arrow-down' }}"></i>
                    {{ $trend }}
                </p>
            @endif
        </div>

        <div class="w-12 h-12 rounded-lg {{ $colorClasses }} flex items-center justify-center">
            <i class="fas {{ $icon }} text-xl"></i>
        </div>
    </div>
</div>
