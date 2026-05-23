<div class="account__section">
    <h2 class="account__heading">{{ __t('Безпека') }}</h2>

    @if(session()->has('security_success'))
        <div class="alert alert--success">{{ session('security_success') }}</div>
    @endif
    @if(session()->has('security_error'))
        <div class="alert alert--error">{{ session('security_error') }}</div>
    @endif

    {{-- Двофакторна автентифікація --}}
    <div class="security-block mt-6">
        <h3 class="security-block__title">{{ __t('Двофакторна автентифікація') }}</h3>
        <p class="security-block__desc">
            {{ __t('Захистіть акаунт додатковим кодом підтвердження при вході') }}
        </p>

        <div class="mt-4">
            @if($twoFactorEnabled)
                <div class="alert alert--success mb-3">{{ __t('2FA увімкнена') }}</div>
                @if(! $showDisableConfirm)
                    <button type="button" class="btn btn--ghost" wire:click="confirmDisable">
                        {{ __t('Вимкнути 2FA') }}
                    </button>
                @else
                    <p class="text-sm text-gray-600 mb-3">
                        {{ __t('Для керування 2FA перейдіть до налаштувань безпеки') }}
                    </p>
                    <a href="{{ route('two-factor.show') }}" class="btn btn--primary">
                        {{ __t('Керувати 2FA') }}
                    </a>
                    <button type="button" class="btn btn--ghost ml-2" wire:click="cancelDisable">
                        {{ __t('Скасувати') }}
                    </button>
                @endif
            @else
                <a href="{{ route('two-factor.show') }}" class="btn btn--primary">
                    {{ __t('Увімкнути 2FA') }}
                </a>
            @endif
        </div>
    </div>

    {{-- Підключені акаунти --}}
    @if($providers->isNotEmpty())
        <div class="security-block mt-8">
            <h3 class="security-block__title">{{ __t('Підключені акаунти') }}</h3>
            <p class="security-block__desc">{{ __t('Соціальні мережі для входу') }}</p>

            <div class="providers-list mt-4">
                @foreach($providers as $provider)
                    <div class="provider-row" wire:key="provider-{{ $provider->id }}">
                        @if($provider->avatar)
                            <img src="{{ $provider->avatar }}" alt="{{ $provider->name }}"
                                 class="provider-avatar" width="40" height="40">
                        @endif
                        <div class="provider-info">
                            <span class="fw-600">{{ ucfirst($provider->provider) }}</span>
                            @if($provider->name)
                                <span class="text-gray-500 ml-2">{{ $provider->name }}</span>
                            @endif
                        </div>
                        <button type="button" class="btn btn--ghost btn--sm ml-auto"
                                wire:click="disconnectProvider({{ $provider->id }})"
                                wire:confirm="{{ __t('Відключити цей акаунт?') }}"
                                wire:loading.attr="disabled">
                            {{ __t('Відключити') }}
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
