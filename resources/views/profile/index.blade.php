<x-layouts.shop :title="__t('Особистий кабінет')">
    <div class="max-w-[1264px] mx-auto px-4 sm:px-8 py-8">
        <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
            <h1 class="text-2xl md:text-3xl font-bold">{{ __t('Особистий кабінет') }}</h1>
            <x-breadcrumbs :items="$breadcrumbs" />
        </div>
        <div class="flex gap-6 flex-wrap md:flex-nowrap">
            @include('profile.partials.sidebar', ['action' => $action])
            <div class="flex-1 min-w-0">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div>
                        <livewire:profile.user.edit-data />
                    </div>
                    <div>
                        @if(auth()->user()->hasPassword())
                            <livewire:profile.user.edit-password />
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.shop>
