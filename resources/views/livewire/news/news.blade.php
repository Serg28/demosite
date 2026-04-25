<div>
    <div class="flex-row flex v--center h--between mt-24">
        <div class="scrl">
            <div class="blog-tabs flex v--center">
                <a wire:navigate.hover href="{{ route('blog') }}" class="blog-tab @if(request()->url() == route('blog')) current @endif" data-blog-cat="1">{{ __t('Всі статті') }}</a>
                @if ($categories)
                    @foreach($categories as $key => $category)
                        <a wire:navigate wire:key="news-category-{{$category->id}}" href="{{ $category->getUrl() }}" class="blog-tab @if($currentUrl == $category->getUrl()) current @endif">{{ $category->t('title') }}</a>
                    @endforeach
                @endif
            </div>
        </div>
        @include('partials.sorting')
    </div>
    <div wire:loading.class="opacity-50">
        @include('news.partials.list_item',['list' => $list, 'count' => count($list)])
    </div>
    @include('partials.paginate', ['items' => $news])
</div>
