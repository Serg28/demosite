@if(!empty($product))
    @php
        $partPay = $product->part_pay == 1 && $product->privat_payparts_count > 0 && $product->quantity > 0;
        $monoPay = $product->mono == 1 && $product->mono_payparts_count > 0 && $product->quantity > 0;
        $price = $product->getPrice();
        $oldprice = $product->getPriceOld();
        $url = $product->getUrl();
        $cacheKey = $product->getCacheKey();
        $title = $product->t('title');
        $isPrivatParts = ($product->has_privat_payparts)?$product->privat_payparts_count:0;
        $isMonoParts = ($product->has_mono_payparts)?$product->mono_payparts_count:0;
        //$comments = $product->comments()->with(['user'])->active()->get();
        $rating = $product->rating;//$comments->sum('rating');
        $base_characteristics = $product->baseCharacteristics;
        $checkCompare = $product->checkCompare();
    @endphp
    <div class="product-card" data-id="{{$product->id}}">
        <div class="before"> </div>
        <div class="labels">
            @if(!empty($oldprice))
            <div class="label super-price">{{__t('Супер ціна')}}</div>
            <div class="label discount">-{{$product->getSale()}}</div>
                @cache('product_labels_'.$cacheKey)
                @if($product->labels->isNotEmpty())
                    @foreach($product->labels as $label)
                        <span class="sticker bg-yellow" style="background: {{$label->color}}">{{$label->t('title')}}</span>
                    @endforeach
                @endif
                @endcache
            @endif
        </div>
        <div class="buttons">
            @if(setting('compare_active')==true)
                <button class="compare cart-btn compare-button {{$checkCompare ? 'active' : ''}}"
                        data-id="{{ $product->id }}"
                        title="{{__t('Порівняти')}}">
                    <img src="{{$checkCompare ? '/assets/images/compare-active.svg':'/assets/images/compare-gray.svg'}}" alt="compare-icon" class="compare-icon">
                    <span id="compare-button_{{ $product->id }}" class="spinner compare-black" style="display: none" ></span>
                </button>
            @endif
            <button class="like cart-btn"
                    @if (app('user'))
                        onclick="Like.doToggle(this, {{$product->id}})"
                    @else
                        onclick="toastr.error('{{__t('Для перегляду обраного потрібно авторизуватися')}}')"
                    @endif
                    data-product="{{$product->id}}" title="{{__t('Список побажань')}}">
                @if($product->checkLike())
                    <img src="/assets/images/heart-gray.svg" alt="" class="visible">
                @else
                    <img src="/assets/images/heart-active.svg" alt="" class="hidden">
                @endif
            </button>
        </div>
        <div class="top">
            <div class="image image-1">
                <div class="images">
                    <img src="{!! $product->getImgPath(296, 190) !!}" alt="">
                </div>
            </div>
            <div class="image image-2">
                <div class="images">
                    <img src="/assets/images/p-i2.png" alt="">
                </div>
            </div>
            <div class="image image-3">
                <div class="images">
                    <img src="/assets/images/p-i3.png" alt="">
                </div>
            </div>
            <div class="image image-4">
                <div class="images">
                    <img src="/assets/images/p-i4.png" alt="">
                </div>
            </div>
            <!-- <div class="colors flex v--center">
                <span class="color product-card-color current" style="background: #A8B9C9;" data-color="1"></span>
                <span class="color product-card-color" style="background: #1D252B;" data-color="2"></span>
                <span class="color product-card-color" style="background: #E6E0EB;" data-color="3"></span>
                <span class="color product-card-color" style="background: #FC102F;" data-color="4"></span>
                <a href="" class="all-colors">+1</a>
            </div> -->
        </div>
        <div class="bottom">
            <a href="{{$url}}" class="product-name fsz-16 fw-400 color--black">{{$title}}</a>
            <div class="row flex v--center h--between">
                <div class="left">
                    @if($product->id_1c)<span class="fsz-12 color--gray">{{__t('Код товару')}}: {{ $product->id_1c }}</span>@endif
                </div>
                <div class="right">
                    <a href="" class="raiting flex v--center">
                        <span class="stars flex v--center">
                            @foreach([1,2,3,4,5] as $key=>$star)
                                @if($star > $rating)
                                    <img src="/assets/images/star-empty.svg" alt="">
                                @else
                                    <img src="/assets/images/star-full.svg" alt="">
                                @endif
                            @endforeach
                        </span>
                        <span class="num color--black fsz-13">{{--0(3) --}}</span>
{{--                        <span class="num color--black fsz-13">{{$comments->count()}}({{$rating}}%)</span>--}}
                    </a>
                </div>
            </div>
            <div class="bank-row flex v--center">
                @if($isPrivatParts || $isMonoParts)
                    @if($isMonoParts)
                        <div class="bank">
                            <div class="visible flex v--center">
                                <img src="/assets/images/mono-black.svg" alt="">
                                <div class="num fsz-12 color--gray">{{$isMonoParts}}</div>
                            </div>
                            <div class="hidden">
                                {!!  str_replace(['[isMonoParts]','[isMonoParts2]'], [$isMonoParts , $isMonoParts-1], setting('opisaniya-oplata-chastyami-monobank-dlya-tovara')) !!}

                                <a href="{{url('/credit/monobank')}}" class="main-btn blue-small">{{ __t('Докладніше') }}</a>
                            </div>
                        </div>
                    @endif
                    @if($isPrivatParts)
                        <div class="bank">
                            <div class="visible flex v--center">
                                <img src="/assets/images/privat.svg" alt="">
                                <div class="num fsz-12 color--gray">{{$isPrivatParts}}</div>
                            </div>
                            <div class="hidden">
                                {!!  str_replace(['[isPrivatParts]','[isPrivatParts2]'], [$isPrivatParts , $isPrivatParts-1], setting('opisaniya-oplata-chastyami-privatbank-dlya-tovara')) !!}

                                <a href="{{url('/credit/privatbank')}}" class="main-btn blue-small">{{ __t('Докладніше') }}</a>
                            </div>
                        </div>
                    @endif
                @endif
                {{--<div class="bank">
                    <div class="visible flex v--center">
                        <img src="/assets/images/credit.svg" alt="">
                        <div class="num fsz-12 color--gray">10</div>
                    </div>
                    <div class="hidden">
                        <p class="fsz-14 fw600">Оплата частинами від ПриватБанку на 4 платежі</p>
                        <ol>
                            <li>1. Наявність кредитної картки ПриватБанку.</li>
                            <li>2. Доступний ліміт за сервісом "Оплата частинами".</li>
                        </ol>
                        <span class="fsz-12">Перший платіж буде списано з кредитної картки. Перший платіж +14 наступних. Не сумісно з подарунками, підвищеними бонусами та акцією 2 в 1.</span>
                        <a href="" class="main-btn blue-small">Докладніше</a>
                    </div>
                </div>--}}
            </div>
            <div class="bottom-row flex h--between v--end"  itemprop="offers" itemscope itemtype="https://schema.org/Offer">
                <meta itemprop="price" content="{{ $price }}"/>
                <meta itemprop="url" content="{{ $product->getUrl() }}"/>
                @if($product->quantity>0)
                    <link itemprop="availability" href="https://schema.org/InStock"/>
                @else
                    <link itemprop="availability" href="https://schema.org/OutOfStock"/>
                @endif

                <div class="price-wrap ">
                    @if ($oldprice && ($oldprice>$price))
                    <s class="fsz-14 color--gray">{{$oldprice}} {{ setting('currency') }}</s>
                    @endif
                    <p class="price fsz-18 fw-600">@money($price) {{ setting('currency') }}</p>
                </div>
                @if($product->quantity>0)
                <button class="main-btn blue-small icon-left"
                        onclick="Basket.add(this)"
                        data-count="1"
                        @if(setting('checkbox_dynamic_remarketing_google'))
                        data-dynamic="{{ setting('code_dynamic_remarketing_google') }}"
                        @endif
                        @if(setting('checkbox_google_analytics_four'))
                        data-ga4-addtocart
                        @endif
                        data-sum="{{ $price }}"
                        data-id="{{$product->id}}">
                    <span class="icon"><img src="/assets/images/cart-white.svg" alt=""></span>{{ __t('У кошик') }}
                </button>
                @endif
            </div>
        </div>

        @if (!empty($base_characteristics))
        <div class="hidden-block">
            <div class="info-rows">
                @loop ($base_characteristics as $characteristic)
                <div class="info-row flex v--center h--between">
                    <span class="fsz-14 color--gray">{!! $characteristic["title"] !!}</span>
                    <p class="fsz-14">{!! $characteristic["values"] !!}</p>
                </div>
                @endloop
            </div>
        </div>
        @endif
    </div>
@endif
