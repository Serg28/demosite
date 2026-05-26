<div class="p-6">

    @if($state === 'confirm')
        {{-- Підтвердження скасування --}}
        <div class="text-center">
            <div class="text-4xl mb-4">⚠️</div>
            <h2 class="text-xl font-bold mb-2">{{ __t('Скасувати замовлення?') }}</h2>
            <p class="text-ink-muted text-sm mb-6">
                {{ __t('Замовлення') }} <strong>#{{ $orderId }}</strong>
                {{ __t('буде скасовано. Цю дію не можна відмінити.') }}
            </p>
            <div class="flex gap-3 justify-center">
                <button
                    wire:click="$dispatch('closeModal')"
                    class="btn btn-o"
                >
                    {{ __t('Залишити') }}
                </button>
                <button
                    wire:click="confirm"
                    wire:loading.attr="disabled"
                    class="btn btn-p bg-red-600 hover:bg-red-700 border-red-600 hover:border-red-700"
                >
                    <span wire:loading wire:target="confirm" class="mr-1">⏳</span>
                    {{ __t('Так, скасувати') }}
                </button>
            </div>
        </div>

    @elseif($state === 'success')
        {{-- Успіх --}}
        <div class="text-center">
            <div class="text-4xl mb-4">✅</div>
            <h2 class="text-xl font-bold mb-2 text-green-600">{{ __t('Скасовано') }}</h2>
            <p class="text-ink-muted text-sm mb-6">{{ $message }}</p>
            <button
                wire:click="$dispatch('closeModal')"
                onclick="window.location.reload()"
                class="btn btn-p"
            >
                {{ __t('Закрити') }}
            </button>
        </div>

    @else
        {{-- Помилка --}}
        <div class="text-center">
            <div class="text-4xl mb-4">❌</div>
            <h2 class="text-xl font-bold mb-2 text-red-600">{{ __t('Помилка') }}</h2>
            <p class="text-ink-muted text-sm mb-6">{{ $message }}</p>
            <button
                wire:click="$dispatch('closeModal')"
                class="btn btn-p"
            >
                {{ __t('Закрити') }}
            </button>
        </div>
    @endif

</div>
