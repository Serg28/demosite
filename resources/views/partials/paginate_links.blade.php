@if ($paginator->hasPages())
    <div class="pagination">
        @if ($paginator->hasMorePages() && !request()->has("*/admin/*") && request()->get('showMore'))
        <a href="{{ $paginator->nextPageUrl() }}" class="show-more fsz-18 fw-500 color--blue js-show-more">{{__t('Показати ще 24')}}</a>
        @endif
        <div class="pagination-wrap flex v--center">
            <ul class="flex v--center">
                {{-- Previous Page Link --}}
                @if (!$paginator->onFirstPage())
                    <a href="{{request()->url()}}" class="pagination-btn prev all mob-visible"><img src="/img/na.svg" alt=""></a>

                    @if ($paginator->toArray()['first_page_url'] == $paginator->previousPageUrl())
                        <a href="{{request()->url()}}" class="pagination-btn prev"><img src="/assets/images/arrow-blue-left.svg" alt="@lang('pagination.previous')"></a>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')" class="pagination-btn prev">
                            <img src="/assets/images/arrow-blue-left.svg" alt="@lang('pagination.previous')">
                        </a>
                    @endif

                @else
                    <a href="#" class="pagination-btn prev all mob-visible disabled"><img src="/img/na.svg" alt=""></a>
                    <a href="#" class="pagination-btn prev disabled"><img src="/img/ap.svg" alt=""></a>
                @endif

                {{-- Pagination Elements --}}
                @foreach ($elements as $element)

                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <li><span aria-disabled="true">{{ $element }}</span></li>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <li><a href="#" class="current" aria-current="page">{{ $page }}</a></li>
                            @else
                                @if ($paginator->toArray()['first_page_url'] == $url)
                                    <li><a href="{{request()->url()}}">{{ $page }}</a></li>
                                @else
                                    <li><a href="{{ $url }}">{{ $page }}</a></li>
                                @endif
                            @endif
                        @endforeach
{{--                        <p class="mob-visible">--}}
{{--                            {!! str_replace(['[current]', '[total]'], [$paginator->currentPage(), $paginator->lastPage() ], __t('Страница [current] из [total]')) !!}--}}
{{--                        </p>--}}
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <li><a href="{{ $paginator->nextPageUrl() }}" class="pagination-btn next"><img src="/assets/images/arrow-blue-right-1.svg" alt=""></a></li>
                    <li><a href="{{ $paginator->toArray()['last_page_url'] }}" class="pagination-btn next all mob-visible" rel="next"
                       aria-label="@lang('pagination.next')"><img src="/img/na.svg" alt="@lang('pagination.next')"></a></li>
                @else
                    <li><a href="#" class="pagination-btn next disabled"><img src="/assets/images/arrow-blue-right-1.svg" alt=""></a></li>
                    <li><a href="#" class="pagination-btn next all mob-visible disabled"><img src="/img/na.svg" alt=""></a></li>
                @endif
            </ul>
        </div>
    </div>
@endif
