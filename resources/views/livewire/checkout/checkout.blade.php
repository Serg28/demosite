<div class="container">
            <h2 class="fsz-28 fw-600 chechkout-heading">{{__t('Оформити замовлення')}}</h2>
            <div class="checkout__wrap mt-24 flex v--start h--between">
                <form wire:submit="submit" autocomplete="off"  class="content flex fd--column" x-data="{ formStep: 1 }">
                    @csrf

                    {{-- Контактные данные и город --}}
                    @include('livewire.checkout.sections.contacts')
                    {{-- /Контактные данные и город --}}

                    {{-- Доставка --}}
                    @include('livewire.checkout.sections.deliveries')
                    {{-- /Доставка --}}

                    {{-- Оплата --}}
                    @include('livewire.checkout.sections.payments')
                    {{-- /Оплата --}}

                </form>
                <div class="right-side-bar br--br-4 bg--white">


                    <livewire:cart.content view="livewire.checkout.products" />

                    <div class="bottom-row p-16 flex fd--column">
                        <livewire:checkout.promocode />

                        <div class="info-rows flex fd--column">
                            <div class="info-row flex v--center h--between">
                                <p class="fsz-14">{{__t('Сума товарів')}}:</p>
                                <span class="fsz-14">{{$cartSubTotal}} {{ setting('currency') }}</span>
                            </div>
                            <!--[if BLOCK]><![endif]--> <!-- [tl! highlight] -->
                            @if($promoSaleSum)
                            <div class="info-row flex v--center h--between">
                                <p class="fsz-14">{{__t('Знижка')}}:</p>
                                <span class="fsz-14">{{$promoSaleSum}} {{ setting('currency') }}</span>
                            </div>
                            @endif
                            <!--[if ENDBLOCK]><![endif]--> <!-- [tl! highlight] -->

                            <!--[if BLOCK]><![endif]--> <!-- [tl! highlight] -->
                            @if($cartDeliveryDesc)
                            <div class="info-row flex v--center h--between">
                                <p class="fsz-14">{{__t('Доставка')}}:</p>
                                <span class="fsz-14">{{$cartDeliveryDesc}}</span>
                            </div>
                            @endif
                            <!--[if ENDBLOCK]><![endif]--> <!-- [tl! highlight] -->

                            <!--[if BLOCK]><![endif]--> <!-- [tl! highlight] -->
                            @if($paymentTaxSum)
                                <div class="info-row flex v--center h--between">
                                    <p class="fsz-14">{{__t('Комісія платіжної системи')}}:</p>
                                    <span class="fsz-14">{{$paymentTaxSum}} {{ setting('currency') }}</span>
                                </div>
                            @endif
                            <!--[if ENDBLOCK]><![endif]--> <!-- [tl! highlight] -->

                            {{-- TODO: кешбек --}}
                            {{--<div class="info-row flex v--center h--between">
                                <p class="fsz-14">Буде нараховано кешбеку:</p>
                                <span class="fsz-13 orange flex v--center fw-600"><img src="assets/images/cb.svg" alt="">530 ₴</span>
                            </div>
                            <div class="info-row flex v--center h--between">
                                <p class="fsz-14">Списано кешбеку:</p>
                                <span class="fsz-14">815 ₴</span>
                            </div>--}}
                        </div>
                        <div class="final-price flex v--center h--between pt-16">
                            <p class="fw-600">{{__t('Разом до сплати')}}:</p>
                            <span class="fsz-18 fw-600">{{$cartTotal}} {{ setting('currency') }}</span>
                        </div>
                    </div>
                </div>
            </div>

    {{-- отладка --}}
    <!--<div class="order-info">
        city_id: {{$city_id}}<br>
        delivery_id: {{$delivery_id}}<br>
        np_warehouse_id: {{$np_warehouse_id}}<br>
        delivery_pickup_point_id: {{$delivery_pickup_point_id}}<br>
        address: {{$address}}<br>
        street: {{$street}}<br>
        other_street: {{$other_street}}<br>
        ukrposhta_warehouse_id: {{$ukrposhta_warehouse_id}}<br>
        justin_warehouse_id: {{$justin_warehouse_id}}<br>
        meest_warehouse_id: {{$meest_warehouse_id}}<br>
        pay_method_id: {{$pay_method_id}}<br>
        payparts_count: {{$payparts_count}}<br>
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

    </div> -->
    {{-- / --}}
        </div>