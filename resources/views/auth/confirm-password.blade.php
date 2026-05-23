<x-layouts.auth :title="__t('Підтвердження пароля')">
    <div class="flex flex-col gap-6">
        <div class="text-center">
            <h1 class="text-2xl font-bold text-gray-900">{{ __t('Підтвердження пароля') }}</h1>
            <p class="mt-2 text-sm text-gray-600">
                {{ __t('Це захищена зона. Будь ласка, підтвердіть пароль для продовження.') }}
            </p>
        </div>
        <form method="POST" action="{{ route('password.confirm') }}" class="flex flex-col gap-4">
            @csrf
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                    {{ __t('Пароль') }}
                </label>
                <input id="password" name="password" type="password" required autocomplete="current-password"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('password')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <button type="submit"
                class="w-full py-2 px-4 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition">
                {{ __t('Підтвердити') }}
            </button>
        </form>
    </div>
</x-layouts.auth>
