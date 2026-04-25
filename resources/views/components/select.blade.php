@props([
    'label' => null,
    'name' => null,
    'id' => null,
    'placeholder' => null,
    'options' => [],
    'value' => null,
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

    <select
        id="{{ $finalId }}"
        name="{{ $name }}"
        {{ $required ? 'required' : '' }}
        {{ $attributes->merge(['class' => 'px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent']) }}
    >
        @if($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif

        @foreach($options as $optionValue => $optionLabel)
            <option value="{{ $optionValue }}" {{ $value === (string)$optionValue ? 'selected' : '' }}>
                {{ $optionLabel }}
            </option>
        @endforeach
    </select>
</div>
