<x-layouts.shop :title="__t('Адреси доставки')">
    <div class="max-w-[1264px] mx-auto px-4 sm:px-8 py-8">
        <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
            <h1 class="text-2xl md:text-3xl font-bold">{{ __t('Особистий кабінет') }}</h1>
            <nav class="text-sm text-ink-muted">
                <a href="{{ route('home') }}" class="hover:text-brand">{{ __t('Головна') }}</a>
                <span class="mx-1">›</span>
                <a href="{{ route('profile.index') }}" class="hover:text-brand">{{ __t('Кабінет') }}</a>
                <span class="mx-1">›</span>
                <span class="text-ink font-medium">{{ __t('Адреси доставки') }}</span>
            </nav>
        </div>
        <div class="flex gap-6 flex-wrap md:flex-nowrap">
            @include('profile.partials.sidebar', ['action' => $action])
            <div class="flex-1 min-w-0">
                <livewire:profile.address.manager />
            </div>
        </div>
    </div>
</x-layouts.shop>
