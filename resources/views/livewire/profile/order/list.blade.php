<div>
    <div class="card p-5 mb-4">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <h2 class="font-bold text-xl">
                {{ __t('Мої замовлення') }}
            </h2>
            <input type="search" wire:model.live.debounce.300ms="search"
                   placeholder="{{ __t('Пошук за номером, ім\'ям...') }}"
                   class="field text-sm" style="width:200px;padding:8px 12px">
        </div>
    </div>

    @if($list->isEmpty())
        <div class="card p-12 text-center">
            <div class="text-5xl mb-3">📦</div>
            <p class="text-ink-muted mb-4">{{ __t('Замовлень не знайдено') }}</p>
            <a href="/" class="btn btn-p">{{ __t('До каталогу') }}</a>
        </div>
    @else
        <div class="space-y-3">
            @foreach($list as $order)
                <div class="card overflow-hidden hover:shadow-md transition-shadow" wire:key="order-{{ $order->id }}">
                    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between flex-wrap gap-3 bg-gray-50/60">
                        <div class="flex items-center gap-4 flex-wrap">
                            <div>
                                <p class="font-bold">#{{ $order->id }}</p>
                                <p class="text-xs text-ink-muted">{{ $order->created_at->format('d.m.Y') }}</p>
                            </div>
                            @if($order->orderStatus)
                                <span class="badge"
                                      style="background-color: {{ $order->orderStatus->color }}20; color: {{ $order->orderStatus->color }};">
                                    {{ $order->orderStatus->t('title') }}
                                </span>
                            @else
                                <span class="badge">{{ $order->status }}</span>
                            @endif
                        </div>
                        <a href="{{ route('profile.orders.details', $order->id) }}"
                           class="text-sm text-brand font-semibold hover:underline">
                            {{ __t('Деталі') }} →
                        </a>
                    </div>
                    <div class="px-5 py-4 flex items-center justify-between flex-wrap gap-3">
                        <div class="text-sm text-ink-muted">
                            {{ $order->products->count() }} {{ __t('товар(ів)') }}
                        </div>
                        <div>
                            <p class="text-xs text-ink-muted">{{ __t('Сума') }}</p>
                            <p class="font-bold text-lg">@money($order->cost) {{ setting('currency') }}</p>
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
