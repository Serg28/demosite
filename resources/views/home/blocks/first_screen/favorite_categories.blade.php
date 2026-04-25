@if(isset($sliderCatalog->favoriteCategories))
<div class="product-columns-title flex v--center">
    @foreach ($sliderCatalog->favoriteCategories as $item)
        <a href="{{$item->getUrl()}}" class="product-column flex v--center">
            @if($item->ico) <img loading="lazy" src="{{glide($item->ico, ['w'=> 120, 'h'=>120])}}" alt="{{$item->t('title')}}"> @endif
            <span class="fsz-16">{{$item->t('title')}}</span>
        </a>
    @endforeach
</div>
@endif
