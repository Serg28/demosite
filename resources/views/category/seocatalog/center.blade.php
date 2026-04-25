<div class="catalog">
    <div class="container">
        <h2 class="fsz-34 fw-600 catalog-heading">{{$page->getSeoH1()}}</h2>

        <div class="catalog__wrap">

            {{-- Популярные категории --}}
            <livewire:partials.sidebarmenu-seocatalog class="sticky"/>
            {{-- /Популярные категории --}}

            @if($rubrics)
                @php
                    $count = count($rubrics);
                @endphp

                <div class="catalog-content">
                    <div class="catalog-level-1 flex v--stretch h--wrap">
                        @foreach($rubrics as $category)
                            @include('category.seocatalog.catalog_item')
                        @endforeach
                    </div>
                    <div class="catalog-hidden-button">
                        <div class="btn flex v--center color--blue fw-500 fsz-18">
                            <span class="text">Всі підкатегорії</span>
                            <div class="icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="9" viewBox="0 0 14 9"
                                     fill="none">
                                    <path d="M1.375 1.625L7 7.25L12.625 1.625" stroke="#2264DC" stroke-width="2"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

            @endif

        </div>
    </div>
</div>
