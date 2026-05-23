<x-layouts.shop :title="__t('Безпека')">
    <div class="account">
        <div class="container">
            <div class="account__wrap">
                @include('profile.partials.sidebar', ['action' => $action])
                <div class="account__content">
                    @if(config('auth_features.two_factor'))
                        <livewire:profile.user.security />
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.shop>
