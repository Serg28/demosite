@props([
    'color' => 'blue', // blue, green, red, yellow, gray
    'size' => 'md', // sm, md, lg
])

@php
    $colorClasses = match($color) {
        'blue' => 'bg-blue-100 text-blue-800',
        'green' => 'bg-green-100 text-green-800',
        'red' => 'bg-red-100 text-red-800',
        'yellow' => 'bg-yellow-100 text-yellow-800',
        'gray' => 'bg-gray-100 text-gray-800',
        default => 'bg-blue-100 text-blue-800',
    };

    $sizeClasses = match($size) {
        'sm' => 'px-2 py-1 text-xs',
        'md' => 'px-3 py-1.5 text-sm',
        'lg' => 'px-4 py-2 text-base',
        default => 'px-3 py-1.5 text-sm',
    };

    $finalClasses = "inline-flex items-center rounded-full font-semibold $colorClasses $sizeClasses";
@endphp

<span {{ $attributes->merge(['class' => $finalClasses]) }}>
    {{ $slot }}
</span>
