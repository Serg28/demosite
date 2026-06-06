@php
    /** @var \App\Models\User $user */
    $user           = auth()->user();
    $displayName    = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) ?: ($user->name ?? '');
    $ordersCount    = $user->orders()->count();
    $addressesCount = $user->addresses()->count();
    $recipientsCount = $user->recipients()->count();
    $discountBest   = $user->discountCards()->where('is_active', true)->where('type', 'percent')->max('value') ?? 0;
    $favoritesCount = $user->wishlists()->count();
    $compareCount   = app(\App\Services\CompareService::class)->count();
@endphp

<aside class="w-full md:w-72 flex-shrink-0">
    {{-- User card --}}
    <div class="card p-5 mb-3">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-14 h-14 rounded-full bg-gradient-to-br from-brand to-brand-dark text-white flex items-center justify-center font-bold text-xl flex-shrink-0 uppercase">
                {{ $user->initials() ?: '?' }}
            </div>
            <div class="min-w-0">
                <p class="font-semibold text-sm leading-tight truncate">{{ $displayName }}</p>
                <p class="text-xs text-ink-muted truncate">{{ $user->phone }}</p>
            </div>
        </div>
        <div class="grid grid-cols-3 gap-2 text-center">
            <div>
                <p class="text-lg font-bold leading-tight">{{ $ordersCount }}</p>
                <p class="text-[10px] text-ink-muted leading-tight">{{ __t('замовлень') }}</p>
            </div>
            <div>
                <p class="text-lg font-bold leading-tight text-amber-600">0</p>
                <p class="text-[10px] text-ink-muted leading-tight">{{ __t('бонусів') }}</p>
            </div>
            <div>
                <p class="text-lg font-bold leading-tight text-brand">{{ $discountBest > 0 ? $discountBest . '%' : '0%' }}</p>
                <p class="text-[10px] text-ink-muted leading-tight">{{ __t('знижка') }}</p>
            </div>
        </div>
    </div>

    {{-- Nav card --}}
    <nav class="card p-2 space-y-0.5">
        @php
            $navItems = [
                [
                    'route' => 'profile.index',
                    'key'   => 'index',
                    'label' => __t('Персональні дані'),
                    'badge' => null,
                    'icon'  => '<svg class="w-5 h-5" viewBox="0 0 18 19" fill="none"><path d="M1.109 18C2.034 14.193 5.253 11.348 9.11 11.348c3.857 0 7.065 2.845 8 6.65M12.583 4.522c0-1.945-1.532-3.522-3.42-3.522C7.274 1 5.743 2.577 5.743 4.522c0 1.944 1.531 3.521 3.42 3.521 1.888 0 3.42-1.577 3.42-3.521z" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round"/></svg>',
                ],
                [
                    'route' => 'profile.orders',
                    'key'   => 'orders',
                    'label' => __t('Мої замовлення'),
                    'badge' => $ordersCount,
                    'icon'  => '<svg class="w-5 h-5" viewBox="0 0 22 25" fill="none"><rect x="1.738" y="1.202" width="19.5" height="22.5" rx="4.25" stroke="currentColor" stroke-width="1.5"/><path d="M6.988 9.452H15.11M6.988 14.247H15.11" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/></svg>',
                ],
                [
                    'route' => 'profile.addresses',
                    'key'   => 'addresses',
                    'label' => __t('Адреси доставки'),
                    'badge' => $addressesCount ?: null,
                    'icon'  => '<svg class="w-5 h-5" viewBox="0 0 18 22" fill="none"><path d="M9 1C5.686 1 3 3.686 3 7c0 4.5 6 13 6 13s6-8.5 6-13c0-3.314-2.686-6-6-6z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><circle cx="9" cy="7" r="2" stroke="currentColor" stroke-width="1.5"/></svg>',
                ],
                [
                    'route' => 'profile.recipients',
                    'key'   => 'recipients',
                    'label' => __t('Отримувачі'),
                    'badge' => $recipientsCount ?: null,
                    'icon'  => '<svg class="w-5 h-5" viewBox="0 0 22 18" fill="none"><path d="M1 17c1-4 4.5-6.5 8-6.5s7 2.5 8 6.5M15.5 3.5c0 2.5-2 4.5-4.5 4.5S6.5 6 6.5 3.5 8.5-1 11-1s4.5 2 4.5 4.5z" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round"/><path d="M19 13c.9-1.2 1.5-2.7 1.5-4.3C20.5 5.5 18 3 15 2.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>',
                ],
                [
                    'route' => 'profile.discounts',
                    'key'   => 'discounts',
                    'label' => __t('Знижки та бонуси'),
                    'badge' => $discountBest > 0 ? $discountBest . '%' : null,
                    'icon'  => '<svg class="w-5 h-5" viewBox="0 0 22 22" fill="none"><circle cx="11" cy="11" r="9.5" stroke="currentColor" stroke-width="1.5"/><path d="M7 15l8-8M8 8.5a.5.5 0 11-1 0 .5.5 0 011 0zM15 13.5a.5.5 0 11-1 0 .5.5 0 011 0z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>',
                ],
                [
                    'route' => 'profile.favorites',
                    'key'   => 'favorites',
                    'label' => __t('Список бажань'),
                    'badge' => $favoritesCount ?: null,
                    'icon'  => '<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',
                ],
                [
                    'route' => 'profile.compare',
                    'key'   => 'compare',
                    'label' => __t('Порівняння'),
                    'badge' => $compareCount ?: null,
                    'icon'  => '<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none"><path d="M9 3H5a2 2 0 00-2 2v14a2 2 0 002 2h4M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4M9 12h6M12 9l3 3-3 3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',
                ],
                [
                    'route' => 'profile.security',
                    'key'   => 'security',
                    'label' => __t('Безпека'),
                    'badge' => null,
                    'icon'  => '<svg class="w-5 h-5" viewBox="0 0 18 22" fill="none"><path d="M9 1L1.5 4.5V10c0 4.97 3.18 9.56 7.5 11 4.32-1.44 7.5-6.03 7.5-11V4.5L9 1z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M6 11l2 2 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',
                ],
            ];
        @endphp

        @foreach($navItems as $item)
            @php $isActive = ($action === $item['key']); @endphp
            <a href="{{ route($item['route']) }}"
               class="flex items-center gap-3 pl-4 pr-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                      {{ $isActive ? 'bg-brand-light text-brand border-l-2 border-brand rounded-l-none' : 'text-ink hover:bg-gray-50' }}">
                <span class="w-5 flex-shrink-0 flex items-center justify-center">
                    {!! $item['icon'] !!}
                </span>
                <span class="flex-1">{{ $item['label'] }}</span>
                @if($item['badge'])
                    <span class="text-xs px-1.5 py-0.5 rounded-full font-semibold
                                 {{ $isActive ? 'bg-brand text-white' : 'bg-gray-100 text-ink-muted' }}">
                        {{ $item['badge'] }}
                    </span>
                @endif
            </a>
        @endforeach

        <div class="border-t border-gray-100 my-1"></div>
        <a href="{{ route('profile.logout') }}"
           class="flex items-center gap-3 pl-4 pr-3 py-2.5 rounded-lg text-sm font-medium text-red-500 hover:bg-red-50 transition-colors">
            <span class="w-5 flex-shrink-0 flex items-center justify-center">
                <svg class="w-5 h-5" viewBox="0 0 18 22" fill="none">
                    <path d="M11.925 1H1.012V21H11.925" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <line x1="8.012" y1="11.502" x2="15.262" y2="11.502" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M12.263 15.096L16.992 11l-4.729-4.096" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </span>
            <span>{{ __t('Вийти з акаунту') }}</span>
        </a>
    </nav>
</aside>
