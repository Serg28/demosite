<x-layouts.shop :title="__t('Мої замовлення')">
    <div class="account">
        <div class="container">
            <div class="account__wrap">
                @include('profile.partials.sidebar', ['action' => $action])
                <div class="account__content">
                    <livewire:profile.order.lists />
                </div>
            </div>
        </div>
    </div>
</x-layouts.shop>
