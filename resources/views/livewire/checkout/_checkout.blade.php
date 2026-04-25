<div>
    <div class="order-wrap" wire:key="checkoutblock">
        <div class="order-wrap__tabs">
            <a href="" class="order-wrap__tab current" data-order-screen="1">{{__t('Замовляю вперше')}}</a>
            <a href="" class="order-wrap__tab" data-order-screen="2">{{__t('Я постійний покупець')}}</a>
        </div>

        <form wire:submit="submit" autocomplete="off">
            @csrf
            <div class="order-wrap__order-screens">
            <div class="left">
                <div class="order-screen order-screen-1">

                        {{--Контактные данные--}}
                        <div class="order-row">
                            <div class="order-row-heading">
                                <span>1</span>
                                <p>{{__t('Ваші контактні дані')}}</p>
                            </div>
                            <div class="input-row">
                                <div class="left">
                                    <input @error('first_name') class="error" @enderror wire:model.live="first_name" type="text" name="first_name" placeholder="{{__t('Ім’я')}} *">
                                </div>
                                <div class="right">
                                    <input @error('last_name') class="error" @enderror wire:model.live="last_name" type="text" name="last_name" placeholder="{{__t('Прізвище')}} *">
                                </div>
                            </div>
                            <div class="input-row">
                                <div class="left">
                                    <input @error('phone') class="error" @enderror wire:model.live="phone" type="tel" name="phone" placeholder="{{__t('Телефон')}} *">
                                </div>
                                <div class="right">
                                    <input @error('email') class="error" @enderror wire:model.live="email" type="email" name="email" placeholder="{{__t('Email')}} *">
                                </div>
                            </div>
                        </div>
                        {{--/Контактные данные--}}

                        {{--Состав заказа--}}
                        <div class="order-row">
                            <div class="order-row-heading">
                                <span>2</span>
                                <p>{{__t('Склад замовлення')}}</p>
                            </div>
                            <livewire:cart.content view="livewire.checkout.products" />
                        </div>
                        {{--/Состав заказа--}}

                        {{--VIN--}}
                        <div class="order-row">
                            <div class="order-row-heading">
                                <span>3</span>
                                <p>{{__t('Введіть VIN код авто для перевірки менеджером відповідності запчастин автомобілю')}}</p>
                            </div>
                            <div class="input-row">
                                <div class="left">
                                    <input type="text" wire:model.lazy="vin" name="vin" placeholder="{{__t('VIN код вашого авто')}}">
                                </div>
                            </div>
                        </div>
                        {{--/VIN--}}

                        <div class="order-row">
                            <div class="order-row-heading">
                                <span>4</span>
                                <p>{{__t('Доставка')}}</p>
                            </div>

{{---------}}
                            {{--
                            <div class="input-row">
                                <div class="left @error('city_id') error @enderror">
                                    <livewire:checkout.select-city model="city_id" placeholder="{{__t('Оберіть місто')}}" :defaultValue="$city_id" />
                                </div>
                            </div> --}}

