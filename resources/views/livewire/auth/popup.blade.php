<div class="p-6">

    {{-- ─── Mode tabs (login / register) ──────────────────────────────────── --}}
    @if(in_array($mode, ['login', 'register']))
        <div class="flex border-b border-gray-200 mb-6 -mx-6 px-6 gap-6">
            <button wire:click="switchMode('login')"
                    class="pb-3 text-sm font-semibold border-b-2 transition-colors {{ $mode === 'login' ? 'border-brand text-brand' : 'border-transparent text-ink-muted' }}">
                {{ __t('Вхід') }}
            </button>
            @if(config('auth_features.registration'))
                <button wire:click="switchMode('register')"
                        class="pb-3 text-sm font-semibold border-b-2 transition-colors {{ $mode === 'register' ? 'border-brand text-brand' : 'border-transparent text-ink-muted' }}">
                    {{ __t('Реєстрація') }}
                </button>
            @endif
        </div>
    @endif

    {{-- ─── Login ───────────────────────────────────────────────────────────── --}}
    @if($mode === 'login')
        <form wire:submit="login" class="space-y-4">
            <div>
                <label class="text-sm font-medium mb-1.5 block">{{ __t('Email') }}</label>
                <input wire:model="email" type="email" required autofocus autocomplete="email"
                       class="field @error('email') border-red-400 @enderror"
                       placeholder="email@example.com">
                @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label class="text-sm font-medium">{{ __t('Пароль') }}</label>
                    @if(config('auth_features.password_reset'))
                        <button type="button" wire:click="switchMode('forgot')" class="text-xs text-brand hover:underline">
                            {{ __t('Забули пароль?') }}
                        </button>
                    @endif
                </div>
                <input wire:model="password" type="password" required autocomplete="current-password"
                       class="field @error('password') border-red-400 @enderror">
                @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <label class="flex items-center gap-2 text-sm cursor-pointer">
                <input wire:model="remember" type="checkbox" class="rounded accent-brand">
                {{ __t("Запам'ятати мене") }}
            </label>
            <button type="submit" class="btn btn-p w-full" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="login">{{ __t('Увійти') }}</span>
                <span wire:loading wire:target="login">{{ __t('Вхід...') }}</span>
            </button>
        </form>

        {{-- Socialite --}}
        @php $socialProviders = array_keys(array_filter(config('auth_features.socialite', []))); @endphp
        @if(count($socialProviders))
            <div class="relative flex items-center gap-3 mt-4">
                <div class="flex-1 h-px bg-gray-200"></div>
                <span class="text-xs text-ink-muted">{{ __t('або') }}</span>
                <div class="flex-1 h-px bg-gray-200"></div>
            </div>
            <div class="flex flex-col gap-2 mt-3">
                @foreach($socialProviders as $provider)
                    <a href="{{ route('auth.socialite.redirect', $provider) }}"
                       class="flex items-center justify-center gap-2 w-full py-2 px-4 border border-gray-300 rounded-lg text-sm font-medium hover:bg-gray-50 transition">
                        <img src="/img/social/{{ $provider }}.svg" width="18" height="18" alt="{{ ucfirst($provider) }}"
                             onerror="this.style.display='none'">
                        {{ __t('Увійти через') }} {{ ucfirst($provider) }}
                    </a>
                @endforeach
            </div>
        @endif

        {{-- OTP --}}
        @if(config('auth_features.otp.enabled'))
            <div class="text-sm text-center mt-4">
                <button type="button" wire:click="switchMode('otp')" class="text-brand hover:underline">
                    {{ __t('Увійти через SMS-код') }}
                </button>
            </div>
        @endif
    @endif

    {{-- ─── Register ────────────────────────────────────────────────────────── --}}
    @if($mode === 'register')
        <form wire:submit="register" class="space-y-4">
            <div>
                <label class="text-sm font-medium mb-1.5 block">{{ __t("Ім'я") }} *</label>
                <input wire:model="regName" type="text" required autofocus autocomplete="name"
                       class="field @error('regName') border-red-400 @enderror">
                @error('regName') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="text-sm font-medium mb-1.5 block">{{ __t('Email') }} *</label>
                <input wire:model="regEmail" type="email" required autocomplete="email"
                       class="field @error('regEmail') border-red-400 @enderror"
                       placeholder="email@example.com">
                @error('regEmail') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="text-sm font-medium mb-1.5 block">{{ __t('Пароль') }} *</label>
                <input wire:model="regPassword" type="password" required autocomplete="new-password"
                       class="field @error('regPassword') border-red-400 @enderror">
                @error('regPassword') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="text-sm font-medium mb-1.5 block">{{ __t('Підтвердження пароля') }} *</label>
                <input wire:model="regPassword_confirmation" type="password" required autocomplete="new-password"
                       class="field">
            </div>
            <button type="submit" class="btn btn-p w-full" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="register">{{ __t('Зареєструватись') }}</span>
                <span wire:loading wire:target="register">{{ __t('Реєстрація...') }}</span>
            </button>
        </form>
    @endif

    {{-- ─── Forgot password ─────────────────────────────────────────────────── --}}
    @if($mode === 'forgot')
        <h2 class="text-lg font-bold mb-1">{{ __t('Відновлення пароля') }}</h2>
        <p class="text-sm text-ink-muted mb-5">{{ __t('Введіть email, і ми надішлемо посилання для скидання') }}</p>

        @if($forgotSent)
            <div class="rounded-lg bg-green-50 border border-green-200 p-4 text-sm text-green-700">
                {{ __t('Посилання надіслано. Перевірте пошту.') }}
            </div>
        @else
            <form wire:submit="forgotPassword" class="space-y-4">
                <div>
                    <label class="text-sm font-medium mb-1.5 block">{{ __t('Email') }}</label>
                    <input wire:model="forgotEmail" type="email" required autofocus
                           class="field @error('forgotEmail') border-red-400 @enderror"
                           placeholder="email@example.com">
                    @error('forgotEmail') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <button type="submit" class="btn btn-p w-full" wire:loading.attr="disabled">
                    {{ __t('Надіслати посилання') }}
                </button>
            </form>
        @endif

        <div class="text-sm text-center mt-4">
            <button type="button" wire:click="switchMode('login')" class="text-brand hover:underline">
                ← {{ __t('Назад до входу') }}
            </button>
        </div>
    @endif

    {{-- ─── OTP ─────────────────────────────────────────────────────────────── --}}
    @if($mode === 'otp')
        <h2 class="text-lg font-bold mb-1">{{ __t('Вхід через код') }}</h2>
        <p class="text-sm text-ink-muted mb-5">
            @if($otpStep === 1)
                @if(config('auth_features.otp.channel') === 'sms')
                    {{ __t('Введіть номер телефону для отримання коду') }}
                @else
                    {{ __t('Введіть email для отримання коду') }}
                @endif
            @else
                {{ __t('Введіть 6-значний код, надісланий на') }} {{ $identifier }}
            @endif
        </p>

        @if($otpStep === 1)
            <form wire:submit="sendOtp" class="space-y-4">
                <div>
                    @if(config('auth_features.otp.channel') === 'sms')
                        <label class="text-sm font-medium mb-1.5 block">{{ __t('Телефон') }}</label>
                        <input wire:model="identifier" type="tel" required autofocus
                               placeholder="+380991234567"
                               class="field @error('identifier') border-red-400 @enderror">
                    @else
                        <label class="text-sm font-medium mb-1.5 block">{{ __t('Email') }}</label>
                        <input wire:model="identifier" type="email" required autofocus
                               placeholder="email@example.com"
                               class="field @error('identifier') border-red-400 @enderror">
                    @endif
                    @error('identifier') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <button type="submit" class="btn btn-p w-full" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="sendOtp">{{ __t('Отримати код') }}</span>
                    <span wire:loading wire:target="sendOtp">{{ __t('Надсилання...') }}</span>
                </button>
            </form>
        @else
            <form wire:submit="verifyOtp" class="space-y-4">
                <div>
                    <label class="text-sm font-medium mb-1.5 block">{{ __t('Код підтвердження') }}</label>
                    <input wire:model="code" type="text" inputmode="numeric" maxlength="6"
                           required autofocus autocomplete="one-time-code"
                           class="field text-center tracking-widest text-lg font-mono @error('code') border-red-400 @enderror">
                    @error('code') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror

                    {{-- Alpine countdown 60s --}}
                    <p class="mt-1 text-xs text-ink-muted"
                       x-data="{ remaining: 0, canResend: false }"
                       x-init="
                           const expires = {{ $expiresAt }};
                           const tick = () => {
                               remaining = Math.max(0, expires - Math.floor(Date.now() / 1000));
                               canResend = remaining === 0;
                               if (remaining > 0) setTimeout(tick, 1000);
                           };
                           tick();
                       ">
                        <span x-show="!canResend">
                            {{ __t('Код дійсний ще') }}
                            <span x-text="Math.floor(remaining / 60) + ':' + String(remaining % 60).padStart(2, '0')"></span>
                        </span>
                        <button x-show="canResend" type="button" wire:click="resendOtp"
                                class="text-brand hover:underline">
                            {{ __t('Надіслати повторно') }}
                        </button>
                    </p>
                </div>
                <button type="submit" class="btn btn-p w-full" wire:loading.attr="disabled">
                    {{ __t('Увійти') }}
                </button>
                <button type="button" wire:click="changeIdentifier"
                        class="text-sm text-brand hover:underline text-center w-full">
                    {{ __t('Змінити') }} {{ config('auth_features.otp.channel') === 'sms' ? __t('номер') : __t('email') }}
                </button>
            </form>
        @endif

        <div class="text-sm text-center mt-4">
            <button type="button" wire:click="switchMode('login')" class="text-brand hover:underline">
                ← {{ __t('Назад до входу') }}
            </button>
        </div>
    @endif

</div>
