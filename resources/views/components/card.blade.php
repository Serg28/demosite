@props([])

<div {{ $attributes->merge(['class' => 'bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden']) }}>
    @isset($header)
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            {{ $header }}
        </div>
    @endisset

    <div class="px-6 py-4">
        {{ $slot }}
    </div>

    @isset($footer)
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            {{ $footer }}
        </div>
    @endisset
</div>
