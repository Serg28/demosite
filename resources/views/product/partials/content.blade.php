<div class="product-title-screen mb-80" itemscope itemtype="https://schema.org/Product">
    <div class="sticky-row">
        <div class="container">
            <div class="flex-row flex v--center h--between">
                <div class="scrl">
                    <div class="tabs flex v--center">
                        <a href="#characteristic" class="tab pt-18 pb-18 relative current scroll-to">{{__t('Характеристики')}}</a>
                        <a href="#similar" class="tab pt-18 pb-18 relative scroll-to">{{__t('Схожі товари')}}</a>
                        <a href="#reviews" class="tab pt-18 pb-18 flex v--start relative scroll-to">{{__t('Відгуки')}} <span class="fsz-12 color--gray">({{$page->count_comments}})</span></a>
                        <a href="#acses" class="tab pt-18 pb-18 relative scroll-to">{{__t('Аксесуари')}}</a>
                    </div>
                </div>
                <div class="hidden-row">
                    <div class="row flex v--center">
                        <meta itemprop="url" content="{{ $page->getUrl() }}"/>
                        <meta itemprop="price" content="{{ $price }}"/>
                        <meta itemprop="priceCurrency" content="{{ setting('currency') }}"/>
                        @if($page->isActive())
                            <link itemprop="availability" href="https://schema.org/InStock"/>
                        @else
                            <link itemprop="availability" href="https://schema.org/OutOfStock"/>
                        @endif
                        <div class="img">
                            @if(!empty($page->picture))
                                <img src="{{$page->getImgPath(42, 42)}}" alt="{{ e($page->t('title')) }}" title="{{ e($page->t('title')) }}" itemprop="image">
                            @else
                                <img src="{!! glide($page->firstOtherPicture, ['w'=>42, 'h'=>42]) !!}" alt="{{ e($page->t('title')) }}" title="{{ e($page->t('title')) }}" itemprop="image">
                            @endif

                        </div>
                        <p class="prod-name ml-24 fsz-14" itemprop="name">{!! $page->getSeoH1() !!}</p>
                        <div class="price-wrap ml-40">
                            @if ($priceOld && ($priceOld>$price))<s class="fsz-12 color--gray">@money($priceOld) {{ setting('currency') }}</s>@endif
                            <p class="color--red fsz-16 fw-600">@money($price) {{ setting('currency') }}</p>
                        </div>
                        {{--@if($page->isActive())
                            <livewire:cart.addtocart :productId="$page->id" />
                        @endif --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="product-title-screen__bottom-row pt-24 pb-40">
        <div class="container">
            <div class="product-title-screen__wrap flex v--start h--between">
                <div class="left">
                    @include('product.partials.content.gallery')
                    @include('product.partials.content.characteristics')
                </div>
                <div class="right">
                    <div class="prod-title-absolute-row">
                        <h2 class="prod-name fsz-24 fw-600 color--black">{{$page->getSeoH1()}}</h2>
                        <a href="#reviews" class="raiting flex v--center mt-8 scroll-to">
                            <div class="starts flex v--center">
                                @for ($i = 1; $i <= 5; $i++)
                                    <img src="{{ $i > $comments->sum('rating') ? '/assets/images/star-empty.svg' : '/assets/images/star-full.svg' }}" alt="{{$i}}">
                                @endfor
                            </div>
                            <span class="fsz-14 ml-12 color--black">{{$comments->count()}} {{__t('відгуків')}}</span>
                        </a>
                        <div class="row flex v--center h--between mt-8 pb-16">
                            <div class="info flex v--center">
                                <p class="status" style="color:{!! $page->status_color ?? '' !!}">
                                    {{$page->status_name}}
                                </p>
                                @if($page->status_description)
                                <span class="relative ml-8">
									<img src="/assets/images/info-blue.svg" alt="">
									<span class="description p-12 fsz-12 color--black">{{$page->status_description}}</span>
								</span>
                                @endif
                            </div>
                            <span class="article fsz-16 color--gray">{{__t('Код товару:')}} {{$page->getArticle()}}</span>
                        </div>
                    </div>

                    @cache('relative_product_' . $page->getCacheKey())
                    <livewire:product.relation-products :page="$page->id" />
                    @endcache

                    @if ($priceOld && ($priceOld>$price))
                    <div class="cash-row flex v--center mt-24">
                        <div class="col percent flex v--center">
                            <span class="p-5 flex v--center h--center icon"><img src="/assets/images/percent.svg" alt=""></span>
                            <p class="ml-8 fsz-13 fw-400">{{__t('Вигода')}} <span class="ml-4 fw-600 color--red">@money($priceOld - $price) {{ setting('currency') }}</span></p>
                        </div>
                        {{--
                        <div class="col cash flex v--center">
                            <span class="p-5 flex v--center h--center icon"><img src="/assets/images/cash.svg" alt=""></span>
                            <p class="ml-8 fsz-13 fw-400">{{__t('Кешбек')}}  <span class="ml-4 fw-600 color--orange">@money($priceOld - $price) {{ setting('currency') }}</span></p>
                        </div> --}}
                    </div>
                    @endif
                    <div class="price-row flex v--center h--between mt-24">
                        <div class="price flex v--center">
                            @if ($priceOld && ($priceOld>$price))<s class="fsz-18 color--gray">{{$priceOld}} {{ setting('currency') }}</s>@endif
                            <p class="color--red fsz-20 fw-600 ml-16">@money($price) {{ setting('currency') }}</p>
                        </div>
                        <div class="buttons flex v--center">
                            @if(setting('compare_active')==true)
                                @php
                                    $checkCompare = $page->checkCompare();
                                @endphp
                                <button class="compare cart-btn compare-button {{$checkCompare ? 'active' : ''}}"
                                        data-id="{{ $page->id }}"
                                        title="{{__t('Порівняти')}}">
                                    <img src="{{$checkCompare ? '/assets/images/compare-active.svg':'/assets/images/compare-gray.svg'}}" alt="compare-icon" class="compare-icon">
                                    <span id="compare-button_{{ $page->id }}" class="spinner compare-black" style="display: none" ></span>
                                </button>
                            @endif
                                @include('partials.product-btn-favorite', ['product' => $page, 'class' => '', 'active' => $page->checkLike()])
                            @if (app('user'))

                            @endif
                        </div>
                    </div>
                        @include('partials.product_btn-add-to-cart', ['product' => $page, 'spinner_style' => true])

                        @if($page->isActive())
                        <div class="bank-swiper swiper mt-40">
                            <div class="swiper-wrapper">

                                @if($isMonoParts)
                                    @include('product.partials.payparts', ['logo' => '/assets/images/mono-black.svg', 'count'=>$page->mono_payparts_count])
                                @endif
                                @if($isPrivatParts)
                                    @include('product.partials.payparts', ['logo' => '/assets/images/privat.svg', 'count' => $page->privat_payparts_count])
                                @endif
                            </div>
                            <div class="bank-swiper-button-prev bank-btn flex v--center h--end"><img src="/assets/images/arrow-right-gray.svg" alt=""></div>
                            <div class="bank-swiper-button-next bank-btn flex v--center h--end"><img src="/assets/images/arrow-right-gray.svg" alt=""></div>
                        </div>


                        {{-- TODO: как работает
                        <button class="main-btn border-small mt-16">{{__t('Купити в кредит від')}} 2 250 ₴/міс</button>
                        --}}

                        <div class="info-columns flex v--center h--wrap mt-40">
                            <div class="info-column flex v--center">
                                <div class="icon"><img src="/assets/images/ic-i1.svg" alt=""></div>
                                <p class="fsz-14">{{__t('Завжди в наявності')}}</p>
                            </div>
                            <div class="info-column flex v--center">
                                <div class="icon"><img src="/assets/images/ic-i2.svg" alt=""></div>
                                <p class="fsz-14">{{__t('Сплачуй як зручно')}}</p>
                            </div>
                            <div class="info-column flex v--center">
                                <div class="icon"><img src="/assets/images/ic-i3.svg" alt=""></div>
                                <p class="fsz-14">{{__t('Швидка доставка/самовивіз')}}</p>
                            </div>
                            <div class="info-column flex v--center">
                                <div class="icon"><img src="/assets/images/ic-i4.svg" alt=""></div>
                                <p class="fsz-14">14 {{__t('днів обмін/повернення')}}</p>
                            </div>
                        </div>

                    <livewire:product.quick-order :productId="$page->id" />
                    @endif

                    @include('product.partials.content.delivery')
                    @include('product.partials.content.pay')

                    <div class="change pt-24 pb-24">
                        <p class="fw-600 flex v--center get-more">{{__t('Обмін та повернення')}} <img src="/assets/images/arrow-right-black1.svg" alt="" class="ml-4"></p>
                    </div>
                    @include('product.partials.content.add-service')

                </div>
            </div>
        </div>
    </div>
</div>
