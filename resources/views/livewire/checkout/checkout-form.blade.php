<div class="max-w-screen-2xl mx-auto px-4 sm:px-8 py-8">
    <h1 class="text-2xl md:text-3xl font-bold mb-6">{{ __t('Оформлення замовлення') }}</h1>

    <div class="flex gap-6 flex-wrap lg:flex-nowrap">
        {{-- ── ЛІВА КОЛОНКА: форма ─────────────────────────────────────── --}}
        <div class="flex-1 min-w-0 space-y-5">

            {{-- 1. Контактні дані --}}
            <div class="card p-6">
                <h2 class="font-bold text-lg mb-5">{{ __t('Контактні дані') }}</h2>
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium mb-1.5 block">{{ __t("Ім'я") }} *</label>
                        <input wire:model="firstName" type="text" class="field @error('firstName') border-red-400 @enderror">
                        @error('firstName')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="text-sm font-medium mb-1.5 block">{{ __t('Прізвище') }}</label>
                        <input wire:model="lastName" type="text" class="field @error('lastName') border-red-400 @enderror">
                        @error('lastName')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="text-sm font-medium mb-1.5 block">{{ __t('Телефон') }} *</label>
                        <input wire:model="phone" type="tel" class="field @error('phone') border-red-400 @enderror" placeholder="+38(0__)___-__-__">
                        @error('phone')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="text-sm font-medium mb-1.5 block">Email</label>
                        <input wire:model="email" type="email" class="field @error('email') border-red-400 @enderror">
                        @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="mt-4">
                    <label class="text-sm font-medium mb-1.5 block">{{ __t('Коментар') }}</label>
                    <textarea wire:model="comment" class="field resize-none" rows="2" placeholder="{{ __t('Додаткові побажання...') }}"></textarea>
                </div>
            </div>

            {{-- 2. Спосіб доставки --}}
            <div class="card p-6">
                <h2 class="font-bold text-lg mb-5">{{ __t('Спосіб доставки') }}</h2>

                @if($this->deliveries->isEmpty())
                    <p class="text-ink-muted text-sm">{{ __t('Способи доставки тимчасово недоступні') }}</p>
                @else
                    <div class="space-y-2 mb-4">
                        @foreach($this->deliveries as $delivery)
                            <label class="flex items-center justify-between p-4 border-2 rounded-xl cursor-pointer transition-colors
                                {{ $deliveryId === $delivery->id ? 'border-brand bg-brand-light' : 'border-gray-100 hover:border-gray-200' }}">
                                <div class="flex items-center gap-3">
                                    <input type="radio"
                                           wire:model.live="deliveryId"
                                           value="{{ $delivery->id }}"
                                           class="accent-brand">
                                    <div>
                                        <p class="text-sm font-semibold">{{ $delivery->t('title') }}</p>
                                        @if($delivery->free_cost)
                                            <p class="text-xs text-ink-muted">
                                                {{ __t('Безкоштовно від :amount', ['amount' => number_format($delivery->free_cost, 0, '.', ' ')]) }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                                <span class="text-sm font-semibold {{ $delivery->price == 0 ? 'instock' : 'text-ink-muted' }}">
                                    @if($delivery->price == 0)
                                        {{ __t('Безкоштовно') }}
                                    @else
                                        ~@money($delivery->price, 0)
                                    @endif
                                </span>
                            </label>
                        @endforeach
                    </div>
                    @error('deliveryId')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror

                    {{-- DeliverySelector: re-mount при зміні deliveryId --}}
                    @if($this->selectedDelivery !== null)
                        <livewire:checkout.delivery-selector
                            :deliveryId="$deliveryId"
                            :deliverySlug="$this->selectedDelivery->slug"
                            :wire:key="'ds-'.$deliveryId"
                        />
                    @endif
                @endif
            </div>

            {{-- 3. Спосіб оплати --}}
            <div class="card p-6">
                <h2 class="font-bold text-lg mb-5">{{ __t('Спосіб оплати') }}</h2>

                @if($this->payMethods->isEmpty())
                    <p class="text-ink-muted text-sm">{{ __t('Оберіть спосіб доставки для відображення методів оплати') }}</p>
                @else
                    <div class="space-y-2">
                        @foreach($this->payMethods as $payMethod)
                            <label class="flex items-start gap-3 p-4 border-2 rounded-xl cursor-pointer transition-colors
                                {{ $payMethodId === $payMethod->id ? 'border-brand bg-brand-light' : 'border-gray-100 hover:border-gray-200' }}">
                                <input type="radio"
                                       wire:model.live="payMethodId"
                                       value="{{ $payMethod->id }}"
                                       class="mt-0.5 accent-brand flex-shrink-0">
                                <div>
                                    <p class="text-sm font-semibold">{{ $payMethod->t('title') }}</p>
                                    @if($payMethod->commission_percent > 0)
                                        <p class="text-xs text-ink-muted">
                                            {{ __t('Комісія :percent%', ['percent' => $payMethod->commission_percent]) }}
                                        </p>
                                    @endif
                                </div>
                            </label>
                        @endforeach
                    </div>
                    @error('payMethodId')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror

                    {{-- Калькулятор розстрочки --}}
                    @if($this->isInstallmentsGateway && $this->selectedPayMethod !== null)
                        <livewire:checkout.pay-parts-calculator
                            :gatewaySlug="$this->selectedPayMethod->gateway"
                            :orderAmount="$this->total"
                            :wire:key="'ppc-'.$payMethodId.'-'.(int)$this->total"
                        />
                    @endif
                @endif
            </div>

        </div>

        {{-- ── ПРАВА КОЛОНКА: підсумок ─────────────────────────────────── --}}
        <div class="w-full lg:w-80 flex-shrink-0">
            <div class="card p-6 sticky top-24">
                <h3 class="font-bold text-lg mb-4">{{ __t('Ваше замовлення') }}</h3>

                {{-- Список товарів --}}
                <div class="space-y-3 mb-4 max-h-48 overflow-y-auto">
                    @foreach($this->cartItems as $item)
                        <div class="flex gap-3">
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
                <div class="mb-4">
                    @if($promoApplied)
                        <div class="flex items-center justify-between p-2 bg-green-50 border border-green-200 rounded-lg">
                            <p class="text-green-700 text-sm font-medium">{{ $promoMessage }}</p>
                            <button wire:click="removePromoCode" class="text-green-500 hover:text-red-500 ml-2 text-sm">✕</button>
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
                        @if($promoMessage && !$promoApplied)
                            <p class="text-red-500 text-xs mt-1.5">{{ $promoMessage }}</p>
                        @endif
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
                            <span>{{ __t('Знижка') }}</span>
                            <span>−@money($promoDiscount)</span>
                        </div>
                    @endif

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

                {{-- Кнопка оформлення --}}
                <button wire:click="placeOrder"
                        wire:loading.attr="disabled"
                        wire:target="placeOrder"
                        :disabled="{{ $this->cartCount === 0 ? 'true' : 'false' }}"
                        class="btn btn-p w-full mt-5 text-base {{ $this->cartCount === 0 ? 'opacity-50 cursor-not-allowed' : '' }}">
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
