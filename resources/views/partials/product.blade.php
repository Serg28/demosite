@php
    $price = $product->getPrice();
    $oldprice = $product->getPriceOld();
    $url = $product->getUrl();
    $cacheKey = $product->getCacheKey();
    $title = $product->t('title');
    $privatParts = $product->getPrivatPartsCount();
    $monoParts = $product->getMonoPartsCount();
    $base_characteristics = (!empty($compare_delete)) ? $product->baseCharacteristics : false;
    $favBtnDell = isset($favorite);
    $checkCompare = $product->checkCompare();
    $isActive = $product->isActive();
    $currency = setting('currency');
    $key = "product_".$product->id;
@endphp
<div class="product-card" data-id="{{$product->id}}" wire:key="product-card-{{$product->id}}" data-ga-push="0" data-code="{{ $product->getArticle() }}" data-sum="{{$price}}">
    <div class="before"></div>
    <div class="labels">
        @if(!empty($oldprice) && $oldprice>$price)
            <div class="label super-price">{{__t('Супер ціна')}}</div>
            <div class="label discount">-{{$product->getSale()}}</div>
        @endif
        @cache('product_labels_'.$cacheKey)
        @if($product->labels->isNotEmpty())
            @foreach($product->labels as $label)
                <span class="sticker bg-yellow" style="background: {{$label->color}}">{{$label->t('title')}}</span>
            @endforeach
        @endif
        @endcache
    </div>
    <div class="buttons">

        @if(setting('compare_active')==true)
            @if(!empty($compare_delete))
                <button class="trash cart-btn" wire:click="removeProduct({{ $product->id }})" wire:loading.attr="disabled">
                    <img src="/assets/images/trash.svg" alt="trash-icon" class="visible">
                    <span wire:loading.class="spinner" wire:target="removeProduct({{ $product->id }})"></span>
                </button>
            @else
                <button class="compare cart-btn compare-button {{$checkCompare ? 'active' : ''}}" data-id="{{ $product->id }}" title="{{__t('Порівняти')}}">
                    <img src="{{$checkCompare ? '/assets/images/compare-active.svg':'/assets/images/compare-gray.svg'}}"
                         alt="compare-icon" class="compare-icon">
                    <span id="compare-button_{{ $product->id }}" class="spinner compare-black" style="display: none"></span>
                </button>
            @endif
        @endif
        @include('partials.product-btn-favorite', ['product' => $product, 'class' => '', 'favBtnDell' => $favBtnDell])
    </div>
    <div class="top">
        <div class="image image-1">
            <div class="images">
                @php
                    $img = !empty($product->picture) ? $product->getImgPath(296, 190) : glide($product->firstOtherPicture, ['w'=>296, 'h'=>190]);
                @endphp
                <img loading="lazy" src="{!! $img !!}" alt="{{e($title)}}">
            </div>
        </div>
        {{--<div class="image image-2">
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
         <div class="colors flex v--center">
            <span class="color product-card-color current" style="background: #A8B9C9;" data-color="1"></span>
            <span class="color product-card-color" style="background: #1D252B;" data-color="2"></span>
            <span class="color product-card-color" style="background: #E6E0EB;" data-color="3"></span>
            <span class="color product-card-color" style="background: #FC102F;" data-color="4"></span>
            <a href="" class="all-colors">+1</a>
        </div> --}}
    </div>
    <div class="bottom">
        <a href="{{$url}}" class="product-name fsz-16 fw-400 color--black" data-ga4-click>{{$title}}</a>
        <div class="row flex v--center h--between">
            <div class="left">
                @if($product->getArticle())
                    <span class="fsz-12 color--gray">{{__t('Код товару')}}: {{ $product->getArticle() }}</span>
                @endif
            </div>
            <div class="right">
                <a href="" class="raiting flex v--center">
                    <rating-stars>{{$product->rating}}</rating-stars>
                    <span class="num color--black fsz-13">{{$product->count_comments ?? 0}}</span>
                </a>
            </div>
        </div>
        <div class="bank-row flex v--center">
            @if(($privatParts || $monoParts) && $isActive )
                @if($monoParts)
                    <div class="bank">
                        <div class="visible flex v--center">
                            <img src="/assets/images/mono-black.svg" alt="">
                            <div class="num fsz-12 color--gray">{{$monoParts}}</div>
                        </div>
                        <div class="hidden">
                            {!!  str_replace(['[isMonoParts]','[isMonoParts2]'], [$monoParts , $monoParts-1], setting('opisaniya-oplata-chastyami-monobank-dlya-tovara')) !!}

                            <a href="{{geturl('/credit/monobank')}}"
                               class="main-btn blue-small">{{ __t('Докладніше') }}</a>
                        </div>
                    </div>
                @endif
                @if($privatParts)
                    <div class="bank">
                        <div class="visible flex v--center">
                            <img src="/assets/images/privat.svg" alt="">
                            <div class="num fsz-12 color--gray">{{$privatParts}}</div>
                        </div>
                        <div class="hidden">
                            {!!  str_replace(['[isPrivatParts]','[isPrivatParts2]'], [$privatParts , $privatParts-1], setting('opisaniya-oplata-chastyami-privatbank-dlya-tovara')) !!}

                            <a href="{{geturl('/credit/privatbank')}}"
                               class="main-btn blue-small">{{ __t('Докладніше') }}</a>
                        </div>
                    </div>
                @endif
            @endif
        </div>
        <div class="bottom-row flex h--between v--end" itemprop="offers" itemscope itemtype="https://schema.org/Offer">
            <meta itemprop="price" content="{{ $price }}"/>
            <meta itemprop="url" content="{{ $url }}"/>
            @if($isActive)
                <link itemprop="availability" href="https://schema.org/InStock"/>
            @else
                <link itemprop="availability" href="https://schema.org/OutOfStock"/>
            @endif

            <div class="price-wrap ">
                @if ($oldprice && ($oldprice>$price))
                    <s class="fsz-14 color--gray">{{$oldprice}} {{ $currency }}</s>
                @endif
                <p class="price fsz-18 fw-600">@money($price) {{ $currency }}</p>
            </div>
            @if($isActive)
                {{-- TODO: сделать кнопку через JS --}}
                @include('partials.product-btn-add-to-cart', ['product' => $product])
            @endif
        </div>
    </div>

    {{-- TODO: сделать асинхронно --}}
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