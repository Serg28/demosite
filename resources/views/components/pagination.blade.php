@props([
    'paginator' => null,
])

@if($paginator && $paginator->hasPages())
    <nav class="flex items-center justify-between gap-2 mt-8">
        <!-- Previous Button -->
        @if($paginator->onFirstPage())
            <span class="px-3 py-2 text-gray-400 cursor-not-allowed">← Предыдущая</span>
        @else
            <a
                href="{{ $paginator->previousPageUrl() }}"
                class="px-3 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
            >
                ← Предыдущая
            </a>
        @endif

        <!-- Page Numbers -->
        <div class="flex items-center gap-1">
            @foreach($paginator->getUrlRange(1, $paginator->lastPage()) as $page => $url)
                @if($page == $paginator->currentPage())
                    <span class="px-3 py-2 bg-blue-600 text-white rounded-lg font-semibold">
                        {{ $page }}
                    </span>
                @else
                    <a
                        href="{{ $url }}"
                        class="px-3 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
                    >
                        {{ $page }}
                    </a>
                @endif
            @endforeach
        </div>

        <!-- Next Button -->
        @if($paginator->hasMorePages())
            <a
                href="{{ $paginator->nextPageUrl() }}"
                class="px-3 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
            >
                Следующая →
            </a>
        @else
            <span class="px-3 py-2 text-gray-400 cursor-not-allowed">Следующая →</span>
        @endif
    </nav>
@endif
