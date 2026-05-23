<x-layouts.shop :title="__t('Замовлення') . ' #' . $order->id">
    <div class="account">
        <div class="container">
            <div class="account__wrap">
                @include('profile.partials.sidebar', ['action' => 'orders'])
                <div class="account__content">
                    <div class="account__section">
                        <div class="account__heading-row">
                            <h2 class="account__heading">{{ __t('Замовлення') }} #{{ $order->id }}</h2>
                            <a href="{{ route('profile.orders') }}" class="btn btn--ghost btn--sm">
                                ← {{ __t('Назад до замовлень') }}
                            </a>
                        </div>

                        <div class="order-detail">
                            <div class="order-detail__meta">
                                <div class="order-detail__meta-item">
                                    <span class="order-detail__label">{{ __t('Дата') }}</span>
                                    <span class="order-detail__value">{{ $order->created_at->format('d.m.Y H:i') }}</span>
                                </div>
                                <div class="order-detail__meta-item">
                                    <span class="order-detail__label">{{ __t('Статус') }}</span>
                                    @if($order->orderStatus)
                                        <span class="badge"
                                              style="background-color: {{ $order->orderStatus->color }}20; color: {{ $order->orderStatus->color }};">
                                            {{ $order->orderStatus->t('title') }}
                                        </span>
                                    @else
                                        <span class="order-detail__value">{{ $order->status }}</span>
                                    @endif
                                </div>
                                @if($order->payMethod instanceof \App\Models\PayMethod)
                                    <div class="order-detail__meta-item">
                                        <span class="order-detail__label">{{ __t('Оплата') }}</span>
                                        <span class="order-detail__value">{{ $order->payMethod->t('title') }}</span>
                                    </div>
                                @endif
                                @if($order->delivery instanceof \App\Models\Delivery)
                                    <div class="order-detail__meta-item">
                                        <span class="order-detail__label">{{ __t('Доставка') }}</span>
                                        <span class="order-detail__value">{{ $order->delivery->t('title') }}</span>
                                    </div>
                                @endif
                            </div>

                            <div class="order-detail__products">
                                <h3 class="order-detail__subtitle">{{ __t('Товари') }}</h3>
                                @foreach($order->products as $item)
                                    <div class="order-detail__product">
                                        <span class="order-detail__product-title">{{ $item->title }}</span>
                                        <span class="order-detail__product-qty">× {{ $item->count }}</span>
                                        <span class="order-detail__product-price">
                                            {{ number_format($item->amount ?: ($item->price * $item->count), 2) }} {{ __t('грн') }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>

                            <div class="order-detail__totals">
                                @if($order->price_delivery > 0)
                                    <div class="order-detail__total-row">
                                        <span>{{ __t('Доставка') }}</span>
                                        <span>{{ number_format($order->price_delivery, 2) }} {{ __t('грн') }}</span>
                                    </div>
                                @endif
                                <div class="order-detail__total-row order-detail__total-row--main">
                                    <span>{{ __t('Разом') }}</span>
                                    <span>{{ number_format($order->cost, 2) }} {{ __t('грн') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.shop>
