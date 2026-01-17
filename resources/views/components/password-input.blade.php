@props(['name' => 'password', 'id' => null, 'placeholder' => 'Enter password'])

@php
    $inputId = $id ?? $name;
@endphp

<div x-data="{ show: false }" class="relative">
    <input :type="show ? 'text' : 'password'"
           name="{{ $name }}"
           id="{{ $inputId }}"
           placeholder="{{ $placeholder }}"
           {{ $attributes->merge(['class' => 'input pr-10']) }}>

    <button type="button"
            @click="show = !show"
            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 focus:outline-none">
        <i class="fas" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
    </button>
</div>
