{{-- Шаблон меню каталога на главной странице первый экран (слева) - десктоп-версия --}}
<div class="flex">
    @if($menu->isNotEmpty())
        @foreach($menu as $item)
            @if($item->children->isNotEmpty())
                <div wire:key="catmenuTop-level-right-{{$item->id}}" class="title-menu-wrap title-menu title-menu-{{$loop->index}}">
                    <div class="menu-wrapper flex h--between v--start">
                        <div class="menu" style="max-height: 410px; overflow: auto;scrollbar-width: thin;">
                            {{--@foreach($item->children as $children) --}}
                                @include('livewire.catalogmenu.partials.submenu', [
                                    'items' => $item->children,
                                    'topClass' => 'title-menu-wrap',
                                    'wire_key_prefix' => 'catmenuTop-header-right-submenu-level-2'
                                ])
                            {{--@endforeach--}}
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

        <div class="title-side-bar">
            <ul data-simplebar data-simplebar-auto-hide="false">
                @foreach($menu as $item)
                    <li wire:key="catmenuTop-level-1-{{$item->id}}" class="{{ $item->children->isNotEmpty() ? 'menu-item-has-children': '' }} {!! $item->css_class ?? '' !!}" data-menu="{{$loop->index}}">
                        <a href="{{getUrl($item->getUrl())}}" {!! $item->is_target_blank ? 'target="_blank"' : '' !!} wire:navigate.hover class="flex v--center side-bar-link" itemprop="url">
                            <x-catalogmenu.item-icon :picture="$item->picture" :alt="$item->t('title')"/>
                            <span class="text">{!! $item->t('title') !!}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
</div>