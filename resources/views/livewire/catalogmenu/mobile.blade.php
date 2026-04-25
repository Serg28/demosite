<div itemscope itemtype="http://schema.org/SiteNavigationElement">
    <div class="header-mobile-menu">

        <livewire:partials.langmenu view="livewire.partials.langmenu-mob" :page="$page??null" />

        <div class="mob-menu-bottom-row" data-simplebar data-simplebar-auto-hide="false">
            @if($menuCatalog->isNotEmpty())
            <ul class="menu">
                @foreach($menuCatalog as $item)
                <li class="{{ $item->children->isNotEmpty() ? 'menu-item-has-children': '' }} {!! $item->css_class ?? '' !!}" data-menu="{{$loop->index}}">
                    <div href="" class="flex v--center side-bar-link">
                        <x-catalogmenu.item-icon :picture="$item->picture" :alt="e($item->t('title'))" />
                        <span class="text">{!! $item->t('title') !!}</span>
                    </div>
                </li>
                @endforeach
            </ul>
            @endif


            <div class="menu-links flex fd--column">
                <div class="btn flex v--center get-login-popup">
                    <div class="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="19" viewBox="0 0 18 19" fill="none">
                            <path d="M4.5 4.5C4.5 6.981 6.519 9 9 9C11.481 9 13.5 6.981 13.5 4.5C13.5 2.019 11.481 0 9 0C6.519 0 4.5 2.019 4.5 4.5ZM17 19H18V18C18 14.141 14.859 11 11 11H7C3.14 11 0 14.141 0 18V19H1H2H16H17Z" fill="#0A0527"/>
                        </svg>
                    </div>
                    <div class="text">Вхід</div>
                </div>
                <a href="" class="btn flex v--center color--black">
                            <span class="icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="26" height="24" viewBox="0 0 26 24" fill="none">
                                    <path d="M6 21C7.76312 21 9.29999 19.8001 9.72761 18.0896L10 17H6H2L2.27239 18.0896C2.70001 19.8001 4.23688 21 6 21Z" fill="#0A0527"/>
                                    <path d="M16 15L16.2724 16.0896C16.7 17.8001 18.2369 19 20 19C21.7631 19 23.3 17.8001 23.7276 16.0896L24 15H16Z" fill="#0A0527"/>
                                    <path d="M20 7V5L15.25 5.67857M20 7L16 15M20 7L24 15M16 15L16.2724 16.0896C16.7 17.8001 18.2369 19 20 19V19C21.7631 19 23.3 17.8001 23.7276 16.0896L24 15M16 15H24M6 10V7L10.5 6.35714M6 10L10 17M6 10L2 17M10 17L9.72761 18.0896C9.29999 19.8001 7.76312 21 6 21V21V21C4.23688 21 2.70001 19.8001 2.27239 18.0896L2 17M10 17H6H2" stroke="#0A0527" stroke-width="2"/>
                                    <circle cx="13" cy="6" r="2" stroke="#0A0527" stroke-width="2"/>
                                </svg>
                            </span>
                    <span class="text">Порівняння</span>
                </a>
                <a href="" class="btn flex v--center color--black">
                            <span class="icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path d="M16.7778 4C14.2982 4 12.8743 5.30695 12.2998 5.98929C12.1533 6.16341 11.8467 6.16341 11.7002 5.98929C11.1257 5.30695 9.70184 4 7.22222 4C4.37778 4 2 6.39342 2 9.23421C2 15.3185 10.6771 20.2769 11.8657 20.9276C11.952 20.9748 12.048 20.9748 12.1343 20.9276C13.3229 20.2769 22 15.3185 22 9.23421C22 6.39342 19.6222 4 16.7778 4Z" fill="#0A0527"/>
                                </svg>
                            </span>
                    <span class="text">Збережене</span>
                </a>
            </div>
            @if($menuHeader->isNotEmpty())
                <ul class="nav-menu flex v--center fsz-14">
                    @foreach($menuHeader as $item)
                    <li wire:key="mob-left-topmenu-{{$item->id}}">
                        <x-a href="{{getUrl($item->getUrl())}}" wire:navigate.hover itemprop="url">{!! $item->t('title') !!}</x-a>
                    </li>
                    @endforeach
                </ul>
            @endif
            <div class="info-block">
                <a href="tel:+38 (044) 334 71 94" class="tel fw-600 flex color--black">+38 (044) 334 71 94</a>
                <a href="tel:+38 (095) 888 56 77" class="tel fw-600 flex color--black mt-4">+38 (095) 888 56 77</a>
                <p class="fsz-13 mt-8">Пн-Пт. с 10:00 до 18:00</p>
                <p class="fsz-12 color--gray mt-8">Ми у месенджерах</p>
                <div class="socs-wrap flex v--center mt-8">
                    <a href="" class="socs"><img src="/assets/images/telegram.svg" alt=""></a>
                    <a href="" class="socs"><img src="/assets/images/viber.svg" alt=""></a>
                </div>
            </div>
        </div>
    </div>
    <div class="header-mobile-sub-menu header-mobile-sub-menu-1">
        <div class="mob-menu-top-row flex v--center h--between">
            <div class="heading fsz-20 fw-600">Дрібна побутова техніка</div>
            <div class="closer"><img src="/assets/images/closer.svg" alt=""></div>
        </div>
        <div class="bottom-menu">
            <div class="back color--blue flex"><img src="/assets/images/back-arrow.svg" alt="">Усі категорії</div>
            <div class="menu">
                <ul class="title-menu-wrap">
                    <li><a href="">Пральні машини</a>
                        <ul class="sub-menu">
                            <li><a href="">Аксесуари для пральних та сушильних машин</a></li>
                        </ul>
                    </li>
                    <li><a href="">Сушильні машини</a></li>
                    <li><a href="">Електродуховки</a></li>
                    <li><a href="">Холодильники</a>
                        <ul class="sub-menu">
                            <li><a href="">Двокамерні холодильники</a></li>
                            <li><a href="">Холодильники Side by Side</a></li>
                        </ul>
                    </li>
                    <li><a href="">Морозильні камери</a></li>
                    <li><a href="">Винні шафи</a></li>
                    <li><a href="">Плити</a></li>
                    <li><a href="">Посудомийні машини</a></li>
                    <li><a href="">Охолодження і клімат</a>
                        <ul class="sub-menu">
                            <li><a href="">Рекуператори і припливні установки</a></li>
                            <li><a href="">Кондиціонери</a></li>
                            <li><a href="">Очищувачі повітря</a></li>
                            <li><a href="">Вентилятори</a></li>
                            <li><a href="">Зволожувачі повітря</a></li>
                            <li><a href="">Осушувачі повітря</a></li>
                            <li><a href="">Витяжні вентилятори</a></li>
                        </ul>
                    </li>
                    <li><a href="">Обігривачі</a></li>
                    <li><a href="">Мінімийки високого тиску</a></li>
                    <li><a href="">Сад, город</a></li>
                    <li><a href="">Сушилки для рук</a></li>
                    <li><a href="">Генератори</a></li>
                    <li><a href="">Електроінструмент</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="header-mobile-sub-menu header-mobile-sub-menu-2">
        <div class="mob-menu-top-row flex v--center h--between">
            <div class="heading fsz-20 fw-600">Дрібна побутова техніка</div>
            <div class="closer"><img src="/assets/images/closer.svg" alt=""></div>
        </div>
        <div class="bottom-menu">
            <div class="back color--blue flex"><img src="/assets/images/back-arrow.svg" alt="">Усі категорії</div>
            <div class="menu">
                <ul class="title-menu-wrap">
                    <li><a href="">Пральні машини</a>
                        <ul class="sub-menu">
                            <li><a href="">Аксесуари для пральних та сушильних машин</a></li>
                        </ul>
                    </li>
                    <li><a href="">Сушильні машини</a></li>
                    <li><a href="">Електродуховки</a></li>
                    <li><a href="">Холодильники</a>
                        <ul class="sub-menu">
                            <li><a href="">Двокамерні холодильники</a></li>
                            <li><a href="">Холодильники Side by Side</a></li>
                        </ul>
                    </li>
                    <li><a href="">Морозильні камери</a></li>
                    <li><a href="">Винні шафи</a></li>
                    <li><a href="">Плити</a></li>
                    <li><a href="">Посудомийні машини</a></li>
                    <li><a href="">Охолодження і клімат</a>
                        <ul class="sub-menu">
                            <li><a href="">Рекуператори і припливні установки</a></li>
                            <li><a href="">Кондиціонери</a></li>
                            <li><a href="">Очищувачі повітря</a></li>
                            <li><a href="">Вентилятори</a></li>
                            <li><a href="">Зволожувачі повітря</a></li>
                            <li><a href="">Осушувачі повітря</a></li>
                            <li><a href="">Витяжні вентилятори</a></li>
                        </ul>
                    </li>
                    <li><a href="">Обігривачі</a></li>
                    <li><a href="">Мінімийки високого тиску</a></li>
                    <li><a href="">Сад, город</a></li>
                    <li><a href="">Сушилки для рук</a></li>
                    <li><a href="">Генератори</a></li>
                    <li><a href="">Електроінструмент</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="header-mobile-sub-menu header-mobile-sub-menu-3">
        <div class="mob-menu-top-row flex v--center h--between">
            <div class="heading fsz-20 fw-600">Дрібна побутова техніка</div>
            <div class="closer"><img src="/assets/images/closer.svg" alt=""></div>
        </div>
        <div class="bottom-menu">
            <div class="back color--blue flex"><img src="/assets/images/back-arrow.svg" alt="">Усі категорії</div>
            <div class="menu">
                <ul class="title-menu-wrap">
                    <li><a href="">Пральні машини</a>
                        <ul class="sub-menu">
                            <li><a href="">Аксесуари для пральних та сушильних машин</a></li>
                        </ul>
                    </li>
                    <li><a href="">Сушильні машини</a></li>
                    <li><a href="">Електродуховки</a></li>
                    <li><a href="">Холодильники</a>
                        <ul class="sub-menu">
                            <li><a href="">Двокамерні холодильники</a></li>
                            <li><a href="">Холодильники Side by Side</a></li>
                        </ul>
                    </li>
                    <li><a href="">Морозильні камери</a></li>
                    <li><a href="">Винні шафи</a></li>
                    <li><a href="">Плити</a></li>
                    <li><a href="">Посудомийні машини</a></li>
                    <li><a href="">Охолодження і клімат</a>
                        <ul class="sub-menu">
                            <li><a href="">Рекуператори і припливні установки</a></li>
                            <li><a href="">Кондиціонери</a></li>
                            <li><a href="">Очищувачі повітря</a></li>
                            <li><a href="">Вентилятори</a></li>
                            <li><a href="">Зволожувачі повітря</a></li>
                            <li><a href="">Осушувачі повітря</a></li>
                            <li><a href="">Витяжні вентилятори</a></li>
                        </ul>
                    </li>
                    <li><a href="">Обігривачі</a></li>
                    <li><a href="">Мінімийки високого тиску</a></li>
                    <li><a href="">Сад, город</a></li>
                    <li><a href="">Сушилки для рук</a></li>
                    <li><a href="">Генератори</a></li>
                    <li><a href="">Електроінструмент</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="header-mobile-sub-menu header-mobile-sub-menu-4">
        <div class="mob-menu-top-row flex v--center h--between">
            <div class="heading fsz-20 fw-600">Дрібна побутова техніка</div>
            <div class="closer"><img src="/assets/images/closer.svg" alt=""></div>
        </div>
        <div class="bottom-menu">
            <div class="back color--blue flex"><img src="/assets/images/back-arrow.svg" alt="">Усі категорії</div>
            <div class="menu">
                <ul class="title-menu-wrap">
                    <li><a href="">Пральні машини</a>
                        <ul class="sub-menu">
                            <li><a href="">Аксесуари для пральних та сушильних машин</a></li>
                        </ul>
                    </li>
                    <li><a href="">Сушильні машини</a></li>
                    <li><a href="">Електродуховки</a></li>
                    <li><a href="">Холодильники</a>
                        <ul class="sub-menu">
                            <li><a href="">Двокамерні холодильники</a></li>
                            <li><a href="">Холодильники Side by Side</a></li>
                        </ul>
                    </li>
                    <li><a href="">Морозильні камери</a></li>
                    <li><a href="">Винні шафи</a></li>
                    <li><a href="">Плити</a></li>
                    <li><a href="">Посудомийні машини</a></li>
                    <li><a href="">Охолодження і клімат</a>
                        <ul class="sub-menu">
                            <li><a href="">Рекуператори і припливні установки</a></li>
                            <li><a href="">Кондиціонери</a></li>
                            <li><a href="">Очищувачі повітря</a></li>
                            <li><a href="">Вентилятори</a></li>
                            <li><a href="">Зволожувачі повітря</a></li>
                            <li><a href="">Осушувачі повітря</a></li>
                            <li><a href="">Витяжні вентилятори</a></li>
                        </ul>
                    </li>
                    <li><a href="">Обігривачі</a></li>
                    <li><a href="">Мінімийки високого тиску</a></li>
                    <li><a href="">Сад, город</a></li>
                    <li><a href="">Сушилки для рук</a></li>
                    <li><a href="">Генератори</a></li>
                    <li><a href="">Електроінструмент</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="header-mobile-sub-menu header-mobile-sub-menu-5">
        <div class="mob-menu-top-row flex v--center h--between">
            <div class="heading fsz-20 fw-600">Дрібна побутова техніка</div>
            <div class="closer"><img src="/assets/images/closer.svg" alt=""></div>
        </div>
        <div class="bottom-menu">
            <div class="back color--blue flex"><img src="/assets/images/back-arrow.svg" alt="">Усі категорії</div>
            <div class="menu">
                <ul class="title-menu-wrap">
                    <li><a href="">Пральні машини</a>
                        <ul class="sub-menu">
                            <li><a href="">Аксесуари для пральних та сушильних машин</a></li>
                        </ul>
                    </li>
                    <li><a href="">Сушильні машини</a></li>
                    <li><a href="">Електродуховки</a></li>
                    <li><a href="">Холодильники</a>
                        <ul class="sub-menu">
                            <li><a href="">Двокамерні холодильники</a></li>
                            <li><a href="">Холодильники Side by Side</a></li>
                        </ul>
                    </li>
                    <li><a href="">Морозильні камери</a></li>
                    <li><a href="">Винні шафи</a></li>
                    <li><a href="">Плити</a></li>
                    <li><a href="">Посудомийні машини</a></li>
                    <li><a href="">Охолодження і клімат</a>
                        <ul class="sub-menu">
                            <li><a href="">Рекуператори і припливні установки</a></li>
                            <li><a href="">Кондиціонери</a></li>
                            <li><a href="">Очищувачі повітря</a></li>
                            <li><a href="">Вентилятори</a></li>
                            <li><a href="">Зволожувачі повітря</a></li>
                            <li><a href="">Осушувачі повітря</a></li>
                            <li><a href="">Витяжні вентилятори</a></li>
                        </ul>
                    </li>
                    <li><a href="">Обігривачі</a></li>
                    <li><a href="">Мінімийки високого тиску</a></li>
                    <li><a href="">Сад, город</a></li>
                    <li><a href="">Сушилки для рук</a></li>
                    <li><a href="">Генератори</a></li>
                    <li><a href="">Електроінструмент</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="header-mobile-sub-menu header-mobile-sub-menu-6">
        <div class="mob-menu-top-row flex v--center h--between">
            <div class="heading fsz-20 fw-600">Дрібна побутова техніка</div>
            <div class="closer"><img src="/assets/images/closer.svg" alt=""></div>
        </div>
        <div class="bottom-menu">
            <div class="back color--blue flex"><img src="/assets/images/back-arrow.svg" alt="">Усі категорії</div>
            <div class="menu">
                <ul class="title-menu-wrap">
                    <li><a href="">Пральні машини</a>
                        <ul class="sub-menu">
                            <li><a href="">Аксесуари для пральних та сушильних машин</a></li>
                        </ul>
                    </li>
                    <li><a href="">Сушильні машини</a></li>
                    <li><a href="">Електродуховки</a></li>
                    <li><a href="">Холодильники</a>
                        <ul class="sub-menu">
                            <li><a href="">Двокамерні холодильники</a></li>
                            <li><a href="">Холодильники Side by Side</a></li>
                        </ul>
                    </li>
                    <li><a href="">Морозильні камери</a></li>
                    <li><a href="">Винні шафи</a></li>
                    <li><a href="">Плити</a></li>
                    <li><a href="">Посудомийні машини</a></li>
                    <li><a href="">Охолодження і клімат</a>
                        <ul class="sub-menu">
                            <li><a href="">Рекуператори і припливні установки</a></li>
                            <li><a href="">Кондиціонери</a></li>
                            <li><a href="">Очищувачі повітря</a></li>
                            <li><a href="">Вентилятори</a></li>
                            <li><a href="">Зволожувачі повітря</a></li>
                            <li><a href="">Осушувачі повітря</a></li>
                            <li><a href="">Витяжні вентилятори</a></li>
                        </ul>
                    </li>
                    <li><a href="">Обігривачі</a></li>
                    <li><a href="">Мінімийки високого тиску</a></li>
                    <li><a href="">Сад, город</a></li>
                    <li><a href="">Сушилки для рук</a></li>
                    <li><a href="">Генератори</a></li>
                    <li><a href="">Електроінструмент</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="header-mobile-sub-menu header-mobile-sub-menu-7">
        <div class="mob-menu-top-row flex v--center h--between">
            <div class="heading fsz-20 fw-600">Дрібна побутова техніка</div>
            <div class="closer"><img src="/assets/images/closer.svg" alt=""></div>
        </div>
        <div class="bottom-menu">
            <div class="back color--blue flex"><img src="/assets/images/back-arrow.svg" alt="">Усі категорії</div>
            <div class="menu">
                <ul class="title-menu-wrap">
                    <li><a href="">Пральні машини</a>
                        <ul class="sub-menu">
                            <li><a href="">Аксесуари для пральних та сушильних машин</a></li>
                        </ul>
                    </li>
                    <li><a href="">Сушильні машини</a></li>
                    <li><a href="">Електродуховки</a></li>
                    <li><a href="">Холодильники</a>
                        <ul class="sub-menu">
                            <li><a href="">Двокамерні холодильники</a></li>
                            <li><a href="">Холодильники Side by Side</a></li>
                        </ul>
                    </li>
                    <li><a href="">Морозильні камери</a></li>
                    <li><a href="">Винні шафи</a></li>
                    <li><a href="">Плити</a></li>
                    <li><a href="">Посудомийні машини</a></li>
                    <li><a href="">Охолодження і клімат</a>
                        <ul class="sub-menu">
                            <li><a href="">Рекуператори і припливні установки</a></li>
                            <li><a href="">Кондиціонери</a></li>
                            <li><a href="">Очищувачі повітря</a></li>
                            <li><a href="">Вентилятори</a></li>
                            <li><a href="">Зволожувачі повітря</a></li>
                            <li><a href="">Осушувачі повітря</a></li>
                            <li><a href="">Витяжні вентилятори</a></li>
                        </ul>
                    </li>
                    <li><a href="">Обігривачі</a></li>
                    <li><a href="">Мінімийки високого тиску</a></li>
                    <li><a href="">Сад, город</a></li>
                    <li><a href="">Сушилки для рук</a></li>
                    <li><a href="">Генератори</a></li>
                    <li><a href="">Електроінструмент</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="header-mobile-sub-menu header-mobile-sub-menu-8">
        <div class="mob-menu-top-row flex v--center h--between">
            <div class="heading fsz-20 fw-600">Дрібна побутова техніка</div>
            <div class="closer"><img src="/assets/images/closer.svg" alt=""></div>
        </div>
        <div class="bottom-menu">
            <div class="back color--blue flex"><img src="/assets/images/back-arrow.svg" alt="">Усі категорії</div>
            <div class="menu">
                <ul class="title-menu-wrap">
                    <li><a href="">Пральні машини</a>
                        <ul class="sub-menu">
                            <li><a href="">Аксесуари для пральних та сушильних машин</a></li>
                        </ul>
                    </li>
                    <li><a href="">Сушильні машини</a></li>
                    <li><a href="">Електродуховки</a></li>
                    <li><a href="">Холодильники</a>
                        <ul class="sub-menu">
                            <li><a href="">Двокамерні холодильники</a></li>
                            <li><a href="">Холодильники Side by Side</a></li>
                        </ul>
                    </li>
                    <li><a href="">Морозильні камери</a></li>
                    <li><a href="">Винні шафи</a></li>
                    <li><a href="">Плити</a></li>
                    <li><a href="">Посудомийні машини</a></li>
                    <li><a href="">Охолодження і клімат</a>
                        <ul class="sub-menu">
                            <li><a href="">Рекуператори і припливні установки</a></li>
                            <li><a href="">Кондиціонери</a></li>
                            <li><a href="">Очищувачі повітря</a></li>
                            <li><a href="">Вентилятори</a></li>
                            <li><a href="">Зволожувачі повітря</a></li>
                            <li><a href="">Осушувачі повітря</a></li>
                            <li><a href="">Витяжні вентилятори</a></li>
                        </ul>
                    </li>
                    <li><a href="">Обігривачі</a></li>
                    <li><a href="">Мінімийки високого тиску</a></li>
                    <li><a href="">Сад, город</a></li>
                    <li><a href="">Сушилки для рук</a></li>
                    <li><a href="">Генератори</a></li>
                    <li><a href="">Електроінструмент</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="header-mobile-sub-menu header-mobile-sub-menu-9">
        <div class="mob-menu-top-row flex v--center h--between">
            <div class="heading fsz-20 fw-600">Дрібна побутова техніка</div>
            <div class="closer"><img src="/assets/images/closer.svg" alt=""></div>
        </div>
        <div class="bottom-menu">
            <div class="back color--blue flex"><img src="/assets/images/back-arrow.svg" alt="">Усі категорії</div>
            <div class="menu">
                <ul class="title-menu-wrap">
                    <li><a href="">Пральні машини</a>
                        <ul class="sub-menu">
                            <li><a href="">Аксесуари для пральних та сушильних машин</a></li>
                        </ul>
                    </li>
                    <li><a href="">Сушильні машини</a></li>
                    <li><a href="">Електродуховки</a></li>
                    <li><a href="">Холодильники</a>
                        <ul class="sub-menu">
                            <li><a href="">Двокамерні холодильники</a></li>
                            <li><a href="">Холодильники Side by Side</a></li>
                        </ul>
                    </li>
                    <li><a href="">Морозильні камери</a></li>
                    <li><a href="">Винні шафи</a></li>
                    <li><a href="">Плити</a></li>
                    <li><a href="">Посудомийні машини</a></li>
                    <li><a href="">Охолодження і клімат</a>
                        <ul class="sub-menu">
                            <li><a href="">Рекуператори і припливні установки</a></li>
                            <li><a href="">Кондиціонери</a></li>
                            <li><a href="">Очищувачі повітря</a></li>
                            <li><a href="">Вентилятори</a></li>
                            <li><a href="">Зволожувачі повітря</a></li>
                            <li><a href="">Осушувачі повітря</a></li>
                            <li><a href="">Витяжні вентилятори</a></li>
                        </ul>
                    </li>
                    <li><a href="">Обігривачі</a></li>
                    <li><a href="">Мінімийки високого тиску</a></li>
                    <li><a href="">Сад, город</a></li>
                    <li><a href="">Сушилки для рук</a></li>
                    <li><a href="">Генератори</a></li>
                    <li><a href="">Електроінструмент</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="header-mobile-sub-menu header-mobile-sub-menu-10">
        <div class="mob-menu-top-row flex v--center h--between">
            <div class="heading fsz-20 fw-600">Дрібна побутова техніка</div>
            <div class="closer"><img src="/assets/images/closer.svg" alt=""></div>
        </div>
        <div class="bottom-menu">
            <div class="back color--blue flex"><img src="/assets/images/back-arrow.svg" alt="">Усі категорії</div>
            <div class="menu">
                <ul class="title-menu-wrap">
                    <li><a href="">Пральні машини</a>
                        <ul class="sub-menu">
                            <li><a href="">Аксесуари для пральних та сушильних машин</a></li>
                        </ul>
                    </li>
                    <li><a href="">Сушильні машини</a></li>
                    <li><a href="">Електродуховки</a></li>
                    <li><a href="">Холодильники</a>
                        <ul class="sub-menu">
                            <li><a href="">Двокамерні холодильники</a></li>
                            <li><a href="">Холодильники Side by Side</a></li>
                        </ul>
                    </li>
                    <li><a href="">Морозильні камери</a></li>
                    <li><a href="">Винні шафи</a></li>
                    <li><a href="">Плити</a></li>
                    <li><a href="">Посудомийні машини</a></li>
                    <li><a href="">Охолодження і клімат</a>
                        <ul class="sub-menu">
                            <li><a href="">Рекуператори і припливні установки</a></li>
                            <li><a href="">Кондиціонери</a></li>
                            <li><a href="">Очищувачі повітря</a></li>
                            <li><a href="">Вентилятори</a></li>
                            <li><a href="">Зволожувачі повітря</a></li>
                            <li><a href="">Осушувачі повітря</a></li>
                            <li><a href="">Витяжні вентилятори</a></li>
                        </ul>
                    </li>
                    <li><a href="">Обігривачі</a></li>
                    <li><a href="">Мінімийки високого тиску</a></li>
                    <li><a href="">Сад, город</a></li>
                    <li><a href="">Сушилки для рук</a></li>
                    <li><a href="">Генератори</a></li>
                    <li><a href="">Електроінструмент</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="header-mobile-sub-menu header-mobile-sub-menu-11">
        <div class="mob-menu-top-row flex v--center h--between">
            <div class="heading fsz-20 fw-600">Дрібна побутова техніка</div>
            <div class="closer"><img src="/assets/images/closer.svg" alt=""></div>
        </div>
        <div class="bottom-menu">
            <div class="back color--blue flex"><img src="/assets/images/back-arrow.svg" alt="">Усі категорії</div>
            <div class="menu">
                <ul class="title-menu-wrap">
                    <li><a href="">Пральні машини</a>
                        <ul class="sub-menu">
                            <li><a href="">Аксесуари для пральних та сушильних машин</a></li>
                        </ul>
                    </li>
                    <li><a href="">Сушильні машини</a></li>
                    <li><a href="">Електродуховки</a></li>
                    <li><a href="">Холодильники</a>
                        <ul class="sub-menu">
                            <li><a href="">Двокамерні холодильники</a></li>
                            <li><a href="">Холодильники Side by Side</a></li>
                        </ul>
                    </li>
                    <li><a href="">Морозильні камери</a></li>
                    <li><a href="">Винні шафи</a></li>
                    <li><a href="">Плити</a></li>
                    <li><a href="">Посудомийні машини</a></li>
                    <li><a href="">Охолодження і клімат</a>
                        <ul class="sub-menu">
                            <li><a href="">Рекуператори і припливні установки</a></li>
                            <li><a href="">Кондиціонери</a></li>
                            <li><a href="">Очищувачі повітря</a></li>
                            <li><a href="">Вентилятори</a></li>
                            <li><a href="">Зволожувачі повітря</a></li>
                            <li><a href="">Осушувачі повітря</a></li>
                            <li><a href="">Витяжні вентилятори</a></li>
                        </ul>
                    </li>
                    <li><a href="">Обігривачі</a></li>
                    <li><a href="">Мінімийки високого тиску</a></li>
                    <li><a href="">Сад, город</a></li>
                    <li><a href="">Сушилки для рук</a></li>
                    <li><a href="">Генератори</a></li>
                    <li><a href="">Електроінструмент</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="header-mobile-sub-menu header-mobile-sub-menu-12">
        <div class="mob-menu-top-row flex v--center h--between">
            <div class="heading fsz-20 fw-600">Дрібна побутова техніка</div>
            <div class="closer"><img src="/assets/images/closer.svg" alt=""></div>
        </div>
        <div class="bottom-menu">
            <div class="back color--blue flex"><img src="/assets/images/back-arrow.svg" alt="">Усі категорії</div>
            <div class="menu">
                <ul class="title-menu-wrap">
                    <li><a href="">Пральні машини</a>
                        <ul class="sub-menu">
                            <li><a href="">Аксесуари для пральних та сушильних машин</a></li>
                        </ul>
                    </li>
                    <li><a href="">Сушильні машини</a></li>
                    <li><a href="">Електродуховки</a></li>
                    <li><a href="">Холодильники</a>
                        <ul class="sub-menu">
                            <li><a href="">Двокамерні холодильники</a></li>
                            <li><a href="">Холодильники Side by Side</a></li>
                        </ul>
                    </li>
                    <li><a href="">Морозильні камери</a></li>
                    <li><a href="">Винні шафи</a></li>
                    <li><a href="">Плити</a></li>
                    <li><a href="">Посудомийні машини</a></li>
                    <li><a href="">Охолодження і клімат</a>
                        <ul class="sub-menu">
                            <li><a href="">Рекуператори і припливні установки</a></li>
                            <li><a href="">Кондиціонери</a></li>
                            <li><a href="">Очищувачі повітря</a></li>
                            <li><a href="">Вентилятори</a></li>
                            <li><a href="">Зволожувачі повітря</a></li>
                            <li><a href="">Осушувачі повітря</a></li>
                            <li><a href="">Витяжні вентилятори</a></li>
                        </ul>
                    </li>
                    <li><a href="">Обігривачі</a></li>
                    <li><a href="">Мінімийки високого тиску</a></li>
                    <li><a href="">Сад, город</a></li>
                    <li><a href="">Сушилки для рук</a></li>
                    <li><a href="">Генератори</a></li>
                    <li><a href="">Електроінструмент</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="header-mobile-sub-menu header-mobile-sub-menu-13">
        <div class="mob-menu-top-row flex v--center h--between">
            <div class="heading fsz-20 fw-600">Дрібна побутова техніка</div>
            <div class="closer"><img src="/assets/images/closer.svg" alt=""></div>
        </div>
        <div class="bottom-menu">
            <div class="back color--blue flex"><img src="/assets/images/back-arrow.svg" alt="">Усі категорії</div>
            <div class="menu">
                <ul class="title-menu-wrap">
                    <li><a href="">Пральні машини</a>
                        <ul class="sub-menu">
                            <li><a href="">Аксесуари для пральних та сушильних машин</a></li>
                        </ul>
                    </li>
                    <li><a href="">Сушильні машини</a></li>
                    <li><a href="">Електродуховки</a></li>
                    <li><a href="">Холодильники</a>
                        <ul class="sub-menu">
                            <li><a href="">Двокамерні холодильники</a></li>
                            <li><a href="">Холодильники Side by Side</a></li>
                        </ul>
                    </li>
                    <li><a href="">Морозильні камери</a></li>
                    <li><a href="">Винні шафи</a></li>
                    <li><a href="">Плити</a></li>
                    <li><a href="">Посудомийні машини</a></li>
                    <li><a href="">Охолодження і клімат</a>
                        <ul class="sub-menu">
                            <li><a href="">Рекуператори і припливні установки</a></li>
                            <li><a href="">Кондиціонери</a></li>
                            <li><a href="">Очищувачі повітря</a></li>
                            <li><a href="">Вентилятори</a></li>
                            <li><a href="">Зволожувачі повітря</a></li>
                            <li><a href="">Осушувачі повітря</a></li>
                            <li><a href="">Витяжні вентилятори</a></li>
                        </ul>
                    </li>
                    <li><a href="">Обігривачі</a></li>
                    <li><a href="">Мінімийки високого тиску</a></li>
                    <li><a href="">Сад, город</a></li>
                    <li><a href="">Сушилки для рук</a></li>
                    <li><a href="">Генератори</a></li>
                    <li><a href="">Електроінструмент</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>