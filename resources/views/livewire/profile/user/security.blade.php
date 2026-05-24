<div class="card p-6">
    <h2 class="font-bold text-xl mb-1">{{ __t('Безпека') }}</h2>

    @if(session()->has('security_success'))
        <div class="mb-4 px-4 py-3 rounded-lg bg-green-50 text-green-700 text-sm">{{ session('security_success') }}</div>
    @endif
    @if(session()->has('security_error'))
        <div class="mb-4 px-4 py-3 rounded-lg bg-red-50 text-red-700 text-sm">{{ session('security_error') }}</div>
    @endif

    <div class="space-y-0 mt-4">
        {{-- Двофакторна аутентифікація --}}
        @if(config('auth_features.two_factor'))
            <div class="flex items-center justify-between py-4 border-b border-gray-100">
                <div class="min-w-0 flex-1 mr-4">
                    <p class="font-medium text-sm">{{ __t('Двофакторна аутентифікація') }}</p>
                    <p class="text-xs text-ink-muted mt-0.5">{{ __t('Захистіть акаунт додатковим кодом підтвердження при вході') }}</p>
                </div>
                <div class="flex-shrink-0 flex items-center gap-2">
                    @if($twoFactorEnabled)
                        @if(! $showDisableConfirm)
                            <button type="button" class="btn btn-o btn-sm" wire:click="confirmDisable">
                                {{ __t('Вимкнути 2FA') }}
                            </button>
                        @else
                            <a href="{{ route('two-factor.show') }}" class="btn btn-p btn-sm">
                                {{ __t('Керувати 2FA') }}
                            </a>
                            <button type="button" class="btn btn-o btn-sm" wire:click="cancelDisable">
                                {{ __t('Скасувати') }}
                            </button>
                        @endif
                    @else
                        <a href="{{ route('two-factor.show') }}" class="btn btn-p btn-sm">
                            {{ __t('Увімкнути 2FA') }}
                        </a>
                    @endif
                </div>
            </div>
        @endif

        {{-- Підключені акаунти --}}
        @if($providers->isNotEmpty())
            <div class="py-4">
                <p class="font-medium text-sm mb-0.5">{{ __t('Підключені акаунти') }}</p>
                <p class="text-xs text-ink-muted mb-3">{{ __t('Соціальні мережі для входу') }}</p>

                <div class="space-y-2">
                    @foreach($providers as $provider)
                        <div class="flex items-center gap-3 py-2" wire:key="provider-{{ $provider->id }}">
                            @if($provider->avatar)
                                <img src="{{ $provider->avatar }}" alt="{{ $provider->name }}"
                                     class="w-8 h-8 rounded-full" width="32" height="32">
                            @endif
                            <div class="flex-1 min-w-0">
                                <span class="font-semibold text-sm">{{ ucfirst($provider->provider) }}</span>
                                @if($provider->name)
                                    <span class="text-ink-muted text-xs ml-2">{{ $provider->name }}</span>
                                @endif
                            </div>
                            <button type="button" class="btn btn-o btn-sm"
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

        @if(! config('auth_features.two_factor') && $providers->isEmpty())
            <p class="text-ink-muted text-sm py-4">{{ __t('Немає налаштувань безпеки') }}</p>
        @endif
    </div>
</div>
