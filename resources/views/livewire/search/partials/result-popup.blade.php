<div class="hidden absolute" x-show="open" style="display: none">
    <div class="clicable-zone"></div>
    <div class="search-result-wrap flex v--start pt-10 pb-15">
        <div class="right pl-0" style="width: 100%">
            @if(!empty($count))
                <div class="mb-15 ml-10">
                    <a class="fsz-14 color--gray" href="{{route('search-result.page')}}?text={{$text}}" class="all-result-search">{{__t('Всі результати пошуку')}}</a>
                </div>
                {{-- Выводим товары --}}
                <ul class="fd--column products pr-0 flex">
                    @loop($products as $product)
                    <li>
                        <a href="{{$product->getUrl()}}" class="flex v--stretch product" wire:key="p-{{$product->id}}">
                            <div class="img mt-10 mb-10" style="height:48px ">
                                <img loading="lazy" src="{{$product->getImgPath(120, 120)}}" alt="{{e($product->t('title'))}}" width="120" height="120" style="height: 40px"></div>
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
                {{-- /Товары --}}
            @else
                <p class="fsz-14 color--gray mt-15">{{__t('На ваш запит нічого не знайдено. Уточніть свій запит')}}</p>
            @endif
        </div>
    </div>
</div>