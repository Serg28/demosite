@php
    if (! isset($scrollTo)) {
        $scrollTo = 'body';
    }

    $scrollIntoViewJsSnippet = ($scrollTo !== false)
        ? <<<JS
           (\$el.closest('{$scrollTo}') || document.querySelector('{$scrollTo}')).scrollIntoView({behavior: 'smooth'})
        JS
        : '';
@endphp

<div>
    {{-- Paginator Bootstrap template(bootstrap.blade) --}}
    @if ($paginator->hasPages())
        <div class="pagination">
            @if ($paginator->hasMorePages() && !request()->has("*/admin/*") )
                <span class="show-more fsz-18 fw-500 color--blue js-show-more" wire:click="showMore()" >{{__t('Показати ще ')}} {{$paginator->perPage()}}</span>
            @endif

            <div class="pagination-wrap flex v--center">
                <ul class="flex v--center">
                    @if ($paginator->onFirstPage())
                        <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.previous')" style="display: none;">
                            <span class="page-link" aria-hidden="true"><img src="/assets/images/arrow-blue-left.svg" alt=""></span>
                        </li>
                    @else
                        <li class="page-item">
                            <button type="button" x-on:click="{{ $scrollIntoViewJsSnippet }}" dusk="previousPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}" class="page-link" wire:click="previousPage('{{ $paginator->getPageName() }}')" wire:loading.attr="disabled" rel="prev" aria-label="@lang('pagination.previous')"><img src="/assets/images/arrow-blue-left.svg" alt=""></button>
                        </li>
                    @endif
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <li class="page-item disabled" aria-disabled="true"><span class="page-link">{{ $element }}</span></li>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <li class="page-item active" wire:key="paginator-{{ $paginator->getPageName() }}-page-{{ $page }}" aria-current="page"><span class="page-link">{{ $page }}</span></li>
                                @else
                                    <li class="page-item" wire:key="paginator-{{ $paginator->getPageName() }}-page-{{ $page }}"><button type="button" class="page-link" x-on:click="{{ $scrollIntoViewJsSnippet }}" wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')">{{ $page }}</button></li>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <li class="page-item">
                            <button type="button" dusk="nextPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}" class="page-link" x-on:click="{{ $scrollIntoViewJsSnippet }}" wire:click="nextPage('{{ $paginator->getPageName() }}')" wire:loading.attr="disabled" rel="next" aria-label="@lang('pagination.next')"><img src="/assets/images/arrow-blue-right-1.svg" alt=""></button>
                        </li>
                    @else
                        <li class="page-item disabled" aria-disabled="true" style="display: none;" aria-label="@lang('pagination.next')">
                            <span class="page-link" aria-hidden="true"><img src="/assets/images/arrow-blue-right-1.svg" alt=""></span>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    @endif
</div>
