<div class="flex flex-col gap-6">
    <div class="text-center">
        <h1 class="text-2xl font-bold text-gray-900">{{ __t('Скидання пароля') }}</h1>
        <p class="mt-2 text-sm text-gray-600">{{ __t('Введіть новий пароль') }}</p>
    </div>

    @if (session('status'))
        <div class="text-sm text-green-600 bg-green-50 border border-green-200 rounded-lg p-3 text-center">
            {{ session('status') }}
        </div>
    @endif

    <form wire:submit="resetPassword" class="flex flex-col gap-4">
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                {{ __t('Email') }}
            </label>
            <input wire:model="email" id="email" type="email" required autocomplete="email"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror">
            @error('email')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                {{ __t('Новий пароль') }}
            </label>
            <input wire:model="password" id="password" type="password" required autocomplete="new-password"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('password') border-red-500 @enderror">
            @error('password')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                {{ __t('Підтвердження пароля') }}
            </label>
            <input wire:model="password_confirmation" id="password_confirmation" type="password" required autocomplete="new-password"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <button type="submit" data-test="reset-password-button"
            class="w-full py-2 px-4 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition"
            wire:loading.attr="disabled">
            {{ __t('Змінити пароль') }}
        </button>
    </form>
</div>
