<x-layouts.shop :title="__t('Замовлення') . ' #' . $order->id">
    <div class="max-w-[1264px] mx-auto px-4 sm:px-8 py-8">
        <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
            <h1 class="text-2xl md:text-3xl font-bold">{{ __t('Особистий кабінет') }}</h1>
            <x-breadcrumbs :items="$breadcrumbs" />
        </div>

        <x-flash-messages />

        <div class="flex gap-6 flex-wrap md:flex-nowrap">
            @include('profile.partials.sidebar', ['action' => 'orders'])
            <div class="flex-1 min-w-0">

                {{-- Header card --}}
                <div class="card p-6 mb-4">
                    <div class="flex items-start justify-between flex-wrap gap-4 mb-4">
                        <div>
                            <p class="text-xs text-ink-muted mb-1">{{ __t('Замовлення') }}</p>
                            <h2 class="text-2xl font-bold mb-1">#{{ $order->id }}</h2>
                            <p class="text-sm text-ink-muted">{{ $order->created_at->format('d.m.Y H:i') }}</p>
                        </div>
                        <div class="flex items-center gap-3 flex-wrap">
                            <x-status-pill :status="$order->orderStatus" />
                            <a href="{{ route('profile.orders') }}" class="btn btn-o btn-sm">
                                ← {{ __t('До замовлень') }}
                            </a>
                        </div>
                    </div>

                    {{-- Vertical timeline --}}
                    <x-order-timeline
                        :statuses="$timelineStatuses"
                        :currentPriority="$currentPriority"
                        :orderStatus="$orderStatus"
                    />

                    {{-- Action buttons — під таймлайном --}}
                    <div class="flex gap-2 flex-wrap pt-4 border-t border-gray-100 mt-4">
                        @if($order->canBeRepaid())
                            <form method="POST" action="{{ route('profile.orders.pay', $order->id) }}">
                                @csrf
                                <button type="submit" class="btn btn-p btn-sm">
                                    💳 {{ __t('Оплатити') }}
                                </button>
                            </form>
                        @endif
                        <button type="button"
                                data-js-modal
                                data-component="profile.order.repeat-order"
                                data-id="{{ $order->id }}"
                                class="btn btn-o btn-sm">
                            ↻ {{ __t('Повторити') }}
                        </button>
                        @if($order->canBeCancelled())
                            <button type="button"
                                    data-js-modal
                                    data-component="profile.order.cancel-order"
                                    data-id="{{ $order->id }}"
                                    class="btn btn-o btn-sm border-red-300 text-red-600 hover:bg-red-50">
                                {{ __t('Скасувати') }}
                            </button>
                        @endif
                    </div>
                </div>

                {{-- 3-col info grid — окремі картки --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div class="card p-5">
                        <p class="text-xs text-ink-muted font-medium mb-2 uppercase tracking-wide">{{ __t('Доставка') }}</p>
                        @if($order->delivery instanceof \App\Models\Delivery)
                            <p class="text-sm font-semibold">{{ $order->delivery->t('title') }}</p>
                        @endif
                        @if($order->address)
                            <p class="text-xs text-ink-muted mt-1">{{ $order->address }}</p>
                        @endif
                    </div>
                    <div class="card p-5">
                        <p class="text-xs text-ink-muted font-medium mb-2 uppercase tracking-wide">{{ __t('Оплата') }}</p>
                        @if($order->payMethod instanceof \App\Models\PayMethod)
                            <p class="text-sm font-semibold">{{ $order->payMethod->t('title') }}</p>
                        @endif
                        @if($order->canBeRepaid())
                            <p class="text-xs text-amber-600 font-semibold mt-1">● {{ __t('Очікує оплати') }}</p>
                        @endif
                    </div>
                    <div class="card p-5">
                        <p class="text-xs text-ink-muted font-medium mb-2 uppercase tracking-wide">{{ __t('Отримувач') }}</p>
                        <p class="text-sm font-semibold">{{ trim($order->first_name . ' ' . $order->last_name) }}</p>
                        @if($order->phone)
                            <p class="text-xs text-ink-muted mt-1">{{ $order->phone }}</p>
                        @endif
                        @if($order->email)
                            <p class="text-xs text-ink-muted">{{ $order->email }}</p>
                        @endif
                    </div>
                </div>

                {{-- Products --}}
                <div class="card mb-4 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="font-bold text-lg">{{ __t('Товари') }}</h3>
                    </div>
                    <div class="divide-y divide-gray-50">
                        @foreach($order->products as $item)
                            <div class="px-5 py-4 flex items-center gap-4">
                                @if($item->product?->picture)
                                    <img src="{{ $item->product->picture }}"
                                         class="w-16 h-16 rounded-lg object-cover border border-gray-100 flex-shrink-0"
                                         alt="">
                                @else
                                    <div class="w-16 h-16 rounded-lg bg-gray-100 border border-gray-100 flex items-center justify-center text-gray-300 flex-shrink-0">
                                        <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9l4-4 4 4 4-4 4 4"/><circle cx="8.5" cy="13.5" r="1.5"/></svg>
                                    </div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    @if($item->product?->code)
                                        <p class="text-xs text-ink-muted mb-0.5">{{ __t('Код') }}: {{ $item->product->code }}</p>
                                    @endif
                                    <p class="font-medium text-sm leading-snug line-clamp-2">{{ $item->title }}</p>
                                </div>
                                <div class="text-right flex-shrink-0">
                                    <p class="text-sm text-ink-muted">@money($item->price) {{ setting('currency') }} × {{ $item->count }}</p>
                                    <p class="font-bold">@money($item->amount ?: ($item->price * $item->count)) {{ setting('currency') }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    {{-- Totals --}}
                    <div class="px-5 py-4 bg-gray-50 border-t border-gray-100 space-y-1.5 text-sm">
                        @if($order->price_delivery > 0)
                            <div class="flex justify-between">
                                <span class="text-ink-muted">{{ __t('Доставка') }}</span>
                                <span>@money($order->price_delivery) {{ setting('currency') }}</span>
                            </div>
                        @else
                            <div class="flex justify-between">
                                <span class="text-ink-muted">{{ __t('Доставка') }}</span>
                                <span class="text-green-600">{{ __t('Безкоштовно') }}</span>
                            </div>
                        @endif
                        @if($order->sale > 0)
                            <div class="flex justify-between">
                                <span class="text-ink-muted">{{ __t('Знижка') }}</span>
                                <span class="text-green-600">−@money($order->sale) {{ setting('currency') }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between font-bold text-base pt-2 border-t border-gray-200 mt-2">
                            <span>{{ __t('Разом') }}</span>
                            <span>@money($order->cost) {{ setting('currency') }}</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-layouts.shop>
