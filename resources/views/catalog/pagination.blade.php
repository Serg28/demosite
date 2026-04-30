<div data-js-paginator>
@if($paginator->hasPages())
    <div class="mt-10 space-y-6">

        {{-- Show More — JS (load-more.js) appends next page HTML without reload --}}
        @if($paginator->hasMorePages())
            <div class="flex justify-center">
                <button class="js-load-more inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 transition hover:border-gray-400 hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50"
                        data-next-page="{{ $paginator->currentPage() + 1 }}"
                        data-last-page="{{ $paginator->lastPage() }}">
                    <span class="js-load-more-text">{{ __t('Показати ще') }}</span>
                </button>
            </div>
        @endif

        {{-- Numbered pagination --}}
        <nav aria-label="{{ __t('Навігація по сторінках') }}" class="flex justify-center">
            <ul class="flex items-center gap-1">

                <li>
                    @if($paginator->onFirstPage())
                        <span class="flex h-9 w-9 cursor-not-allowed items-center justify-center rounded border border-gray-200 text-sm text-gray-300">←</span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}"
                           class="flex h-9 w-9 items-center justify-center rounded border border-gray-300 text-sm text-gray-600 transition hover:border-gray-400 hover:bg-gray-50"
                           aria-label="{{ __t('Попередня') }}">←</a>
                    @endif
                </li>

                @foreach($elements as $element)
                    @if(is_string($element))
                        <li><span class="flex h-9 w-9 items-center justify-center text-sm text-gray-400">…</span></li>
                    @elseif(is_array($element))
                        @foreach($element as $page => $url)
                            <li>
                                @if($page == $paginator->currentPage())
                                    <span aria-current="page"
                                          class="flex h-9 w-9 items-center justify-center rounded border border-blue-500 bg-blue-500 text-sm font-semibold text-white">{{ $page }}</span>
                                @else
                                    <a href="{{ $url }}"
                                       class="flex h-9 w-9 items-center justify-center rounded border border-gray-300 text-sm text-gray-600 transition hover:border-gray-400 hover:bg-gray-50">{{ $page }}</a>
                                @endif
                            </li>
                        @endforeach
                    @endif
                @endforeach

                <li>
                    @if($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}"
                           class="flex h-9 w-9 items-center justify-center rounded border border-gray-300 text-sm text-gray-600 transition hover:border-gray-400 hover:bg-gray-50"
                           aria-label="{{ __t('Наступна') }}">→</a>
                    @else
                        <span class="flex h-9 w-9 cursor-not-allowed items-center justify-center rounded border border-gray-200 text-sm text-gray-300">→</span>
                    @endif
                </li>

            </ul>
        </nav>
    </div>
@endif
</div>
