<div class="flex flex-col gap-6">
    <div class="text-center">
        <h1 class="text-2xl font-bold text-gray-900">{{ __t('Вхід через код') }}</h1>
        <p class="mt-2 text-sm text-gray-600">
            @if ($step === 1)
                @if (config('auth_features.otp.channel') === 'sms')
                    {{ __t('Введіть номер телефону для отримання коду') }}
                @else
                    {{ __t('Введіть email для отримання коду') }}
                @endif
            @else
                {{ __t('Введіть 6-значний код') }}
            @endif
        </p>
    </div>

    @if ($step === 1)
        <form wire:submit="sendCode" class="flex flex-col gap-4">
            <div>
                @if (config('auth_features.otp.channel') === 'sms')
                    <label for="identifier" class="block text-sm font-medium text-gray-700 mb-1">
                        {{ __t('Телефон') }}
                    </label>
                    <input wire:model="identifier" id="identifier" type="tel" required autofocus
                        placeholder="+380991234567"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('identifier') border-red-500 @enderror">
                @else
                    <label for="identifier" class="block text-sm font-medium text-gray-700 mb-1">
                        {{ __t('Email') }}
                    </label>
                    <input wire:model="identifier" id="identifier" type="email" required autofocus
                        placeholder="email@example.com"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('identifier') border-red-500 @enderror">
                @endif
                @error('identifier')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                class="w-full py-2 px-4 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition"
                wire:loading.attr="disabled">
                <span wire:loading.remove>{{ __t('Отримати код') }}</span>
                <span wire:loading>{{ __t('Надсилання...') }}</span>
            </button>
        </form>
    @else
        <form wire:submit="verify" class="flex flex-col gap-4">
            <div>
                <label for="code" class="block text-sm font-medium text-gray-700 mb-1">
                    {{ __t('Код підтвердження') }}
                </label>
                <input wire:model="code" id="code" type="text" inputmode="numeric" maxlength="6"
                    required autofocus autocomplete="one-time-code"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm text-center tracking-widest text-lg font-mono focus:outline-none focus:ring-2 focus:ring-blue-500 @error('code') border-red-500 @enderror">
                @error('code')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500" x-data="{ remaining: 0 }"
                    x-init="
                        const expires = {{ $expiresAt }};
                        const update = () => {
                            remaining = Math.max(0, expires - Math.floor(Date.now() / 1000));
                            if (remaining > 0) setTimeout(update, 1000);
                        };
                        update();
                    ">
                    <span x-show="remaining > 0">
                        {{ __t('Код дійсний ще') }}
                        <span x-text="Math.floor(remaining / 60) + ':' + String(remaining % 60).padStart(2, '0')"></span>
                    </span>
                    <span x-show="remaining === 0">{{ __t('Код прострочено') }}</span>
                </p>
            </div>

            <button type="submit"
                class="w-full py-2 px-4 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition"
                wire:loading.attr="disabled">
                {{ __t('Увійти') }}
            </button>

            <button type="button" wire:click="$set('step', 1)"
                class="text-sm text-blue-600 hover:underline text-center">
                {{ __t('Назад') }}
            </button>
        </form>
    @endif

    <div class="text-sm text-center text-gray-600">
        <a href="{{ route('login') }}" class="text-blue-600 hover:underline">
            {{ __t('Увійти через пароль') }}
        </a>
    </div>
</div>
