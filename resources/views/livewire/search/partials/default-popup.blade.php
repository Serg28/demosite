<div class="hidden absolute">
    <div class="clicable-zone"></div>
    <div class="search-result-wrap flex v--start">
        <div class="left">
            <div class="row flex fd--column">
                <p class="fsz-14 color--gray">{{__t('Популяні запити')}}</p>
                <ul class="mt-16 flex fd--column">
                    <li><a href="{{route('search-result.page') . '?text='}}{{__t('Зарядна станція')}}">{{__t('Зарядна станція')}}</a><div class="delete"><img src="/assets/images/close.svg" alt=""></div></li>
                    <li><a href="{{route('search-result.page') . '?text='}}{{__t('Тепловізор')}}">{{__t('Тепловізор')}}</a><div class="delete"><img src="/assets/images/close.svg" alt=""></div></li>
                    <li><a href="{{route('search-result.page') . '?text='}}{{__t('Приставка Sony')}}">{{__t('Приставка Sony')}} </a><div class="delete"><img src="/assets/images/close.svg" alt=""></div></li>
                    <li><a href="{{route('search-result.page') . '?text='}}{{__t('Навушники Sony')}}">{{__t('Навушники Sony')}}</a><div class="delete"><img src="/assets/images/close.svg" alt=""></div></li>
                </ul>
            </div>

            {{-- Популярные категории --}}
            <div class="row flex fd--column">
                <p class="fsz-14 color--gray">{{__t('Популярні категорії')}}</p>
                <ul class="flex fd--column mt-16">
                    <li>
                        <a href="{{geturl('/noutbuki')}}" class="flex v--center relative">
                            <span class="icon">
                                <img loading="lazy" src="/storage/editor/fotos/3e67caf57bd7689c89e7016766e2e347_1713800641.svg" alt="{{__t('Ноутбуки')}}" width="24" height="24">
                            </span>
                            <span class="text">{{__t('Ноутбуки')}}</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{geturl('/smartfony')}}" class="flex v--center relative">
                            <span class="icon">
                                <img loading="lazy" src="/storage/editor/fotos/97f953bde94e231b4126e17c788c0f94_1713735735.svg" alt="{{__t('Смартфоны')}}" width="24" height="24">
                            </span>
                            <span class="text">{{__t('Смартфони')}}</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{geturl('/zariadni-stantsii')}}" class="flex v--center relative">
                            <span class="icon">
                                <img loading="lazy" src="/storage/editor/fotos/7d4c9eac8e16dc3b2638ab5489ad7098_1713802424.svg" alt="{{__t('Зарядні станції')}}" width="24" height="24">
                            </span>
                            <span class="text">{{__t('Зарядні станції')}}</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{geturl('/naushniki')}}" class="flex v--center relative">
                            <span class="icon">
                                <img loading="lazy" src="/storage/editor/fotos/79b59caabb940fbee39c70175c0e88fd_1713804192.svg" alt="{{__t('Навушники, гарнітури')}}" width="24" height="24">
                            </span>
                            <span class="text">{{__t('Навушники, гарнітури')}}</span>
                        </a>
                    </li>
                </ul>
            </div>
            {{-- /Популярные категории --}}
        </div>

        <div class="right">
            @if($popularProducts)
            <p class="fsz-14 color--gray">{{__t('Популярні товари')}}</p>
            <ul class="fd--column products  flex">

                @loop($popularProducts as $product)
                <li>
                    <a href="{{$product->getUrl()}}" class="flex v--stretch product" wire:key="p-{{$product->id}}">
                        <div class="img">
                            <img loading="lazy" src="{{$product->getImgPath(120, 120)}}" alt="{{e($product->t('title'))}}" width="120" height="120"></div>
                        <div class="info">
                            <div class="name fsz-14 color--black">{{$product->t('title')}}</div>
                            <div class="price flex v--center">
                                @if($product->price_old>$product->price)
                                    <s class="fsz-12 color--gray">@money($product->price_old) {{setting('currency')}}</s>
                                    <span class="ml-12 fw-500 color--red">@money($product->price) {{setting('currency')}}</span>
                                @else
                                    <span class="fw-600 color--black">@money($product->price) {{setting('currency')}}</span>
                                @endif
                            </div>
                        </div>
                    </a>
                </li>
                @endloop
            </ul>
            @endif
        </div>
    </div>
</div>