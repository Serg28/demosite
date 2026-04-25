@props([
    'type' => 'text',
    'label' => null,
    'placeholder' => null,
    'value' => null,
    'name' => null,
    'id' => null,
    'required' => false,
])

@php
    $finalId = $id ?? $name;
@endphp

<div class="flex flex-col gap-2 w-full">
    @if($label)
        <label for="{{ $finalId }}" class="text-sm font-medium text-gray-700">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    @if($type === 'textarea')
        <textarea
            id="{{ $finalId }}"
            name="{{ $name }}"
            placeholder="{{ $placeholder }}"
            {{ $required ? 'required' : '' }}
            {{ $attributes->merge(['class' => 'px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent']) }}
        >{{ $value }}</textarea>
    @else
        <input
            type="{{ $type }}"
            id="{{ $finalId }}"
            name="{{ $name }}"
            value="{{ $value }}"
            placeholder="{{ $placeholder }}"
            {{ $required ? 'required' : '' }}
            {{ $attributes->merge(['class' => 'px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent']) }}
        />
    @endif
</div>
