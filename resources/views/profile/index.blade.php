<x-layouts.shop :title="__t('Персональні дані')">
    <div class="account">
        <div class="container">
            <div class="account__wrap">
                @include('profile.partials.sidebar', ['action' => $action])
                <div class="account__content">
                    <livewire:profile.user.edit-data />
                    <livewire:profile.user.edit-password />
                </div>
            </div>
        </div>
    </div>
</x-layouts.shop>
