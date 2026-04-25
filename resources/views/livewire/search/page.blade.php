<div class="catalog_filter catalog @if(empty($count)) search-result mt-8 pb-80 @endif">
    <div class="container">
        <h2 class="fsz-34 fw-600 catalog-heading">{{str_replace('[term]', ($text ? '«'.$text.'»' : ''), __t('Результати пошуку [term]'))}}</h2>
        

        @if(!empty($count) && !empty($text))
            <div class="hidden-filter-buttons">
                {{--<div class="btn main-btn blue-small icon-left get-filter">
                    <div class="icon">
                        <img src="/assets/images/filter.svg" alt="{{__t('Фільтри')}}">
                    </div>{{__t('Фільтри')}}
                </div> --}}
                @include('partials.sorting_mobile')
            </div>
            <div class="catalog__wrap search-result">

                {{-- Популярные категории --}}
                <livewire:partials.sidebarmenu-seocatalog />
                {{-- /Популярные категории --}}

                {{--
                <div class="catalog-side-bar">
                    <div class="cat-wrapper">
                        <p class="fsz-18 fw-600">Категорії</p>
                        <div class="cat-wrap mt-16 pt-16 pb-16 mb-16 flex fd--column">
                            <div class="cat-row">
                                <div class="visible flex v--center h--between">
                                    <p>Телефони і аксесуари</p>
                                    <div class="arrow">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="10" height="6" viewBox="0 0 10 6" fill="none">
                                            <path d="M1 1L5 5L9 1" stroke="#0A0527"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="hidden">
                                    <a href="" class="cat color--gray flex mt-8">Смартфони</a>
                                    <a href="" class="cat color--gray flex mt-8">Чохли до смартфонів</a>
                                    <a href="" class="cat color--gray flex mt-8">Зарядні пристрої</a>
                                </div>
                            </div>
                            <div class="cat-row">
                                <div class="visible flex v--center h--between">
                                    <p>Apple Store</p>
                                    <div class="arrow">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="10" height="6" viewBox="0 0 10 6" fill="none">
                                            <path d="M1 1L5 5L9 1" stroke="#0A0527"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="hidden">
                                    <a href="" class="cat color--gray flex mt-8">Смартфони</a>
                                    <a href="" class="cat color--gray flex mt-8">Чохли до смартфонів</a>
                                    <a href="" class="cat color--gray flex mt-8">Зарядні пристрої</a>
                                </div>
                            </div>
                            <div class="cat-row">
                                <div class="visible flex v--center h--between">
                                    <p>Аудіо та відео техніка</p>
                                    <div class="arrow">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="10" height="6" viewBox="0 0 10 6" fill="none">
                                            <path d="M1 1L5 5L9 1" stroke="#0A0527"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="hidden">
                                    <a href="" class="cat color--gray flex mt-8">Смартфони</a>
                                    <a href="" class="cat color--gray flex mt-8">Чохли до смартфонів</a>
                                    <a href="" class="cat color--gray flex mt-8">Зарядні пристрої</a>
                                </div>
                            </div>
                            <div class="cat-row">
                                <div class="visible flex v--center h--between">
                                    <p>Дрібна побутова техніка</p>
                                    <div class="arrow">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="10" height="6" viewBox="0 0 10 6" fill="none">
                                            <path d="M1 1L5 5L9 1" stroke="#0A0527"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="hidden">
                                    <a href="" class="cat color--gray flex mt-8">Смартфони</a>
                                    <a href="" class="cat color--gray flex mt-8">Чохли до смартфонів</a>
                                    <a href="" class="cat color--gray flex mt-8">Зарядні пристрої</a>
                                </div>
                            </div>
                            <div class="cat-row">
                                <div class="visible flex v--center h--between">
                                    <p>Гаджети та розваги</p>
                                    <div class="arrow">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="10" height="6" viewBox="0 0 10 6" fill="none">
                                            <path d="M1 1L5 5L9 1" stroke="#0A0527"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="hidden">
                                    <a href="" class="cat color--gray flex mt-8">Смартфони</a>
                                    <a href="" class="cat color--gray flex mt-8">Чохли до смартфонів</a>
                                    <a href="" class="cat color--gray flex mt-8">Зарядні пристрої</a>
                                </div>
                            </div>
                        </div>
                    </div>

                </div> --}}
                <div class="catalog-content">
                    <div class="flex-row flex v--center h--between mb-24">
                        <div class="filter-row flex v--center h--wrap">
                            <p class="fsz-16 color--gray">{{ str_replace(['[count]', '[plural]'], [$count, inflection($count, [__('товар'), __('товара'), __('товарів')])], __t('Знайдено [count] [plural]')) }}</p>
                            {{--<div class="label flex v--center">Телефони і аксесуари <div class="icon"><img src="assets/images/close-blue.svg" alt=""></div></div>
                            <span class="clear fsz-15 fw-600 color--blue">Скинути</span>--}}
                        </div>
                        @include('partials.sorting')
                    </div>

                    {{-- Товары --}}
                    @include('partials.products_container')
                    {{-- /Товары --}}

                </div>
            </div>
        @else
            <div class="search-result__wrap mt-24 p-40 br--br-8">
                <p>{{__t('За заданими параметрами не знайдено жодного товару')}}</p>
            </div>
        @endif
    </div>
</div>