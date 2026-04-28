@props(['current' => 1, 'last' => 1, 'hasMore' => false])

@php
    $window = 2;
    $shown = collect(range(1, $last))->filter(
        fn($p) => $p === 1 || $p === $last || abs($p - $current) <= $window
    )->values();
@endphp

<div class="mt-10 space-y-6">

    {{-- Show More --}}
    @if($hasMore)
        <div class="flex justify-center">
            <button
                wire:click="loadMore"
                wire:loading.attr="disabled"
                wire:target="loadMore"
                class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 transition hover:border-gray-400 hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50"
            >
                <svg wire:loading wire:target="loadMore"
                     class="h-4 w-4 animate-spin text-gray-500"
                     xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
                <span wire:loading wire:target="loadMore">{{ __t('Завантаження...') }}</span>
                <span wire:loading.remove wire:target="loadMore">{{ __t('Показати ще') }}</span>
            </button>
        </div>
    @endif

    {{-- Numbered pagination --}}
    @if($last > 1)
        <nav aria-label="{{ __t('Навігація по сторінках') }}" class="flex justify-center">
            <ul class="flex items-center gap-1">
                <li>
                    @if($current > 1)
                        <button wire:click="setPage({{ $current - 1 }})" wire:loading.attr="disabled"
                                class="flex h-9 w-9 items-center justify-center rounded border border-gray-300 text-sm text-gray-600 transition hover:border-gray-400 hover:bg-gray-50"
                                aria-label="{{ __t('Попередня') }}">←</button>
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
                            <button wire:click="setPage({{ $p }})" wire:loading.attr="disabled"
                                    class="flex h-9 w-9 items-center justify-center rounded border border-gray-300 text-sm text-gray-600 transition hover:border-gray-400 hover:bg-gray-50">{{ $p }}</button>
                        @endif
                    </li>
                    @php $prev = $p; @endphp
                @endforeach

                <li>
                    @if($current < $last)
                        <button wire:click="setPage({{ $current + 1 }})" wire:loading.attr="disabled"
                                class="flex h-9 w-9 items-center justify-center rounded border border-gray-300 text-sm text-gray-600 transition hover:border-gray-400 hover:bg-gray-50"
                                aria-label="{{ __t('Наступна') }}">→</button>
                    @else
                        <span class="flex h-9 w-9 cursor-not-allowed items-center justify-center rounded border border-gray-200 text-sm text-gray-300">→</span>
                    @endif
                </li>
            </ul>
        </nav>
    @endif

</div>
