@props([
    'variant' => 'primary', // primary, secondary, outline, disabled
    'size' => 'md', // sm, md, lg
    'type' => 'button',
])

@php
    $baseClasses = 'font-semibold rounded-lg transition-colors duration-200 cursor-pointer inline-flex items-center justify-center gap-2';

    $sizeClasses = match($size) {
        'sm' => 'px-3 py-1.5 text-sm',
        'md' => 'px-4 py-2 text-base',
        'lg' => 'px-6 py-3 text-lg',
        default => 'px-4 py-2 text-base',
    };

    $variantClasses = match($variant) {
        'primary' => 'bg-blue-600 text-white hover:bg-blue-700 active:bg-blue-800',
        'secondary' => 'bg-gray-200 text-gray-900 hover:bg-gray-300 active:bg-gray-400',
        'outline' => 'border-2 border-gray-400 text-gray-700 hover:bg-gray-50 active:bg-gray-100',
        'disabled' => 'bg-gray-300 text-gray-500 cursor-not-allowed opacity-60',
        default => 'bg-blue-600 text-white hover:bg-blue-700 active:bg-blue-800',
    };

    $finalClasses = "$baseClasses $sizeClasses $variantClasses";
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => $finalClasses]) }}>
    {{ $slot }}
</button>
