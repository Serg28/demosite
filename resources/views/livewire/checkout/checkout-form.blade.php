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
                        <input wire:model.blur="firstName" type="text"
                               class="field @error('firstName') border-red-400 @enderror"
                               autocomplete="given-name">
                        @error('firstName')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="text-sm font-medium mb-1.5 block">{{ __t('Прізвище') }}</label>
                        <input wire:model.blur="lastName" type="text"
                               class="field @error('lastName') border-red-400 @enderror"
                               autocomplete="family-name">
                        @error('lastName')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <x-phone-input wire:model="phone" :label="__t('Телефон') . ' *'" />
                    <x-email-input wire:model="email" label="Email" />
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
                            <x-phone-input wire:model="receiverPhone" :label="__t('Телефон') . ' *'" />
                            <div class="sm:col-span-2">
                                <x-email-input wire:model="receiverEmail" label="Email" />
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

                @guest
                <div class="mt-3">
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input wire:model="registerMe" type="checkbox"
                               class="rounded border-gray-300 text-brand focus:ring-brand">
                        <span class="text-sm">{{ __t('Створити акаунт після оформлення замовлення') }}</span>
                    </label>
                </div>
                @endguest
            </div>

            {{-- 2. Спосіб доставки ──────────────────────────────────────── --}}
            <div class="card p-6" :class="! $wire.contactsStepValid ? 'opacity-60' : ''">
                <h2 class="font-bold text-lg {{ $contactsStepValid ? 'mb-5' : '' }}">
                    {{ __t('Спосіб доставки') }}
                    @if(! $contactsStepValid)
                        <span class="text-xs font-normal text-ink-muted ml-2">{{ __t('— заповніть контактні дані') }}</span>
                    @endif
                </h2>

                @if($contactsStepValid)
                    @if($this->deliveries->isEmpty())
                        <p class="text-ink-muted text-sm">
                            {{ __t('Способи доставки тимчасово недоступні') }}
                        </p>
                    @else
                        <div class="space-y-2">
                            @foreach($this->deliveries as $delivery)
                                <x-checkout.delivery-option
                                    :delivery="$delivery"
                                    :selected="$deliveryId === $delivery->id"
                                    :wire:key="'dopt-'.$delivery->id"
                                />
                                @if($deliveryId === $delivery->id)
                                    <div class="ml-4 mt-1 mb-2" wire:key="'sub-'.$delivery->id">
                                        <livewire:checkout.delivery-selector
                                            :deliveryId="$deliveryId"
                                            :deliverySlug="$delivery->slug"
                                            :cityId="$cityId"
                                            :wire:key="'ds-'.$deliveryId"
                                        />
                                    </div>
                                @endif
                            @endforeach
                        </div>
                        @error('deliveryId')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    @endif
                @endif
            </div>

            {{-- 3. Спосіб оплати ─────────────────────────────────────────── --}}
            <div class="card p-6" :class="! $wire.deliveryStepValid ? 'opacity-60' : ''">
                <h2 class="font-bold text-lg {{ $deliveryStepValid ? 'mb-5' : '' }}">
                    {{ __t('Спосіб оплати') }}
                    @if(! $deliveryStepValid)
                        <span class="text-xs font-normal text-ink-muted ml-2">{{ __t('— оберіть спосіб доставки') }}</span>
                    @endif
                </h2>

                @if($deliveryStepValid)
                    @if($this->payMethods->isEmpty())
                        <p class="text-ink-muted text-sm">
                            {{ __t('Методи оплати тимчасово недоступні') }}
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
                @endif
            </div>

        </div>

        {{-- ── ПРАВА КОЛОНКА: підсумок ─────────────────────────────────── --}}
        <div class="w-full lg:w-80 flex-shrink-0">
            <div class="card p-6 sticky top-24" x-data="{ editing: false }">

                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-lg">{{ __t('Ваше замовлення') }}</h3>
                    <button
                        @click="editing = !editing"
                        type="button"
                        class="text-xs text-brand underline hover:no-underline"
                        x-text="editing ? '{{ __t('Готово') }}' : '{{ __t('Редагувати') }}'"
                    ></button>
                </div>

                <div class="space-y-3 mb-4 max-h-64 overflow-y-auto">
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
                                {{-- Звичайний вигляд --}}
                                <p class="text-xs text-ink-muted mt-1" x-show="!editing">
                                    {{ $item->qty }} × @money($item->price)
                                </p>
                                {{-- Режим редагування --}}
                                <div class="flex items-center gap-2 mt-1" x-show="editing" x-cloak>
                                    <div class="flex items-center border border-gray-200 rounded-lg overflow-hidden">
                                        <button
                                            type="button"
                                            wire:click="updateCartQty('{{ $item->rowId }}', {{ $item->qty - 1 }})"
                                            class="w-7 h-7 flex items-center justify-center text-gray-500 hover:bg-gray-100 text-sm font-bold"
                                        >−</button>
                                        <span class="w-7 text-center text-xs font-medium">{{ $item->qty }}</span>
                                        <button
                                            type="button"
                                            wire:click="updateCartQty('{{ $item->rowId }}', {{ $item->qty + 1 }})"
                                            class="w-7 h-7 flex items-center justify-center text-gray-500 hover:bg-gray-100 text-sm font-bold"
                                        >+</button>
                                    </div>
                                    <span class="text-xs text-ink-muted">@money($item->price)</span>
                                    <button
                                        type="button"
                                        wire:click="removeCartItem('{{ $item->rowId }}')"
                                        class="ml-auto text-red-400 hover:text-red-600 text-xs"
                                        aria-label="{{ __t('Видалити') }}"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
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
                        @if($autoDetectedCard !== null)
                            <div class="mt-2 p-2 bg-blue-50 border border-blue-200 rounded-lg flex items-center justify-between">
                                <span class="text-blue-700 text-sm">
                                    {{ __t('Знайдено дисконтну картку. Знижка: :amount', ['amount' => number_format($autoDetectedCard['discount'], config('cart.format.decimals', 0), config('cart.format.decimal_point', '.'), config('cart.format.thousand_separator', ' '))]) }}
                                </span>
                                <button wire:click="applyAutoDetectedCard"
                                        class="btn btn-sm btn-brand ml-2">
                                    {{ __t('Застосувати') }}
                                </button>
                            </div>
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
                        :disabled="! ($wire.contactsStepValid && $wire.deliveryStepValid && $wire.paymentStepValid) || $wire.cartHasBlockingItems || $wire.cartCount === 0"
                        class="btn btn-p w-full mt-5 text-base"
                        :class="(! ($wire.contactsStepValid && $wire.deliveryStepValid && $wire.paymentStepValid) || $wire.cartHasBlockingItems || $wire.cartCount === 0) ? 'opacity-50 cursor-not-allowed' : ''">
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

    {{-- ── Full-page preloader (placeOrder) ────────────────────────────────── --}}
    <div wire:loading wire:target="placeOrder"
         class="fixed inset-0 z-50 bg-white/80 flex items-center justify-center">
        <svg class="animate-spin w-12 h-12 text-brand" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
        </svg>
    </div>

    {{-- ── Cart Guard Modal (out of stock) ──────────────────────────────────── --}}
    <div x-data="cartGuardModal()"
         x-on:cart-guard-blocking.window="open($event.detail.items)"
         x-cloak>
        <template x-if="isOpen">
            <div class="relative z-50" role="dialog" aria-modal="true">
                <div class="fixed inset-0 bg-black/50" @click="close()"></div>
                <div class="fixed inset-0 flex items-center justify-center p-4">
                    <div class="relative w-full max-w-md bg-white rounded-xl shadow-2xl p-6"
                         @click.stop
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100">
                        <button @click="close()" class="absolute top-4 right-4 text-ink-light hover:text-ink" aria-label="{{ __t('Закрити') }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                        <h3 class="text-lg font-semibold mb-4">{{ __t('Товари недоступні') }}</h3>
                        <ul class="space-y-2 mb-5">
                            <template x-for="item in items" :key="item.rowId">
                                <li class="flex items-center justify-between gap-3 text-sm">
                                    <span x-text="item.name" class="flex-1 truncate"></span>
                                    <button @click="removeItem(item.rowId)"
                                            class="text-red-500 hover:text-red-700 text-xs shrink-0 underline">
                                        {{ __t('Видалити') }}
                                    </button>
                                </li>
                            </template>
                        </ul>
                        <button @click="close()" class="btn btn-o w-full">{{ __t('Закрити') }}</button>
                    </div>
                </div>
            </div>
        </template>
    </div>

    @script
    <script>
        Alpine.data('cartGuardModal', () => ({
            isOpen: false,
            items: [],
            open(items) {
                this.items = items;
                this.isOpen = true;
            },
            close() {
                this.isOpen = false;
            },
            removeItem(rowId) {
                this.$wire.removeCartItem(rowId);
                this.items = this.items.filter(i => i.rowId !== rowId);
                if (this.items.length === 0) {
                    this.isOpen = false;
                }
            },
        }));

        $watch('$wire.cartGuardResult', (result) => {
            if (result && result.price_changed && result.price_changed.length > 0) {
                const names = result.price_changed.map(i => i.name).join(', ');
                $dispatch('notify', { type: 'warning', message: '{{ __t('Ціни змінились') }}: ' + names });
            }
        });
    </script>
    @endscript

</div>
