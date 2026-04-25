<div class="flex flex-col items-center justify-center gap-3">
    @php
        $sizeClasses = match($size) {
            'sm' => 'w-6 h-6',
            'lg' => 'w-12 h-12',
            default => 'w-8 h-8',
        };

        $colorClasses = match($color) {
            'white' => 'text-white',
            'gray' => 'text-gray-400',
            default => 'text-blue-600',
        };
    @endphp

    <svg class="animate-spin {{ $sizeClasses }} {{ $colorClasses }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
    </svg>

    @if($message)
        <p class="text-sm font-medium text-gray-600">{{ $message }}</p>
    @endif
</div>
