<div>
    @if(!empty($topMenu))
        @foreach ($topMenu as $menuItem)
            @php
                $itemList = $menuItem->getParentMenu($menuItem->id)
            @endphp
            @if(!empty($itemList))
                <div wire:key="menu-column-{{$menuItem->id}}" class="menu-column">

                    <p class="column-title">{{ $menuItem->t('title') }}</p>
                    <div class="sub-menu">
                        <ul itemscope itemtype="http://schema.org/SiteNavigationElement">
                            @foreach ($itemList as $item)
                                @php
                                    $menuItemUrl = getUrl($item->getUrl());
                                @endphp
                                @if ($currentUrl !== $menuItemUrl)
                                    <li wire:key="menu-mob-{{ $item->id }}" @active('{{ $menuItemUrl }}')>
                                    <a wire:navigate href="{{ $menuItemUrl }}" itemprop="url">
                                        {{ $item->t('title') }}
                                    </a>
                                    </li>
                                @endif
                                @if ($currentUrl !== $menuItemUrl)
                                    <li wire:key="menu-mob-{{ $item->id }}" class="current">
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
</div>

