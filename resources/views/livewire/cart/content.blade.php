<div>
<div class="top-row">
    <p class="fsz-24 fw-600">{{__t('Кошик')}}</p>
</div>
    @if($productsInCart->isNotEmpty())
        <div class="midle-row p-24 bg--white flex v--start h--between">
            <div class="content flex fd--column">
                @foreach ($productsInCart as $product)
                    @php
                        $model = $product->model;
                    @endphp
                    <div class="product-row flex v--start" wire:loading.class="opacity-50" wire:key="cart-row-{{ $product->rowId }}">
                        <a href="{{ $model->getUrl() }}" class="image flex v--center h--center">
                            @if(!empty($model->picture))
                            {!! $model->getImg(120, 120) !!}
                            @else
                            <img src="{!! glide($model->firstOtherPicture, ['w'=>120, 'h'=>120]) !!}" alt="{{ e($model->t('title')) }}">
                            @endif
                        </a>
                        <div class="right-block relative">
                            <div class="trash flex v--center h--center" wire:click="remove('{{ $product->rowId }}')" wire:loading.attr="disabled">
                                <svg width="800px" height="800px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M10 10V16M14 10V16M18 6V18C18 19.1046 17.1046 20 16 20H8C6.89543 20 6 19.1046 6 18V6M4 6H20M15 6V5C15 3.89543 14.1046 3 13 3H11C9.89543 3 9 3.89543 9 5V6" stroke="#AFB1C4" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <a href="{{ $model->getUrl() }}" class="prod-name color--black">{{ $model->t('title') }}</a>
                            @if($model->getArticle())<p class="mt-8 fsz-12 color--gray">{{__t('Код товару')}}: {{ $model->getArticle() }}</p>@endif
                            <div class="flex-row flex v--center h--between mt-12">
                                <div class="price flex v--center">
                                    @if($model->getPriceOld())
                                    <s class="fsz-14 color--gray">@money($model->getPriceOld()) {{ setting('currency') }}</s>
                                    @endif
                                    <p class="color--red fsz-14 ml-12">@money($product->price) {{ setting('currency') }}</p>
                                </div>
                                <div class="calc-wrap flex v--center">
                                    <div class="calc">
                                        <div class="input-group flex v--center">
                                            <button class="minus-item @if($product->qty === 1) disabled @endif" wire:click="decrementQuantity('{{ $product->rowId }}')" wire:loading.attr="disabled" type="button">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                    <path d="M8 12.5H17" stroke="#0A0527"/>
                                                    <circle cx="12.5" cy="12.5" r="9" stroke="#0A0527"/>
                                                </svg>
                                            </button>
                                            <input type="text" wire:model.lazy="quantities.{{$product->rowId}}" onkeypress="return isNumberKey(event)" class="item-quantity" wire:loading.attr="disabled" maxlength="3" autocomplete="off" readonly>
                                            <button class="plus-item @if($model->getQuantity()===$product->qty) disabled @endif" wire:click="incrementQuantity('{{ $product->rowId }}')" wire:loading.attr="disabled" type="button">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                    <path d="M12.5 8V12.5M12.5 12.5H8M12.5 12.5V17M12.5 12.5H17" stroke="#0A0527"/>
                                                    <circle cx="12.5" cy="12.5" r="9" stroke="#0A0527"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                    <p class="fw-600 ml-40">@money($product->price) {{ setting('currency') }}</p>
                                </div>

                            </div>
                            {{-- TODO: додаткові послуги
                            <div class="add-service color--blue fsz-14 mt-8 flex v--center">Додаткові послуги <img class="ml-12" src="/assets/images/cat-arrow-down-blue.svg" alt=""></div>
                            <div class="labels-wrapper mt-16 bg--light-blue br--br-4 pr-12">
                                <div class="flex-row flex v--center h--between">
                                    <label for="input-1" class="flex v--center checkbox">
                                        <input type="checkbox" id="input-1">
                                        <p href="" class="flex v--start color--gray">Послуга «Новий гаджет»
                                            <span class="relative ml-8 info mt-4">
                                                        <img src="/assets/images/info-icon.svg" alt="">
                                                        <span class="description p-12 fsz-12 color--black">
                                                            Товар уже їде до нас! Можете сміливо оформляти замовлення
                                                        </span>
                                                    </span>
                                        </p>
                                    </label>
                                    <p class="fw-600">199 ₴</p>
                                </div>
                                <div class="flex-row flex v--center h--between">
                                    <label for="input-2" class="flex v--center checkbox">
                                        <input type="checkbox" id="input-2">
                                        <p href="" class="flex v--start color--gray">Подовжена гарантія +12 міс
                                            <span class="relative ml-8 info mt-4">
                                                        <img src="/assets/images/info-icon.svg" alt="">
                                                        <span class="description p-12 fsz-12 color--black">
                                                            Товар уже їде до нас! Можете сміливо оформляти замовлення
                                                        </span>
                                                    </span>
                                        </p>
                                    </label>
                                    <p class="fw-600">1 650 ₴</p>
                                </div>
                                <div class="flex-row flex v--center h--between">
                                    <label for="input-3" class="flex v--center checkbox">
                                        <input type="checkbox" id="input-3">
                                        <p href="" class="flex v--start color--gray">Подовжена гарантія +24 міс
                                            <span class="relative ml-8 info mt-4">
                                                        <img src="/assets/images/info-icon.svg" alt="">
                                                        <span class="description p-12 fsz-12 color--black">
                                                            Товар уже їде до нас! Можете сміливо оформляти замовлення
                                                        </span>
                                                    </span>
                                        </p>
                                    </label>
                                    <p class="fw-600">2 400 ₴</p>
                                </div>
                            </div>
                            --}}
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="side-bar bg--light-blue p-16">
                {{-- TODO: промокод ? --}}
                {{--<div class="promo-wrap">
                    <div class="visible-row flex v--center h--between">
                        <span class="fsz-14 color--blue">У мене є промокод</span>
                        <div class="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="7" viewBox="0 0 12 7" fill="none">
                                <path d="M0.375002 0.625001L6 6.25L11.625 0.625" stroke="#2264DC"/>
                            </svg>
                        </div>
                    </div>
                    <div class="hidden-row">
                        <div class="row flex v--center">
                            <lebel class="input small">
                                <input type="text" placeholder=" ">
                                <span>Промокод</span>
                                <div class="input-placeholder-clear"><img src="/assets/images/closer.svg" alt=""></div>
                            </lebel>
                            <button class="main-btn blue-small">ОК</button>
                        </div>
                    </div>
                </div>--}}
                {{--<div class="flex-row flex v--center h--between pt-16 mt-16"> --}}
                <div class="flex-row flex v--center h--between _pt-16 _mt-16" style="border: none;">
                    <p>{{__t('Разом')}}:</p>
                    <p class="fsz-18 fw-600">@money($cartTotal) {{ setting('currency') }}</p>
                </div>
                @if($productsInCart->isNotEmpty())
                <a href="{{ route('checkout') }}" class="main-btn blue-big mt-16">{{__t('Оформити замовлення')}}</a>
                @endif
            </div>
        </div>
        {{-- TODO: Вместе дешевле --}}
        {{---<div class="cart-swiper-wrapper p-24">
            <p class="fsz-18 fw-600">Разом дешевше</p>
            <div class="custom-swiper-wrapper mt-16">
                <div class="cart-swiper swiper custom-swiper">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide">
                            <div class="cart-product-card">
                                <a href="" class="flex v--center h--center image">
                                    <img src="/assets/images/cart-prod-i.png" alt="">
                                </a>
                                <div class="bottom-row">
                                    <a href="" class="name fsz-14 color--black">Портативний ЗП Apple MagSafe Battery (White) MJWY3</a>
                                    <a href="" class="raiting flex v--center mt-8">
                                                    <span class="stars flex v--center">
                                                        <img src="/assets/images/star-full.svg" alt="">
                                                        <img src="/assets/images/star-full.svg" alt="">
                                                        <img src="/assets/images/star-full.svg" alt="">
                                                        <img src="/assets/images/star-full.svg" alt="">
                                                        <img src="/assets/images/star-empty.svg" alt="">
                                                    </span>
                                        <span class="num color--black fsz-13">129</span>
                                    </a>
                                    <div class="price flex v--end h--between mt-12">
                                        <div class="price-wrap ">
                                            <s class="fsz-12 color--gray">25 713 ₴</s>
                                            <p class="price fsz-16 fw-600 color--red">42 899 ₴</p>
                                        </div>
                                        <button class="main-btn blue-small icon-left"><span class="icon"><img src="/assets/images/cart-white.svg" alt=""></span></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="cart-product-card">
                                <a href="" class="flex v--center h--center image">
                                    <img src="/assets/images/cart-prod-i.png" alt="">
                                </a>
                                <div class="bottom-row">
                                    <a href="" class="name fsz-14 color--black">Портативний ЗП Apple MagSafe Battery (White) MJWY3</a>
                                    <a href="" class="raiting flex v--center mt-8">
                                                    <span class="stars flex v--center">
                                                        <img src="/assets/images/star-full.svg" alt="">
                                                        <img src="/assets/images/star-full.svg" alt="">
                                                        <img src="/assets/images/star-full.svg" alt="">
                                                        <img src="/assets/images/star-full.svg" alt="">
                                                        <img src="/assets/images/star-empty.svg" alt="">
                                                    </span>
                                        <span class="num color--black fsz-13">129</span>
                                    </a>
                                    <div class="price flex v--end h--between mt-12">
                                        <div class="price-wrap ">
                                            <!-- <s class="fsz-12 color--gray">25 713 ₴</s> -->
                                            <p class="price fsz-16 fw-600">42 899 ₴</p>
                                        </div>
                                        <button class="main-btn blue-small icon-left"><span class="icon"><img src="/assets/images/cart-white.svg" alt=""></span></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="cart-product-card">
                                <a href="" class="flex v--center h--center image">
                                    <img src="/assets/images/cart-prod-i.png" alt="">
                                </a>
                                <div class="bottom-row">
                                    <a href="" class="name fsz-14 color--black">Портативний ЗП Apple MagSafe Battery (White) MJWY3</a>
                                    <a href="" class="raiting flex v--center mt-8">
                                                    <span class="stars flex v--center">
                                                        <img src="/assets/images/star-full.svg" alt="">
                                                        <img src="/assets/images/star-full.svg" alt="">
                                                        <img src="/assets/images/star-full.svg" alt="">
                                                        <img src="/assets/images/star-full.svg" alt="">
                                                        <img src="/assets/images/star-empty.svg" alt="">
                                                    </span>
                                        <span class="num color--black fsz-13">129</span>
                                    </a>
                                    <div class="price flex v--end h--between mt-12">
                                        <div class="price-wrap ">
                                            <s class="fsz-12 color--gray">25 713 ₴</s>
                                            <p class="price fsz-16 fw-600 color--red">42 899 ₴</p>
                                        </div>
                                        <button class="main-btn blue-small icon-left"><span class="icon"><img src="/assets/images/cart-white.svg" alt=""></span></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="cart-product-card">
                                <a href="" class="flex v--center h--center image">
                                    <img src="/assets/images/cart-prod-i.png" alt="">
                                </a>
                                <div class="bottom-row">
                                    <a href="" class="name fsz-14 color--black">Портативний ЗП Apple MagSafe Battery (White) MJWY3</a>
                                    <a href="" class="raiting flex v--center mt-8">
                                                    <span class="stars flex v--center">
                                                        <img src="/assets/images/star-full.svg" alt="">
                                                        <img src="/assets/images/star-full.svg" alt="">
                                                        <img src="/assets/images/star-full.svg" alt="">
                                                        <img src="/assets/images/star-full.svg" alt="">
                                                        <img src="/assets/images/star-empty.svg" alt="">
                                                    </span>
                                        <span class="num color--black fsz-13">129</span>
                                    </a>
                                    <div class="price flex v--end h--between mt-12">
                                        <div class="price-wrap ">
                                            <!-- <s class="fsz-12 color--gray">25 713 ₴</s> -->
                                            <p class="price fsz-16 fw-600 ">42 899 ₴</p>
                                        </div>
                                        <button class="main-btn blue-small icon-left"><span class="icon"><img src="/assets/images/cart-white.svg" alt=""></span></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="cart-product-card">
                                <a href="" class="flex v--center h--center image">
                                    <img src="/assets/images/cart-prod-i.png" alt="">
                                </a>
                                <div class="bottom-row">
                                    <a href="" class="name fsz-14 color--black">Портативний ЗП Apple MagSafe Battery (White) MJWY3</a>
                                    <a href="" class="raiting flex v--center mt-8">
                                                    <span class="stars flex v--center">
                                                        <img src="/assets/images/star-full.svg" alt="">
                                                        <img src="/assets/images/star-full.svg" alt="">
                                                        <img src="/assets/images/star-full.svg" alt="">
                                                        <img src="/assets/images/star-full.svg" alt="">
                                                        <img src="/assets/images/star-empty.svg" alt="">
                                                    </span>
                                        <span class="num color--black fsz-13">129</span>
                                    </a>
                                    <div class="price flex v--end h--between mt-12">
                                        <div class="price-wrap ">
                                            <s class="fsz-12 color--gray">25 713 ₴</s>
                                            <p class="price fsz-16 fw-600 color--red">42 899 ₴</p>
                                        </div>
                                        <button class="main-btn blue-small icon-left"><span class="icon"><img src="/assets/images/cart-white.svg" alt=""></span></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="cart-product-card">
                                <a href="" class="flex v--center h--center image">
                                    <img src="/assets/images/cart-prod-i.png" alt="">
                                </a>
                                <div class="bottom-row">
                                    <a href="" class="name fsz-14 color--black">Портативний ЗП Apple MagSafe Battery (White) MJWY3</a>
                                    <a href="" class="raiting flex v--center mt-8">
                                                    <span class="stars flex v--center">
                                                        <img src="/assets/images/star-full.svg" alt="">
                                                        <img src="/assets/images/star-full.svg" alt="">
                                                        <img src="/assets/images/star-full.svg" alt="">
                                                        <img src="/assets/images/star-full.svg" alt="">
                                                        <img src="/assets/images/star-empty.svg" alt="">
                                                    </span>
                                        <span class="num color--black fsz-13">129</span>
                                    </a>
                                    <div class="price flex v--end h--between mt-12">
                                        <div class="price-wrap ">
                                            <s class="fsz-12 color--gray">25 713 ₴</s>
                                            <p class="price fsz-16 fw-600 color--red">42 899 ₴</p>
                                        </div>
                                        <button class="main-btn blue-small icon-left"><span class="icon"><img src="/assets/images/cart-white.svg" alt=""></span></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="cart-product-card">
                                <a href="" class="flex v--center h--center image">
                                    <img src="/assets/images/cart-prod-i.png" alt="">
                                </a>
                                <div class="bottom-row">
                                    <a href="" class="name fsz-14 color--black">Портативний ЗП Apple MagSafe Battery (White) MJWY3</a>
                                    <a href="" class="raiting flex v--center mt-8">
                                                    <span class="stars flex v--center">
                                                        <img src="/assets/images/star-full.svg" alt="">
                                                        <img src="/assets/images/star-full.svg" alt="">
                                                        <img src="/assets/images/star-full.svg" alt="">
                                                        <img src="/assets/images/star-full.svg" alt="">
                                                        <img src="/assets/images/star-empty.svg" alt="">
                                                    </span>
                                        <span class="num color--black fsz-13">129</span>
                                    </a>
                                    <div class="price flex v--end h--between mt-12">
                                        <div class="price-wrap ">
                                            <s class="fsz-12 color--gray">25 713 ₴</s>
                                            <p class="price fsz-16 fw-600 color--red">42 899 ₴</p>
                                        </div>
                                        <button class="main-btn blue-small icon-left"><span class="icon"><img src="/assets/images/cart-white.svg" alt=""></span></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="cart-product-card">
                                <a href="" class="flex v--center h--center image">
                                    <img src="/assets/images/cart-prod-i.png" alt="">
                                </a>
                                <div class="bottom-row">
                                    <a href="" class="name fsz-14 color--black">Портативний ЗП Apple MagSafe Battery (White) MJWY3</a>
                                    <a href="" class="raiting flex v--center mt-8">
                                                    <span class="stars flex v--center">
                                                        <img src="/assets/images/star-full.svg" alt="">
                                                        <img src="/assets/images/star-full.svg" alt="">
                                                        <img src="/assets/images/star-full.svg" alt="">
                                                        <img src="/assets/images/star-full.svg" alt="">
                                                        <img src="/assets/images/star-empty.svg" alt="">
                                                    </span>
                                        <span class="num color--black fsz-13">129</span>
                                    </a>
                                    <div class="price flex v--end h--between mt-12">
                                        <div class="price-wrap ">
                                            <s class="fsz-12 color--gray">25 713 ₴</s>
                                            <p class="price fsz-16 fw-600 color--red">42 899 ₴</p>
                                        </div>
                                        <button class="main-btn blue-small icon-left"><span class="icon"><img src="/assets/images/cart-white.svg" alt=""></span></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="cart-product-card">
                                <a href="" class="flex v--center h--center image">
                                    <img src="/assets/images/cart-prod-i.png" alt="">
                                </a>
                                <div class="bottom-row">
                                    <a href="" class="name fsz-14 color--black">Портативний ЗП Apple MagSafe Battery (White) MJWY3</a>
                                    <a href="" class="raiting flex v--center mt-8">
                                                    <span class="stars flex v--center">
                                                        <img src="/assets/images/star-full.svg" alt="">
                                                        <img src="/assets/images/star-full.svg" alt="">
                                                        <img src="/assets/images/star-full.svg" alt="">
                                                        <img src="/assets/images/star-full.svg" alt="">
                                                        <img src="/assets/images/star-empty.svg" alt="">
                                                    </span>
                                        <span class="num color--black fsz-13">129</span>
                                    </a>
                                    <div class="price flex v--end h--between mt-12">
                                        <div class="price-wrap ">
                                            <s class="fsz-12 color--gray">25 713 ₴</s>
                                            <p class="price fsz-16 fw-600 color--red">42 899 ₴</p>
                                        </div>
                                        <button class="main-btn blue-small icon-left"><span class="icon"><img src="/assets/images/cart-white.svg" alt=""></span></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="cart-product-card">
                                <a href="" class="flex v--center h--center image">
                                    <img src="/assets/images/cart-prod-i.png" alt="">
                                </a>
                                <div class="bottom-row">
                                    <a href="" class="name fsz-14 color--black">Портативний ЗП Apple MagSafe Battery (White) MJWY3</a>
                                    <a href="" class="raiting flex v--center mt-8">
                                                    <span class="stars flex v--center">
                                                        <img src="/assets/images/star-full.svg" alt="">
                                                        <img src="/assets/images/star-full.svg" alt="">
                                                        <img src="/assets/images/star-full.svg" alt="">
                                                        <img src="/assets/images/star-full.svg" alt="">
                                                        <img src="/assets/images/star-empty.svg" alt="">
                                                    </span>
                                        <span class="num color--black fsz-13">129</span>
                                    </a>
                                    <div class="price flex v--end h--between mt-12">
                                        <div class="price-wrap ">
                                            <s class="fsz-12 color--gray">25 713 ₴</s>
                                            <p class="price fsz-16 fw-600 color--red">42 899 ₴</p>
                                        </div>
                                        <button class="main-btn blue-small icon-left"><span class="icon"><img src="/assets/images/cart-white.svg" alt=""></span></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-pagination custom-pagiation"></div>
                </div>
                <div class="custom-swiper-btn custom-swiper-btn-prev cart-swiper-btn-prev"><img src="/assets/images/arrow-blue-left.svg" alt=""></div>
                <div class="custom-swiper-btn custom-swiper-btn-next cart-swiper-btn-next"><img src="/assets/images/arrow-blue-right-1.svg" alt=""></div>
            </div>
        </div>  --}}
    @else
        <div class="midle-row p-24 bg--white flex v--start h--between" style="display: flex; align-items: center; justify-content: center;padding: 40px 0">
            <p>{{__t('Кошик порожній')}}</p>
        </div>
    @endif


</div>