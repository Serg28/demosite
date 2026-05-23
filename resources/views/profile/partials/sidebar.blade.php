<nav class="account__sidebar">
    <a href="{{ route('profile.index') }}"
       class="account__link {{ $action === 'index' ? 'active' : '' }}">
        <svg class="icon" width="18" height="19" viewBox="0 0 18 19" fill="none">
            <path d="M1.109 18C2.034 14.193 5.253 11.348 9.11 11.348c3.857 0 7.065 2.845 8 6.65M12.583 4.522c0-1.945-1.532-3.522-3.42-3.522C7.274 1 5.743 2.577 5.743 4.522c0 1.944 1.531 3.521 3.42 3.521 1.888 0 3.42-1.577 3.42-3.521z" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round"/>
        </svg>
        {{ __t('Персональні дані') }}
    </a>
    <a href="{{ route('profile.orders') }}"
       class="account__link {{ $action === 'orders' ? 'active' : '' }}">
        <svg class="icon" width="22" height="25" viewBox="0 0 22 25" fill="none">
            <rect x="1.738" y="1.202" width="19.5" height="22.5" rx="4.25" stroke="currentColor" stroke-width="1.5"/>
            <path d="M6.988 9.452H15.11M6.988 14.247H15.11" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        {{ __t('Мої замовлення') }}
    </a>
    <a href="{{ route('profile.security') }}"
       class="account__link {{ $action === 'security' ? 'active' : '' }}">
        <svg class="icon" width="18" height="22" viewBox="0 0 18 22" fill="none">
            <path d="M9 1L1.5 4.5V10c0 4.97 3.18 9.56 7.5 11 4.32-1.44 7.5-6.03 7.5-11V4.5L9 1z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M6 11l2 2 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        {{ __t('Безпека') }}
    </a>
    <a href="{{ route('profile.logout') }}" class="account__link">
        <svg class="icon" width="18" height="22" viewBox="0 0 18 22" fill="none">
            <path d="M11.925 1H1.012V21H11.925" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            <line x1="8.012" y1="11.502" x2="15.262" y2="11.502" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M12.263 15.096L16.992 11l-4.729-4.096" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        {{ __t('Вийти з акаунту') }}
    </a>
</nav>
