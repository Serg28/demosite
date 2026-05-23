<div class="flex flex-col gap-6">
    <div class="text-center">
        <h1 class="text-2xl font-bold text-gray-900">{{ __t('Реєстрація') }}</h1>
        <p class="mt-2 text-sm text-gray-600">{{ __t('Створіть новий акаунт') }}</p>
    </div>

    <form wire:submit="register" class="flex flex-col gap-4">
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                {{ __t("Ім'я") }}
            </label>
            <input wire:model="name" id="name" type="text" required autofocus autocomplete="name"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror">
            @error('name')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

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
                {{ __t('Пароль') }}
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

        <button type="submit" data-test="register-user-button"
            class="w-full py-2 px-4 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition"
            wire:loading.attr="disabled">
            <span wire:loading.remove>{{ __t('Зареєструватись') }}</span>
            <span wire:loading>{{ __t('Реєстрація...') }}</span>
        </button>
    </form>

    @php $socialProviders = array_keys(array_filter(config('auth_features.socialite', []))); @endphp
    @if (count($socialProviders))
        <div class="relative flex items-center gap-3">
            <div class="flex-1 h-px bg-gray-200"></div>
            <span class="text-xs text-gray-400">{{ __t('або') }}</span>
            <div class="flex-1 h-px bg-gray-200"></div>
        </div>
        <div class="flex flex-col gap-2">
            @foreach ($socialProviders as $provider)
                <a href="{{ route('auth.socialite.redirect', $provider) }}"
                    class="flex items-center justify-center gap-2 w-full py-2 px-4 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                    <img src="/img/social/{{ $provider }}.svg" width="18" height="18" alt="{{ ucfirst($provider) }}"
                        onerror="this.style.display='none'">
                    {{ ucfirst($provider) }}
                </a>
            @endforeach
        </div>
    @endif

    <div class="text-sm text-center text-gray-600">
        {{ __t('Вже є акаунт?') }}
        <a href="{{ route('login') }}" class="text-blue-600 hover:underline">{{ __t('Увійти') }}</a>
    </div>
</div>
