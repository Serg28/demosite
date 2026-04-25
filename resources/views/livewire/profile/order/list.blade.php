<div class="account-page__content">
    <div class="row flex v--end h--between mb-24 heading-flex-row">
        <h2 class="fsz-28 fw-600 content-heading">{{ __t('Ваші замовлення') }}</h2>

        @include('partials.sorting')
        @include('partials.sorting_mobile')

    </div>
    <div class="account-history" wire:loading.class="opacity-50">

        <livewire:cart.content view="livewire.profile.order.cart" />

            @if(count($list))
                <div class="account-history__wrap flex fd--column mt-24">
                    @foreach($list as $item)
                        @php
                            $firstProduct = $item->products->first();
                            $totalProducts = $item->products->count();
                            $statusesCls = [
                                0 => 'new-order',
                                1 => 'complete',
                                2 => 'done',
                                3 => 'in-progres'
                            ];
                            $statusCLS = $statusesCls[$item->is_online_payed] ?? 'cancele';
                            $paylink = ($item->pay() !== null && !$item->is_online_payed)  ? $item->urlPayment() : null;
                        @endphp
                        <div class="history-row p-16 br--br-4 bg--white {{ $statusCLS }}">
                            <div class="visible-row flex v--center">
                                <div class="left flex fd--column pl-16">
                                    <span class="fsz-14 color--gray">{!!  str_replace(['[id]','[data]'], [$item->id, $item->formatDate()], __t('Замовлення №[id] від [data]')) !!}</span>
                                    <p class="fsz-14 fw-600">{{ $item->statusName() }}</p>
                                </div>
                                <div class="center flex fd--column">
                                    <span class="fsz-14 color--gray">{{ __t('Сума замовлення') }}</span>
                                    <p class="fsz-14 fw-600">@money($item->cost) {{ setting('currency') }}</p>
                                </div>
                                <div class="image">
                                    @foreach($item->products->take(3) as $productItem)
                                        @php
                                            $product = $productItem->product;
                                            $imgPath = !empty($product->picture) ? $product->getImgPath(75, '')
                                                        : glide($product->firstOtherPicture, ['w' => 75]);
                                        @endphp
                                        <img loading="lazy" src="{{ $imgPath }}" alt="{{ e($product->t('title')) }}">
                                    @endforeach
                                    @if($totalProducts > 3)
                                        <span class="color--gray">+{{ $totalProducts - 3 }}</span>
                                    @endif
                                </div>

                                <div class="icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="9" viewBox="0 0 14 9" fill="none">
                                        <path d="M1 1L7 7L13 1" stroke="#2264DC" stroke-width="2"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="hidden-row">
                                <div class="hidden-row-wrap flex v--start h--between">
                                    <div class="left-block">
                                        @if(count($item->products))
                                        <div class="prod-rows flex fd--column">
                                            @foreach($item->products as $productItem)
                                            <div class="prod-row flex v--start">
                                                @php
                                                    $product = $productItem->product;
                                                    $imgPath = !empty($product->picture) ? $product->getImgPath(75, '')
                                                                : glide($product->firstOtherPicture, ['w' => 75]);
                                                @endphp
                                                <div class="img">
                                                    <img loading="lazy" src="{{ $imgPath }}" alt="{{ e($product->t('title')) }}">
                                                </div>
                                                <div class="prdod-info flex fd--column">
                                                    <a href="{{$productItem->product?->getUrl() ?? '#'}}">{{$productItem->product?->t('title') ?? ''}}</a>
                                                    <span class="fsz-12 color--gray">{{ __t('Код товару:') }} {{$productItem->product->code}}</span>
                                                    <div class="row flex v--center h--between">
                                                        <div class="price flex v--center">
                                                            @if(!empty($productItem->product->price_old)) <s class="fsz-14 color--gray">@money($productItem->product->price_old) {{ setting('currency') }}</s>@endif
                                                            <span class="fsz-14 color--red">@money($productItem->price) {{ setting('currency') }}</span>
                                                        </div>
                                                        <div class="num color--gray">х{{$productItem->count}}</div>
                                                        <div class="total-price fw-600">@money($productItem->price) {{ setting('currency') }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                        @endif
                                        <div class="prod-info-block mt-24">
                                            <div class="row flex v--center h--between">
                                                <p class="fsz-14">{{ __t('Сума товарів:') }}</p>
                                                <p class="fsz-14">@money($item->cost_without_sale) {{ setting('currency') }}</p>
                                            </div>
                                            <div class="row flex v--center h--between mt-8">
                                                <p class="fsz-14">{{ __t('Знижка:') }}</p>
                                                <p class="fsz-14">@money($item->getSaleSum()) {{ setting('currency') }}</p>
                                            </div>
                                            {{--
                                            <div class="row flex v--center h--between mt-8">
                                                <p class="fsz-14">{{ __t('Кешбек:') }}</p>
                                                <p class="fsz-13 color--orange">530 {{ setting('currency') }}</p>
                                            </div> --}}
                                            @if($item->tax>0)
                                            <div class="row flex v--center h--between mt-8">
                                                <p class="fsz-14">{{__t('Комісія платіжної системи')}}</p>
                                                <p class="fsz-14">@money($item->tax) {{ setting('currency') }}</p>
                                            </div>
                                            @endif

                                            @php
                                                $deliveryPrice = $item->getDeliveryPrice();
                                                $deliveryPrice = ($deliveryPrice!=='free' &&  $deliveryPrice > 0) ? $deliveryPrice : 0
                                            @endphp
                                            @if($deliveryPrice>0)
                                            <div class="row flex v--center h--between mt-8">
                                                <p class="fsz-14">{{__t('Доставка')}}</p>
                                                <p class="fsz-14">
                                                    @money($deliveryPrice) {{ setting('currency') }}
                                                </p>
                                            </div>
                                            @endif

                                            <div class="row flex v--center h--between mt-16">
                                                <p class="fsz-16 fw-600">{{ __t('Разом до сплати:') }}</p>
                                                <p class="fsz-18 fw-600">@money($item->priceForDocuments) {{ setting('currency') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="right-block br--br-4 bg--light-blue p-24">
                                        <p class="fsz-18 fw-600">{{ __t('Інформація') }}</p>
                                        <div class="copy flex v--center mt-20">
                                            <span class="color--blue fsz-14">{{ __t('ТТН') }} {{$item->tracking_num}}</span>
                                            {{--<div class="copy-btn ml-12"><img src="/assets/images/copy-btn.svg" alt=""></div> --}}
                                        </div>

                                        <p class="mt-16 color--gray">{{ $item->receiver == 'other' ? __t('Данні замовника') : __t('Данні отримувача') }}</p>
                                        <p class="mt-8">{{$item->last_name}} {{$item->first_name}} {{$item->patronymic}}</p>
                                        <a href="tel:{{$item->phone}}" class="flex mt-8 color--black">{{$item->phone}}</a>
                                        <a href="mailto:{{$item->email}}" class="flex mt-8 color--black">{{$item->email}}</a>

                                        @if($item->receiver == 'other')
                                        <p class="mt-16 color--gray">{{ __t('Данні отримувача') }}</p>
                                        <p class="mt-8">{{$item->reveiver_last_name}} {{$item->reveiver_first_name}} {{$item->receiver_patronymic_name}}</p>
                                        <a href="tel:{{$item->receiver_phone}}" class="flex mt-8 color--black">{{$item->receiver_phone}}</a>
                                        <a href="mailto:{{$item->receiver_email}}" class="flex mt-8 color--black">{{$item->receiver_email}}</a>
                                        @endif

                                        <p class="color--gray mt-16">{{ __t('Спосіб доставки') }}</p>
                                        <p class="mt-8">{{$item->delivery ? $item->delivery->t('title') : ''}}<br> {{$item->pickUpTheGoods() ?? ''}}</p>

                                        <p class="mt-16 color--gray">{{ __t('Спосіб оплати') }}</p>
                                        <p class="mt-8">{!! $item->paymentName()!!}</p>

                                        @if($paylink)
                                        <a href="{{$paylink}}" target="_blank" class="main-btn blue-small mt-24">{{ __t('Сплатити онлайн') }}</a>
                                        @endif

                                        {{-- <div class="delete-order">{{ __t('Скасувати замовлення') }}</div> --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                @php
                    $link = geturl(setting('blok-luchshie-predlozheniya-ssylka'));
                    $link2 = geturl(setting('ssylka-na-katalog-akcii'));
                @endphp
                <div class="empty-block">
                    <div class="empty-section p-24">
                        <p class="fw-18 fw-600 mb-16">{{ __t('У вас ще не було покупок') }}</p>
                        <p>{!!  str_replace(['[link]','[link2]'], [$link , $link2], __t('Щоб знайти товар, скористайтесь каталогом або подивіться акційні пропозиції')) !!}</p>
                    </div>

                </div>
            @endif
    </div>
    @if(count($list))
        @include('partials.paginate', ['items' => $orders])
    @endif
</div>