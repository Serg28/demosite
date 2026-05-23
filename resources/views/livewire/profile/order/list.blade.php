<div class="account__section">
    <h2 class="account__heading">{{ __t('Мої замовлення') }}</h2>

    <div class="account__search mb-6">
        <input type="search" wire:model.live.debounce.300ms="search"
               placeholder="{{ __t('Пошук за номером, ім\'ям, телефоном...') }}"
               class="input">
    </div>

    @if($list->isEmpty())
        <p class="text-gray-500">{{ __t('Замовлення не знайдено') }}</p>
    @else
        <div class="orders-list">
            @foreach($list as $order)
                <div class="order-card" wire:key="order-{{ $order->id }}">
                    <div class="order-card__header">
                        <span class="order-card__num">#{{ $order->id }}</span>
                        <span class="order-card__date">{{ $order->created_at->format('d.m.Y') }}</span>
                        @if($order->orderStatus)
                            <span class="order-card__status badge"
                                  style="background-color: {{ $order->orderStatus->color }}20; color: {{ $order->orderStatus->color }};">
                                {{ $order->orderStatus->t('title') }}
                            </span>
                        @else
                            <span class="order-card__status">{{ $order->status }}</span>
                        @endif
                    </div>
                    <div class="order-card__body">
                        <span class="order-card__items">
                            {{ $order->products->count() }} {{ __t('товар(ів)') }}
                        </span>
                        <span class="order-card__total">
                            {{ number_format($order->cost, 2) }} {{ __t('грн') }}
                        </span>
                    </div>
                    <div class="order-card__footer">
                        <a href="{{ route('profile.orders.details', $order->id) }}"
                           class="btn btn--ghost btn--sm">
                            {{ __t('Деталі') }}
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $orders->links() }}
        </div>

        @if($orders->hasMorePages())
            <button type="button" class="btn btn--ghost mt-4 w-full"
                    wire:click="showMore"
                    wire:loading.attr="disabled">
                {{ __t('Завантажити ще') }}
            </button>
        @endif
    @endif
</div>