{{---------}}
                            @if($deliveries->isNotEmpty())

                            <div class="radio-row">

                                {{-- Список методов доставки --}}
                                @foreach($deliveries as $delivery)
                                    <label wire:key="delivery-input-{{$delivery['id']}}" for="delivery-input-{{$delivery['id']}}">
                                        <input type="radio" id="delivery-input-{{$delivery['id']}}" wire:click="selectDelivery({{$delivery['id']}}, '{{$delivery['type']}}')" name="delivery_id" @if($delivery_id == $delivery['id']) checked @endif class="radio">
                                        <span>{{$delivery['title']}}</span>
                                    </label>
                                @endforeach
                                @error('delivery_id') <div class="error">{{__t('Выберіть доставку')}}</div> @enderror
                                {{-- / Список методов доставки --}}

                            </div>
                            {{-- Форма выбранного метода доставки для заполнения --}}
                            {!! $delivery_form !!}
                            {{-- / Форма выбранного метода доставки для заполнения --}}


                            @endif

                        </div>


                        <div class="order-row @error('pay_method_id') error @enderror">
                            <div class="order-row-heading">
                                <span>5</span>
                                <p>{{__t('Спосіб оплати')}}</p>
                            </div>
                            @if($payments && $delivery_id)
                                  {!! $payments !!}
                            @endif

                            @error('pay_method_id') <div class="error">{{__t('Выберіть спосіб оплати')}}</div> @enderror
                        </div>

                        <div class="order-row checkbox-row">
                            <label for="checkbox-call-me">
                                <input type="checkbox" id="checkbox-call-me" wire:model.live="call_me" name="call_me" value="1" checked class="checkbox">
                                <span>{{__t('Не передзванювати мені для підтвердження')}}</span>
                            </label>
                            @if(!app('user'))
                            <label for="checkbox-register-me">
                                <input type="checkbox" id="checkbox-register-me"  wire:model="register_me" value="1" checked class="checkbox">
                                <span>{{__t('Зареєструвати мене для наступних покупок')}}</span>
                            </label>
                            @endif
                        </div>
                        <div class="comment-row" x-data="{ open: false }">
                            <a href="" class="get-comment" @click.prevent="open = ! open">{{__t('Додати коментар до замовлення')}}<img src="/img/ared.svg" alt=""></a>
                            <div class="hidden-comment-row_" x-show="open" wire:transition.out.opacity.duration.200ms>
                                <textarea wire:model="comment" name="comment" placeholder="{{__t('Залиште Ваш коментар')}}"></textarea>
                            </div>
                        </div>

                </div>
                <div class="order-screen order-screen-2">
                    <form action="" autocomplete="off">
                        <div class="order-row small">
                            <div class="order-row-heading">
                                <span>1</span>
                                <p>{{__t('Ваші контактні дані')}}</p>
                            </div>
                            <div class="input-row">
                                <input type="text" name="mail" placeholder="Email">
                                <input type="password" name="Password" placeholder="Пароль">
                            </div>
                            <div class="button-row">
                                <button class="lost-password">Забули пароль?</button>
                            </div>
                            <button class="main-btn main-btn--red">Далі</button>
                        </div>
                        <div class="socs-block">
                            <p>{{__t('Або увійти через соц мережі')}}</p>
                            <div class="flex-row">
                                <button class="socs-button"><img src="img/google.svg" alt="">Google</button>
                                <button class="socs-button"><img src="img/facebook.svg" alt="">Facebook</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="right pos-sticky">

                <livewire:checkout.promocode />

                <div class="order-info">
                    <h3>{{__t('разом')}}</h3>
                    <div class="flex-row">
                        <span>{{$countProducts}} {{ inflection($countProducts, [__('товар'), __('товара'), __('товарів')]) }} {{__t('на суму')}}</span>
                        <p>{{ setting('currency') }}{{$cartSubTotal}}</p>
                    </div>
                    <!--[if BLOCK]><![endif]--> <!-- [tl! highlight] -->
                    @if($promoSaleSum)
                        <div class="flex-row">
                            <span>{!! __t('Знижка') !!}</span>
                            <p>{{ setting('currency') }}{{$promoSaleSum}}</p>
                        </div>
                    @endif
                    <!--[if ENDBLOCK]><![endif]--> <!-- [tl! highlight] -->

                    <!--[if BLOCK]><![endif]--> <!-- [tl! highlight] -->
                    @if($cartDeliveryDesc)
                    <div class="flex-row">
                        <span>{!! __t('Вартість доставки') !!}</span>
                        <p>{{$cartDeliveryDesc}}</p>
                    </div>
                    @endif
                    <!--[if ENDBLOCK]><![endif]--> <!-- [tl! highlight] -->
                    <div class="flex-row bordered">
                        <span>{{__t('До сплати')}}</span>
                        <p><strong>{{ setting('currency') }}{{$cartTotal}}</strong></p>
                    </div>
                    <button type="submit" class="main-btn main-btn--red" @if(count($errors)) disabled @endif>
                        <span wire:loading.class="spinner" wire:target="submit"></span>
                        <span wire:loading.remove wire:target="submit">{{__t('Замовлення підтверджую')}}</span>
                    </button>
                    <p class="policy">{{__t('Реєструючись і оформляючи замовлення, я приймаю умови')}} <a href="{{getUrl('privacy-policy')}}">{{__t('користувальницької угоди')}}</a></p>
                </div>


                {{-- отладка --}}
                <div class="order-info">
                    city_id: {{$city_id}}<br>
                    delivery_id: {{$delivery_id}}<br>
                    np_warehouse_id: {{$np_warehouse_id}}<br>
                    delivery_pickup_point_id: {{$delivery_pickup_point_id}}<br>
                    address: {{$address}}<br>
                    ukrposhta_warehouse_id: {{$ukrposhta_warehouse_id}}<br>
                    justin_warehouse_id: {{$justin_warehouse_id}}<br>
                    meest_warehouse_id: {{$meest_warehouse_id}}<br>
                    pay_method_id: {{$pay_method_id}}<br>
                    --------<br>
                    first_name: {{$first_name}}<br>
                    last_name: {{ $last_name}}<br>
                    phone: {{ $phone}}<br>
                    email: {{ $email}}<br>
                    patronymic: {{ $patronymic}}<br>

                    receiver: {{ $receiver}}<br>
                    receiver_first_name: {{ $receiver_first_name}}<br>
                    receiver_last_name: {{ $receiver_last_name}}<br>
                    receiver_patronymic_name: {{ $receiver_patronymic_name}}<br>
                    receiver_phone: {{$receiver_phone}}<br>
                    receiver_email: {{$receiver_email}}<br>

                    comment: {{ $comment}}<br>
                    call_me: {{ $call_me}}<br>
                    register_me: {{ $register_me}}<br>

                    vin: {{ $vin}}<br>

                </div>
                {{-- / --}}


            </div>

        </div>

        </form>

    </div>


</div>