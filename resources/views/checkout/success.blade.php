<x-layouts.shop :title="__t('Замовлення оформлено')">
    <div class="max-w-lg mx-auto px-4 py-20 text-center">
        <div class="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center text-5xl mx-auto mb-6">✓</div>
        <h1 class="text-3xl font-bold mb-3">{{ __t('Дякуємо за замовлення!') }}</h1>
        <p class="text-ink-muted mb-2">
            {{ __t('Замовлення') }}
            <strong class="text-ink">#{{ $order->id }}</strong>
            {{ __t('успішно оформлено.') }}
        </p>
        @if($order->email)
            <p class="text-ink-muted mb-8">{{ __t('Підтвердження надіслано на') }} {{ $order->email }}</p>
        @else
            <p class="mb-8"></p>
        @endif

        <div class="card p-6 text-left mb-6 space-y-3 text-sm divide-y">
            @if($order->delivery)
                <div class="flex justify-between pb-3">
                    <span class="text-ink-muted">{{ __t('Доставка') }}</span>
                    <span class="font-medium">{{ $order->delivery->t('title') }}</span>
                </div>
            @endif
            @if($order->payMethod)
                <div class="flex justify-between pt-3">
                    <span class="text-ink-muted">{{ __t('Оплата') }}</span>
                    <span class="font-medium">{{ $order->payMethod->t('title') }}</span>
                </div>
            @endif
            <div class="flex justify-between pt-3">
                <span class="text-ink-muted">{{ __t('Сума') }}</span>
                <span class="font-bold">@money($order->cost)</span>
            </div>
        </div>

        <div class="flex gap-3 justify-center">
            <a href="{{ route('home') }}" class="btn btn-o">{{ __t('На головну') }}</a>
        </div>
    </div>
</x-layouts.shop>
