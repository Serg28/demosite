@props(['current' => 1, 'last' => 1, 'hasMore' => false])

@php
    $window = 2;
    $shown  = collect(range(1, $last))
        ->filter(fn ($p) => $p === 1 || $p === $last || abs($p - $current) <= $window)
        ->values();

    $pageUrl = fn (int $p) => $p > 1
        ? url()->current() . '?' . http_build_query(array_merge(request()->query(), ['page' => $p]))
        : url()->current() . (count(array_diff_key(request()->query(), ['page' => ''])) ? '?' . http_build_query(array_diff_key(request()->query(), ['page' => ''])) : '');
@endphp

<div class="mt-10 space-y-6">

    {{-- Show More — handled by JS (load-more.js), no wire:click --}}
    @if($hasMore)
        <div class="flex justify-center">
            <button
                class="js-load-more inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 transition hover:border-gray-400 hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50"
                data-next-page="{{ $current + 1 }}"
                data-last-page="{{ $last }}"
            >
                <span class="js-load-more-text">{{ __t('Показати ще') }}</span>
            </button>
        </div>
    @endif

    {{-- Numbered pagination — standard <a> links --}}
    @if($last > 1)
        <nav aria-label="{{ __t('Навігація по сторінках') }}" class="flex justify-center">
            <ul class="flex items-center gap-1">
                <li>
                    @if($current > 1)
                        <a href="{{ $pageUrl($current - 1) }}"
                           class="flex h-9 w-9 items-center justify-center rounded border border-gray-300 text-sm text-gray-600 transition hover:border-gray-400 hover:bg-gray-50"
                           aria-label="{{ __t('Попередня') }}">←</a>
                    @else
                        <span class="flex h-9 w-9 cursor-not-allowed items-center justify-center rounded border border-gray-200 text-sm text-gray-300">←</span>
                    @endif
                </li>

                @php $prev = null; @endphp
                @foreach($shown as $p)
                    @if($prev !== null && $p - $prev > 1)
                        <li><span class="flex h-9 w-9 items-center justify-center text-sm text-gray-400">…</span></li>
                    @endif
                    <li>
                        @if($p === $current)
                            <span aria-current="page"
                                  class="flex h-9 w-9 items-center justify-center rounded border border-blue-500 bg-blue-500 text-sm font-semibold text-white">{{ $p }}</span>
                        @else
                            <a href="{{ $pageUrl($p) }}"
                               class="flex h-9 w-9 items-center justify-center rounded border border-gray-300 text-sm text-gray-600 transition hover:border-gray-400 hover:bg-gray-50">{{ $p }}</a>
                        @endif
                    </li>
                    @php $prev = $p; @endphp
                @endforeach

                <li>
                    @if($current < $last)
                        <a href="{{ $pageUrl($current + 1) }}"
                           class="flex h-9 w-9 items-center justify-center rounded border border-gray-300 text-sm text-gray-600 transition hover:border-gray-400 hover:bg-gray-50"
                           aria-label="{{ __t('Наступна') }}">→</a>
                    @else
                        <span class="flex h-9 w-9 cursor-not-allowed items-center justify-center rounded border border-gray-200 text-sm text-gray-300">→</span>
                    @endif
                </li>
            </ul>
        </nav>
    @endif

</div>
