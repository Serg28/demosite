{{-- Шаблон меню каталога в шапке сайта - десктопная версия --}}
<div>
    @if($menu->isNotEmpty())
        <div class="main-popup-wrap catalog-popup">
            <div class="popup-wrap" id="popup-wrap" itemscope itemtype="http://schema.org/SiteNavigationElement">
                <div class="popup flex v--stretch h--between">
                    <div class="title-side-bar">
                        <ul data-simplebar data-simplebar-auto-hide="false">
                            @foreach($menu as $item)
                                <li wire:key="catmenuTop-header-level-1-{{$item->id}}" class="{{ $item->children->isNotEmpty() ? 'menu-item-has-children': '' }} {!! $item->css_class ?? '' !!}" data-menu="{{$loop->index}}">
                                    <a href="{{getUrl($item->getUrl())}}" {!! $item->is_target_blank ? 'target="_blank"' : '' !!} wire:navigate.hover class="flex v--center side-bar-link" itemprop="url">
                                        <x-catalogmenu.item-icon :picture="$item->picture" :alt="e($item->t('title'))"/>
                                        <span class="text">{!! $item->t('title') !!}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                        <div class="before"></div>
                    </div>

                    @foreach($menu as $item)
                        @if($item->children->isNotEmpty())
                            <div wire:key="catmenuTop-header-right-level-1-{{$item->id}}" class="title-menu-wrap title-menu title-menu-{{$loop->index}}" >
                                <div class="menu-wrapper flex h--between v--start">
                                    <div class="menu" style="max-height: 430px; overflow: auto;scrollbar-width: thin;">
                                            @include('livewire.catalogmenu.partials.submenu', [
                                                'items' => $item->children,
                                                'topClass' => 'title-menu-wrap',
                                                'wire_key_prefix' => 'catmenuTop-header-right-submenu-level-2'
                                            ])
                                    </div>
                                    {{--<div class="baner">
                                        <img src="/assets/images/eco.png" alt="">
                                        <p class="title fsz18 fw-600">Новинки від ECOFLOW</p>
                                        <span class="fsz-13">Будь завжди на зв’язку</span>
                                    </div>--}}
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>


