<div class="max-w-screen-2xl mx-auto px-4 sm:px-8 py-8">
    <h1 class="text-2xl md:text-3xl font-bold mb-6">{{ __t('Оформлення замовлення') }}</h1>

    <div class="flex gap-6 flex-wrap lg:flex-nowrap">

        {{-- ── ЛІВА КОЛОНКА ──────────────────────────────────────────────── --}}
        <div class="flex-1 min-w-0 space-y-5">

            @guest
            <div class="flex gap-3">
                <button type="button" class="btn btn-o">{{ __t('Я новий покупець') }}</button>
                <button type="button" class="btn btn-o" data-component="auth.by-otp.login">{{ __t('Я постійний клієнт') }}</button>
            </div>
            @endguest

            {{-- 1. Контактні дані ────────────────────────────────────────── --}}
            <div class="card p-6">
                <h2 class="font-bold text-lg mb-5">{{ __t('Контактні дані') }}</h2>

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium mb-1.5 block">{{ __t("Ім'я") }} *</label>
                        <input wire:model="firstName" type="text"
                               class="field @error('firstName') border-red-400 @enderror">
                        @error('firstName')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="text-sm font-medium mb-1.5 block">{{ __t('Прізвище') }}</label>
                        <input wire:model="lastName" type="text"
                               class="field @error('lastName') border-red-400 @enderror">
                        @error('lastName')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="text-sm font-medium mb-1.5 block">{{ __t('Телефон') }} *</label>
                        <input wire:model="phone" type="tel"
                               class="field @error('phone') border-red-400 @enderror"
                               placeholder="+38(0__)___-__-__">
                        @error('phone')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="text-sm font-medium mb-1.5 block">Email</label>
                        <input wire:model="email" type="email"
                               class="field @error('email') border-red-400 @enderror">
                        @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="mt-4">
                    <x-checkout.autocomplete
                        :label="__t('Місто') . ' *'"
                        :placeholder="__t('Введіть назву міста...')"
                        :search-url="route('api.v1.checkout.cities')"
                        :selected-id="$cityId"
                        :selected-title="$cityTitle"
                        select-method="selectCity"
                        clear-method="clearCity"
                        :min-chars="2"
                    />
                    @error('cityId')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="mt-4">
                    <label class="text-sm font-medium mb-1.5 block">{{ __t('Коментар') }}</label>
                    <textarea wire:model="comment" class="field resize-none" rows="2"
                              placeholder="{{ __t('Додаткові побажання...') }}"></textarea>
                </div>

                {{-- Одержувач — Alpine toggle (без Livewire roundtrip) --}}
                <div class="mt-4" x-data="{ receiver: $wire.entangle('receiver') }">
                    <p class="text-sm font-medium mb-2">{{ __t('Одержувач') }}</p>
                    <div class="flex gap-3 mb-3">
                        <button type="button"
                                @click="receiver = 'user'"
                                :class="receiver === 'user' ? 'btn-p' : 'btn-o'"
                                class="btn btn-sm">
                            {{ __t('Я') }}
                        </button>
                        <button type="button"
                                @click="receiver = 'other'"
                                :class="receiver === 'other' ? 'btn-p' : 'btn-o'"
                                class="btn btn-sm">
                            {{ __t('Інша особа') }}
                        </button>
                    </div>

                    <div x-show="receiver === 'other'"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         style="display:none">
                        <div class="grid sm:grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm font-medium mb-1.5 block">{{ __t("Ім'я") }} *</label>
                                <input wire:model="receiverFirstName" type="text"
                                       class="field @error('receiverFirstName') border-red-400 @enderror">
                                @error('receiverFirstName')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="text-sm font-medium mb-1.5 block">{{ __t('Прізвище') }}</label>
                                <input wire:model="receiverLastName" type="text"
                                       class="field @error('receiverLastName') border-red-400 @enderror">
                                @error('receiverLastName')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="text-sm font-medium mb-1.5 block">{{ __t('По батькові') }}</label>
                                <input wire:model="receiverPatronymic" type="text"
                                       class="field @error('receiverPatronymic') border-red-400 @enderror">
                                @error('receiverPatronymic')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="text-sm font-medium mb-1.5 block">{{ __t('Телефон') }} *</label>
                                <input wire:model="receiverPhone" type="tel"
                                       class="field @error('receiverPhone') border-red-400 @enderror"
                                       placeholder="+38(0__)___-__-__">
                                @error('receiverPhone')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div class="sm:col-span-2">
                                <label class="text-sm font-medium mb-1.5 block">Email</label>
                                <input wire:model="receiverEmail" type="email"
                                       class="field @error('receiverEmail') border-red-400 @enderror">
                                @error('receiverEmail')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input wire:model="callMe" type="checkbox"
                               class="rounded border-gray-300 text-brand focus:ring-brand">
                        <span class="text-sm">{{ __t('Передзвоніть мені для підтвердження замовлення') }}</span>
                    </label>
                </div>
            </div>

            {{-- 2. Спосіб доставки ──────────────────────────────────────── --}}
            <div class="card p-6">
                <h2 class="font-bold text-lg mb-5">{{ __t('Спосіб доставки') }}</h2>

                @if($this->deliveries->isEmpty())
                    <p class="text-ink-muted text-sm">
                        {{ $cityId ? __t('Способи доставки тимчасово недоступні') : __t('Оберіть місто для відображення доставок') }}
                    </p>
                @else
                    <div class="space-y-2 mb-4">
                        @foreach($this->deliveries as $delivery)
                            <x-checkout.delivery-option
                                :delivery="$delivery"
                                :selected="$deliveryId === $delivery->id"
                                :wire:key="'dopt-'.$delivery->id"
                            />
                        @endforeach
                    </div>
                    @error('deliveryId')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror

                    @if($this->selectedDelivery !== null)
                        <livewire:checkout.delivery-selector
                            :deliveryId="$deliveryId"
                            :deliverySlug="$this->selectedDelivery->slug"
                            :cityId="$cityId"
                            :wire:key="'ds-'.$deliveryId"
                        />
                    @endif
                @endif
            </div>

            {{-- 3. Спосіб оплати ─────────────────────────────────────────── --}}
            <div class="card p-6">
                <h2 class="font-bold text-lg mb-5">{{ __t('Спосіб оплати') }}</h2>

                @if($this->payMethods->isEmpty())
                    <p class="text-ink-muted text-sm">
                        {{ $deliveryId ? __t('Методи оплати тимчасово недоступні') : __t('Оберіть спосіб доставки для відображення методів оплати') }}
                    </p>
                @else
                    <div class="space-y-2">
                        @foreach($this->payMethods as $payMethod)
                            <x-checkout.payment-option
                                :pay-method="$payMethod"
                                :selected="$payMethodId === $payMethod->id"
                                :wire:key="'popt-'.$payMethod->id"
                            />
                        @endforeach
                    </div>
                    @error('payMethodId')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror

                    @if($this->selectedPayMethod !== null)
                        @php $gateway = $this->selectedPayMethod->gateway; @endphp
                        @includeIf('livewire.checkout.payment.' . $gateway, [
                            'gateway'     => $gateway,
                            'payMethodId' => $payMethodId,
                            'orderAmount' => $this->total,
                        ])
                    @endif
                @endif
            </div>

        </div>

        {{-- ── ПРАВА КОЛОНКА: підсумок ─────────────────────────────────── --}}
        <div class="w-full lg:w-80 flex-shrink-0">
            <div class="card p-6 sticky top-24">

                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-lg">{{ __t('Ваше замовлення') }}</h3>
                    <a href="/cart" class="text-xs text-brand underline hover:no-underline">
                        {{ __t('Редагувати') }}
                    </a>
                </div>

                <div class="space-y-3 mb-4 max-h-48 overflow-y-auto">
                    @foreach($this->cartItems as $item)
                        <div class="flex gap-3" wire:key="cart-{{ $item->rowId }}">
                            <div class="w-12 h-12 bg-gray-100 rounded-lg flex-shrink-0 overflow-hidden">
                                @php
                                    $thumb = is_object($item->model) && method_exists($item->model, 'getFirstMediaUrl')
                                        ? $item->model->getFirstMediaUrl('products', 'thumb')
                                        : '';
                                @endphp
                                @if($thumb)
                                    <img src="{{ $thumb }}" alt="{{ $item->name }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-300 text-xs">фото</div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-medium leading-snug line-clamp-2">{{ $item->name }}</p>
                                <p class="text-xs text-ink-muted mt-1">
                                    {{ $item->qty }} × @money($item->price)
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Промокод --}}
                <div class="mb-3">
                    @if($promoApplied)
                        <div class="flex items-center justify-between p-2 bg-green-50 border border-green-200 rounded-lg">
                            <p class="text-green-700 text-sm font-medium">{{ $promoMessage }}</p>
                            <button wire:click="removePromoCode"
                                    class="text-green-500 hover:text-red-500 ml-2 text-sm">✕</button>
                        </div>
                    @else
                        <div class="flex gap-2">
                            <input wire:model="promoCodeInput"
                                   wire:keydown.enter="applyPromoCode"
                                   type="text"
                                   placeholder="{{ __t('Промокод') }}"
                                   class="field flex-1 text-sm"
                                   style="padding:8px 12px">
                            <button wire:click="applyPromoCode"
                                    class="btn btn-o btn-sm"
                                    wire:loading.attr="disabled">
                                {{ __t('ОК') }}
                            </button>
                        </div>
                        @if($promoMessage && ! $promoApplied)
                            <p class="text-red-500 text-xs mt-1.5">{{ $promoMessage }}</p>
                        @endif
                    @endif
                </div>

                {{-- Дисконтна картка --}}
                <div class="mb-3">
                    @if($cardApplied)
                        <div class="flex items-center justify-between p-2 bg-green-50 border border-green-200 rounded-lg">
                            <p class="text-green-700 text-sm font-medium">{{ $cardMessage }}</p>
                            <button wire:click="removeDiscountCard"
                                    class="text-green-500 hover:text-red-500 ml-2 text-sm">✕</button>
                        </div>
                    @else
                        <div class="flex gap-2">
                            <input wire:model="cardInput"
                                   wire:keydown.enter="applyDiscountCard"
                                   type="text"
                                   placeholder="{{ __t('Дисконтна картка') }}"
                                   class="field flex-1 text-sm"
                                   style="padding:8px 12px">
                            <button wire:click="applyDiscountCard"
                                    class="btn btn-o btn-sm"
                                    wire:loading.attr="disabled">
                                {{ __t('ОК') }}
                            </button>
                        </div>
                        @if($cardMessage && ! $cardApplied)
                            <p class="text-red-500 text-xs mt-1.5">{{ $cardMessage }}</p>
                        @endif
                    @endif
                </div>

                {{-- Подарунковий сертифікат --}}
                <div class="mb-4">
                    @forelse($this->appliedGiftCertificates as $cert)
                        <div class="flex items-center justify-between p-2 bg-purple-50 border border-purple-200 rounded-lg mb-2"
                             wire:key="gc-{{ $cert['code'] }}">
                            <span class="text-purple-700 text-sm font-medium">
                                {{ $cert['code'] }} — @money($cert['amount'])
                            </span>
                            <button wire:click="removeGiftCode('{{ $cert['code'] }}')"
                                    class="text-purple-400 hover:text-red-500 ml-2 text-sm">✕</button>
                        </div>
                    @empty
                    @endforelse

                    <div class="flex gap-2">
                        <input wire:model="giftCodeInput"
                               wire:keydown.enter="applyGiftCode"
                               type="text"
                               placeholder="{{ __t('Подарунковий сертифікат') }}"
                               class="field flex-1 text-sm"
                               style="padding:8px 12px">
                        <button wire:click="applyGiftCode"
                                class="btn btn-o btn-sm"
                                wire:loading.attr="disabled">
                            {{ __t('ОК') }}
                        </button>
                    </div>
                    @if($giftMessage)
                        <p class="text-xs mt-1.5 {{ $this->appliedGiftCertificates->isEmpty() ? 'text-red-500' : 'text-green-600' }}">
                            {{ $giftMessage }}
                        </p>
                    @endif
                </div>

                {{-- Підсумок --}}
                <div class="space-y-2 text-sm border-t pt-4">
                    <div class="flex justify-between">
                        <span class="text-ink-muted">
                            {{ $this->cartCount }} {{ __t('товар(и)') }}
                        </span>
                        <span>@money($this->subtotal)</span>
                    </div>

                    @if($promoDiscount > 0)
                        <div class="flex justify-between text-green-600">
                            <span>{{ __t('Промокод') }}</span>
                            <span>−@money($promoDiscount)</span>
                        </div>
                    @endif

                    @if($cardDiscount > 0)
                        <div class="flex justify-between text-green-600">
                            <span>{{ __t('Дисконтна картка') }}</span>
                            <span>−@money($cardDiscount)</span>
                        </div>
                    @endif

                    @foreach($this->appliedGiftCertificates as $cert)
                        <div class="flex justify-between text-purple-500 text-xs"
                             wire:key="gcs-{{ $cert['code'] }}">
                            <span>{{ __t('Сертифікат :code', ['code' => $cert['code']]) }}</span>
                            <span>@money($cert['amount']) {{ __t('(при отриманні)') }}</span>
                        </div>
                    @endforeach

                    @if($this->deliveryPrice > 0)
                        <div class="flex justify-between">
                            <span class="text-ink-muted">{{ __t('Доставка') }}</span>
                            <span>@money($this->deliveryPrice)</span>
                        </div>
                    @elseif($this->selectedDelivery !== null)
                        <div class="flex justify-between text-green-600">
                            <span>{{ __t('Доставка') }}</span>
                            <span>{{ __t('Безкоштовно') }}</span>
                        </div>
                    @endif

                    @if($this->commissionAmount > 0)
                        <div class="flex justify-between">
                            <span class="text-ink-muted">{{ __t('Комісія') }}</span>
                            <span>@money($this->commissionAmount)</span>
                        </div>
                    @endif

                    <div class="flex justify-between font-bold text-base border-t pt-2 mt-2">
                        <span>{{ __t('До сплати') }}</span>
                        <span class="text-brand">@money($this->total)</span>
                    </div>
                </div>

                <button wire:click="placeOrder"
                        wire:loading.attr="disabled"
                        wire:target="placeOrder"
                        :disabled="! ($wire.contactsStepValid && $wire.deliveryStepValid && $wire.paymentStepValid)"
                        class="btn btn-p w-full mt-5 text-base"
                        :class="! ($wire.contactsStepValid && $wire.deliveryStepValid && $wire.paymentStepValid) ? 'opacity-50 cursor-not-allowed' : ''">
                    <span wire:loading.remove wire:target="placeOrder">{{ __t('Оформити замовлення') }}</span>
                    <span wire:loading wire:target="placeOrder">{{ __t('Обробляємо...') }}</span>
                </button>

                <p class="text-xs text-ink-muted text-center mt-3">
                    {{ __t('Підтверджуючи, я погоджуюсь з') }}
                    <a href="#" class="text-brand underline">{{ __t('умовами') }}</a>
                </p>
            </div>
        </div>

    </div>
</div>
