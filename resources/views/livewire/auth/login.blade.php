<div class="flex flex-col gap-6">
    <div class="text-center">
        <h1 class="text-2xl font-bold text-gray-900">{{ __t('Вхід') }}</h1>
        <p class="mt-2 text-sm text-gray-600">{{ __t('Введіть email та пароль') }}</p>
    </div>

    @if (session('status'))
        <div class="text-sm text-green-600 text-center">{{ session('status') }}</div>
    @endif

    <form wire:submit="login" class="flex flex-col gap-4">
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                {{ __t('Email') }}
            </label>
            <input wire:model="email" id="email" type="email" required autofocus autocomplete="email"
                placeholder="email@example.com"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror">
            @error('email')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <div class="flex items-center justify-between mb-1">
                <label for="password" class="block text-sm font-medium text-gray-700">
                    {{ __t('Пароль') }}
                </label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-xs text-blue-600 hover:underline">
                        {{ __t('Забули пароль?') }}
                    </a>
                @endif
            </div>
            <input wire:model="password" id="password" type="password" required autocomplete="current-password"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('password') border-red-500 @enderror">
            @error('password')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
            <input wire:model="remember" type="checkbox" class="rounded border-gray-300 text-blue-600">
            {{ __t("Запам'ятати мене") }}
        </label>

        <button type="submit" data-test="login-button"
            class="w-full py-2 px-4 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition"
            wire:loading.attr="disabled">
            <span wire:loading.remove>{{ __t('Увійти') }}</span>
            <span wire:loading>{{ __t('Вхід...') }}</span>
        </button>
    </form>

    {{-- Socialite --}}
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
                    {{ __t('Увійти через') }} {{ ucfirst($provider) }}
                </a>
            @endforeach
        </div>
    @endif

    {{-- OTP --}}
    @if (config('auth_features.otp.enabled'))
        <div class="text-sm text-center">
            <a href="{{ route('auth.otp') }}" class="text-blue-600 hover:underline">
                {{ __t('Увійти через SMS-код') }}
            </a>
        </div>
    @endif

    @if (config('auth_features.registration') && Route::has('register'))
        <div class="text-sm text-center text-gray-600">
            {{ __t('Немає акаунту?') }}
            <a href="{{ route('register') }}" class="text-blue-600 hover:underline">{{ __t('Зареєструватись') }}</a>
        </div>
    @endif
</div>
