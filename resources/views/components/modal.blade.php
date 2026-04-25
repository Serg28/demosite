@props([
    'id' => 'modal',
    'title' => null,
])

<div
    id="{{ $id }}"
    x-data="{ open: false }"
    @keydown.escape="open = false"
    class="fixed inset-0 z-50 overflow-y-auto hidden"
    :class="open && '!block'"
>
    <!-- Backdrop -->
    <div
        class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"
        @click="open = false"
        x-show="open"
    ></div>

    <!-- Modal -->
    <div class="flex items-center justify-center min-h-screen p-4">
        <div
            class="relative bg-white rounded-lg shadow-xl max-w-md w-full"
            @click.stop
            x-show="open"
            x-transition
        >
            @if($title)
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">{{ $title }}</h2>
                    <button
                        @click="open = false"
                        class="text-gray-400 hover:text-gray-600 transition-colors"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            @endif

            <div class="px-6 py-4">
                @isset($body)
                    {{ $body }}
                @else
                    {{ $slot }}
                @endisset
            </div>

            @isset($footer)
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex gap-3 justify-end">
                    {{ $footer }}
                </div>
            @endisset
        </div>
    </div>
</div>
