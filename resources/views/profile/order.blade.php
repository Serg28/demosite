<x-layouts.shop :title="__t('Замовлення') . ' #' . $order->id">
    <div class="max-w-[1264px] mx-auto px-4 sm:px-8 py-8">
        <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
            <h1 class="text-2xl md:text-3xl font-bold">{{ __t('Особистий кабінет') }}</h1>
            <nav class="text-sm text-ink-muted">
                <a href="{{ route('home') }}" class="hover:text-brand">{{ __t('Головна') }}</a>
                <span class="mx-1">›</span>
                <a href="{{ route('profile.orders') }}" class="hover:text-brand">{{ __t('Замовлення') }}</a>
                <span class="mx-1">›</span>
                <span class="text-ink font-medium">#{{ $order->id }}</span>
            </nav>
        </div>
        <div class="flex gap-6 flex-wrap md:flex-nowrap">
            @include('profile.partials.sidebar', ['action' => 'orders'])
            <div class="flex-1 min-w-0">
                <div class="card p-6 mb-4">
                    <div class="flex items-start justify-between flex-wrap gap-4 mb-4">
                        <div>
                            <p class="text-xs text-ink-muted mb-1">{{ __t('Замовлення') }}</p>
                            <h2 class="text-2xl font-bold mb-1">#{{ $order->id }}</h2>
                            <p class="text-sm text-ink-muted">{{ $order->created_at->format('d.m.Y H:i') }}</p>
                        </div>
                        <div class="flex items-center gap-3 flex-wrap">
                            @if($order->orderStatus)
                                <span class="badge"
                                      style="background-color: {{ $order->orderStatus->color }}20; color: {{ $order->orderStatus->color }};">
                                    {{ $order->orderStatus->t('title') }}
                                </span>
                            @else
                                <span class="badge">{{ $order->status }}</span>
                            @endif
                            <a href="{{ route('profile.orders') }}" class="btn btn-o btn-sm">
                                ← {{ __t('До замовлень') }}
                            </a>
                        </div>
                    </div>

                    {{-- Meta info --}}
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 py-4 border-t border-gray-100">
                        @if($order->payMethod instanceof \App\Models\PayMethod)
                            <div>
                                <p class="text-xs text-ink-muted mb-1">{{ __t('Оплата') }}</p>
                                <p class="font-medium text-sm">{{ $order->payMethod->t('title') }}</p>
                            </div>
                        @endif
                        @if($order->delivery instanceof \App\Models\Delivery)
                            <div>
                                <p class="text-xs text-ink-muted mb-1">{{ __t('Доставка') }}</p>
                                <p class="font-medium text-sm">{{ $order->delivery->t('title') }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Products --}}
                <div class="card p-6 mb-4">
                    <h3 class="font-bold text-lg mb-4">{{ __t('Товари') }}</h3>
                    <div class="space-y-3">
                        @foreach($order->products as $item)
                            <div class="flex items-center justify-between gap-4 py-2 border-b border-gray-50 last:border-0">
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-sm truncate">{{ $item->title }}</p>
                                    <p class="text-xs text-ink-muted">× {{ $item->count }}</p>
                                </div>
                                <p class="font-semibold text-sm flex-shrink-0">
                                    @money($item->amount ?: ($item->price * $item->count)) {{ setting('currency') }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Totals --}}
                <div class="card p-6">
                    <h3 class="font-bold text-lg mb-4">{{ __t('Підсумок') }}</h3>
                    <div class="space-y-2">
                        @if($order->price_delivery > 0)
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-ink-muted">{{ __t('Доставка') }}</span>
                                <span>@money($order->price_delivery) {{ setting('currency') }}</span>
                            </div>
                        @endif
                        <div class="flex items-center justify-between border-t border-gray-100 pt-3 mt-3">
                            <span class="font-bold">{{ __t('Разом') }}</span>
                            <span class="font-bold text-xl">@money($order->cost) {{ setting('currency') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.shop>
