<div>
    <div class="card p-5 mb-4">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <h2 class="font-bold text-xl">{{ __t('Мої замовлення') }}</h2>
            <input type="search" wire:model.live.debounce.300ms="search"
                   placeholder="{{ __t('Пошук за номером, ім\'ям...') }}"
                   class="field text-sm" style="width:200px;padding:8px 12px">
        </div>
    </div>

    @if($list->isEmpty())
        <x-empty-state
            emoji="📦"
            :title="__t('Замовлень не знайдено')"
            :description="__t('Оформіть перше замовлення в каталозі')"
            :actionUrl="route('catalog.show', 'all')"
            :actionLabel="__t('До каталогу')"
        />
    @else
        <div class="space-y-3">
            @foreach($list as $order)
                <div class="card overflow-hidden hover:shadow-md transition-shadow" wire:key="order-{{ $order->id }}">
                    {{-- Header --}}
                    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between flex-wrap gap-3 bg-gray-50/60">
                        <div class="flex items-center gap-4 flex-wrap">
                            <div>
                                <p class="font-bold">#{{ $order->id }}</p>
                                <p class="text-xs text-ink-muted">{{ $order->created_at->format('d.m.Y') }}</p>
                            </div>
                            <x-status-pill :status="$order->orderStatus" />
                            @if($order->canBeRepaid())
                                <span class="text-xs font-semibold text-amber-600">● {{ __t('Не оплачено') }}</span>
                            @endif
                        </div>
                        <a href="{{ route('profile.orders.details', $order->id) }}"
                           class="text-sm text-brand font-semibold hover:underline">
                            {{ __t('Детальніше') }} →
                        </a>
                    </div>

                    {{-- Thumbnails + delivery info --}}
                    <div class="px-5 pt-4 pb-2 flex items-center gap-2 flex-wrap">
                        @foreach($order->products->take(4) as $item)
                            @if($item->product?->picture)
                                <img src="{{ $item->product->picture }}"
                                     class="w-14 h-14 rounded-lg object-cover border border-gray-100 flex-shrink-0"
                                     alt="">
                            @else
                                <div class="w-14 h-14 rounded-lg bg-gray-100 border border-gray-100 flex items-center justify-center text-gray-300 flex-shrink-0">
                                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9l4-4 4 4 4-4 4 4"/><circle cx="8.5" cy="13.5" r="1.5"/></svg>
                                </div>
                            @endif
                        @endforeach
                        @if($order->products->count() > 4)
                            <div class="w-14 h-14 rounded-lg bg-gray-100 flex items-center justify-center text-xs text-ink-muted font-semibold flex-shrink-0">
                                +{{ $order->products->count() - 4 }}
                            </div>
                        @endif
                        <p class="text-sm text-ink-muted flex-1 min-w-[140px]">
                            {{ $order->products->count() }} {{ __t('позиц.') }}
                            @if($order->delivery instanceof \App\Models\Delivery)
                                · {{ $order->delivery->t('title') }}
                            @endif
                        </p>
                    </div>

                    {{-- Total + actions --}}
                    <div class="px-5 py-4 flex items-center justify-between flex-wrap gap-3">
                        <div>
                            <p class="text-xs text-ink-muted">{{ __t('Сума') }}</p>
                            <p class="font-bold text-lg">@money($order->cost) {{ setting('currency') }}</p>
                        </div>
                        <div class="flex gap-2 flex-wrap">
                            @if($order->canBeRepaid())
                                <form method="POST" action="{{ route('profile.orders.pay', $order->id) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-p btn-sm">💳 {{ __t('Оплатити') }}</button>
                                </form>
                            @endif
                            @if($order->canBeCancelled())
                                <button type="button"
                                        data-js-modal
                                        data-component="profile.order.cancel-order"
                                        data-id="{{ $order->id }}"
                                        class="btn btn-o btn-sm border-red-300 text-red-600 hover:bg-red-50">
                                    {{ __t('Скасувати') }}
                                </button>
                            @endif
                            <form method="POST" action="{{ route('profile.orders.repeat', $order->id) }}">
                                @csrf
                                <button type="submit" class="btn btn-o btn-sm">↻ {{ __t('Повторити') }}</button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if($orders->hasMorePages())
            <button type="button" class="btn btn-o mt-4 w-full"
                    wire:click="showMore"
                    wire:loading.attr="disabled">
                {{ __t('Завантажити ще') }}
            </button>
        @endif
    @endif
</div>
