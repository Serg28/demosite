<div class="right flex v--start">
    @if($footerMenu)
        @foreach ($footerMenu as $menuItem)
            @if($menuItem->children->isNotEmpty())
                <div class="menu-column" wire:key="menu-column-{{$menuItem->id}}">
                    <p class="menu-column-heading fsz-18 fw-600 color--white">{{ $menuItem->t('title') }}</p>
                    <div class="hidden">
                        <ul class="menu" itemscope itemtype="http://schema.org/SiteNavigationElement">
                            @foreach ($menuItem->children as $item)
                                @php
                                    $menuItemUrl = getUrl($item->getUrl());
                                @endphp
                                @if ($currentUrl !== $menuItemUrl)
                                    <li wire:key="column-{{$menuItem->id}}-menu-{{ $item->id }}" @active('{{ $menuItemUrl }}')>
                                    <a wire:navigate href="{{ $menuItemUrl }}" itemprop="url">
                                        {{ $item->t('title') }}
                                    </a>
                                    </li>
                                @else
                                    <li wire:key="column-{{$menuItem->id}}-menu-{{ $item->id }}" class="current">
                                        <span>{{ $item->t('title') }}</span>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
        @endforeach
    @endif
    @include('partials.footer.social')
</div>