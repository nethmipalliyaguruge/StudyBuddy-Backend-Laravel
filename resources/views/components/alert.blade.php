@props(['type' => 'info', 'dismissible' => true])

@php
    $classes = match($type) {
        'success' => 'alert-success',
        'error' => 'alert-error',
        'warning' => 'alert-warning',
        default => 'alert-info',
    };

    $icon = match($type) {
        'success' => 'fa-check-circle',
        'error' => 'fa-exclamation-circle',
        'warning' => 'fa-exclamation-triangle',
        default => 'fa-info-circle',
    };
@endphp

<div x-data="{ show: true }"
     x-show="show"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     {{ $attributes->merge(['class' => $classes . ' flex items-center justify-between']) }}>
    <div class="flex items-center gap-2">
        <i class="fas {{ $icon }}"></i>
        <span>{{ $slot }}</span>
    </div>
    @if($dismissible)
        <button @click="show = false" class="text-current opacity-70 hover:opacity-100">
            <i class="fas fa-times"></i>
        </button>
    @endif
</div>
