<div class="center d--flex ai--center">
    @if(!empty($topMenu))
        @foreach ($topMenu as $menuItem)
                <div class="menu-column menu-opener-wrap">
                    <p class="column-title sub-menu-opener">{{ $menuItem->t('title') }}
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="7" viewBox="0 0 12 7" fill="none">
                            <path d="M11 1.63672L6.70711 5.92961C6.31658 6.32014 5.68342 6.32014 5.29289 5.92961L1 1.63672" stroke="white" stroke-width="1.5" stroke-linecap="round"/>
                        </svg>
                    </p>
                    <div class="sub-menu"></div>
                </div>
        @endforeach
    @endif
</div>