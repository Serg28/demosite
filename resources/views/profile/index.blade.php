<x-layouts.shop :title="__t('Персональні дані')">
    <div class="account">
        <div class="container">
            <div class="account__wrap">
                @include('profile.partials.sidebar', ['action' => $action])
                <div class="account__content">
                    <livewire:profile.user.edit-data />
                    @if(auth()->user()->hasPassword())
                        <livewire:profile.user.edit-password />
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.shop>
