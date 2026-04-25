<div class="article-carousel">
    @cache('product_card_gallery_'.$page->getCacheKey())
    <div class="swiper-container article-slider swiper">
        <div class="swiper-wrapper">
            @if(!empty($page->picture))
            <a href="{{$page->picture ?  : $page->getImgPath(633, 500)}}" class="swiper-slide get-single-popup">
                <img src="{{$page->picture ?  : $page->getImgPath(633, 500)}}" itemprop="image" alt="{{ $page->t('title') }}">
            </a>
            @endif

            @if(!empty($otherPictures))
                @foreach($otherPictures as $pictureOriginal => $pictureSmall)
                    @unless($page->picture === $pictureOriginal)
                        <a href="{{$pictureOriginal}}" class="swiper-slide get-single-popup">
                            <img src="{{glide($pictureOriginal, ['w' => 633, 'h' => 500])}}" itemprop="image" alt="{{ $page->t('title') }}  {{__t('фото')}} №{{$loop->index}}"/>
                        </a>
                    @endunless
                @endforeach
            @endif

        </div>
        <div class="swiper-button-prev"></div>
        <div class="swiper-button-next"></div>
    </div>
    <div class="swiper-container article-thumbs swiper">
        <div class="swiper-wrapper"  >

            @if(!empty($page->picture))
            <a href="#" wire:click.prevent="" class="swiper-slide">
                <img src="{{$page->picture ?  : $page->getImgPath(90, '')}}" itemprop="image" alt="{{ $page->t('title') }}">
            </a>
            @endif

            @if(!empty($otherPictures))
                @foreach($otherPictures as $pictureOriginal => $pictureSmall)
                    @unless($page->picture === $pictureOriginal)
                        <a href="#" wire:click.prevent="" class="swiper-slide">
                            <img src="{{glide($pictureOriginal, ['w' => 90])}}" alt="{{ $page->t('title') }}  {{__t('фото')}} №{{$loop->index}}"/>
                        </a>
                    @endunless
                @endforeach
            @endif

        </div>
    </div>
    @endcache

</div>

