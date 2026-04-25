<div class="filter-wrapper ">
    <div class="filter-wrap">
        <div class="filter-close"></div>
        <a href="" class="filter-closer mob-visible"><img src="/img/pc.svg" alt=""></a>
        <div class="filter-heading">{{__t('Категории')}}</div>
        <div class="filter-container filter-block">
                <div class="filter-section filters_category_">


                    <!--<div class="filter-sub-heading"><img src="/img/a5.svg" alt=""> Категории:</div>-->
                    <div class="filter-wrapper">
                        <div class="top block_has_li has_label">
                                <ul class="category-wrap">
                                    <?php $i = 0; ?>
                                @foreach($categories as $category)
                                        <?php $i++; ?>
                                        <li><a href="" class="tag noupper">{{$category->t('title')}}</a></li>
                                @endforeach
                                </ul>
                        </div>
                        @if ($i > 10)
                        <div class="deployment-row ">
                            <a href="" class="d-flex ai-c open-filter"><span><img src="/img/a1.svg" alt=""></span>
                                <b data-text1='{{__t('Розгорнути')}}' data-text2='{{__t('Згорнути')}}'></b>
                            </a>
                        </div>
                        @endif
                    </div>
                </div>

        </div>

    </div>
</div>
