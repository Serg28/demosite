@props(['total', 'subtotal'])

<div class="px-6 py-4 border-t border-gray-100 bg-white">
    <div class="flex justify-between text-sm text-gray-600 mb-1">
        <span>{{ __t('Без ПДВ') }}</span>
        <span>@money($subtotal) {{ setting('currency') }}</span>
    </div>
    <div class="flex justify-between text-base font-semibold text-ink mb-4">
        <span>{{ __t('Разом') }}</span>
        <span>@money($total) {{ setting('currency') }}</span>
    </div>

    <a href="{{ route('checkout.index') }}"
       class="block w-full text-center bg-brand hover:bg-brand/90 text-white font-semibold py-3 rounded-xl transition-colors">
        {{ __t('Оформити замовлення') }}
    </a>
    <button type="button"
            wire:click="close"
            class="block w-full text-center mt-2 text-sm text-gray-500 hover:text-ink transition-colors py-2">
        {{ __t('Продовжити покупки') }}
    </button>
</div>
