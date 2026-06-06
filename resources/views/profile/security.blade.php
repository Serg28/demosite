<x-layouts.shop :title="__t('Безпека')">
    <div class="max-w-[1264px] mx-auto px-4 sm:px-8 py-8">
        <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
            <h1 class="text-2xl md:text-3xl font-bold">{{ __t('Особистий кабінет') }}</h1>
            <x-breadcrumbs :items="$breadcrumbs" />
        </div>
        <div class="flex gap-6 flex-wrap md:flex-nowrap">
            @include('profile.partials.sidebar', ['action' => $action])
            <div class="flex-1 min-w-0">
                <livewire:profile.user.security />
            </div>
        </div>
    </div>
</x-layouts.shop>
