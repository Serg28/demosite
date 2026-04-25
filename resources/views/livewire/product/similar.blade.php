<div class="buy-plus">
    @if (count($list))
    <div class="container">
        <h3 class="font--3">{{__t('З цим товаром також купують')}}</h3>
        <div class="column-3">

                @cache('product_card_characteristics_'.$page->getCacheKey())
                @foreach ($list as $slug => $items)

                    <div class="column card-parts" >
                        <p class="country"><img src="img/ua.svg" alt="">{{__t('в Україні')}}</p>
                        <a href="{{$items->picture}}"
                           data-src="{{$items->picture ?  : $items->getImgPath(633, 500)}}"
                           class="img-block">
                            <img itemprop="image"
                                 src="{{glide($items->picture, ['w' => 633, 'h' => 500])}}" data-src="{{$items->picture}}"
                                 alt="{{ $items->t('title') }}" title="{{ $items->t('title') }}"></a>
                        <div class="info-block">
                            <div class="flex-row">
                                <p class="price">{{ setting('currency') }}{{$items->getPrice()}}</p>
                                <span>{{__t('Артикул:')}} {{ $items->code }} {{--<br> {{__t('Парт №:')}} {{ $items->part_number }} --}}</span>
                            </div>
                            <a href="{{ $items->t('url') }}" class="name-part">{{ $items->t('title') }}</a>
                            <div class="button-block">
                                <button class="js_button_buy_product main-btn main-btn--red"
                                        type="button" onclick="Basket.add(this)"
                                        data-count="1"
                                        @if(setting('checkbox_dynamic_remarketing_google'))
                                        data-dynamic="{{ setting('code_dynamic_remarketing_google') }}"
                                        @endif
                                        @if(setting('checkbox_google_analytics_four'))
                                        data-ga4-addtocart
                                        @endif
                                        data-sum="{{$items->getPrice()}}"
                                        data-id="{{$items->id}}"
                                        data-options="">{{__t('Додати в кошик')}}
                                </button>

                                <a href="{{ $items->t('url') }}" class="details">{{__t('Детальніше')}}</a>

                            </div>
                        </div>
                    </div>
                @endforeach
                @endcache

        </div>
    </div>
    @endif
</div>
