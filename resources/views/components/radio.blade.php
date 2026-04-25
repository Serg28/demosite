@props([
    'label' => null,
    'name' => null,
    'id' => null,
    'value' => null,
    'checked' => false,
])

@php
    $finalId = $id ?? $name;
@endphp

<div class="flex items-center gap-2">
    <input
        type="radio"
        id="{{ $finalId }}"
        name="{{ $name }}"
        value="{{ $value }}"
        {{ $checked ? 'checked' : '' }}
        {{ $attributes->merge(['class' => 'w-4 h-4 text-blue-600 cursor-pointer border-gray-300 focus:ring-2 focus:ring-blue-500']) }}
    />
    @if($label)
        <label for="{{ $finalId }}" class="text-sm font-medium text-gray-700 cursor-pointer">
            {{ $label }}
        </label>
    @endif
</div>
