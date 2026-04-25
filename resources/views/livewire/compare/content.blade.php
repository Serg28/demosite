<div class="comparison" itemscope itemtype="https://schema.org/Article">
    <div class="container">
        <h2 class="fsz-34 fw-600 heading">{{ __t('Порівняння товарів') }}</h2>
        <div class="scrl mt-16">
            @if (count($categories))
            <div class="comparison__tabs flex v--center">
                @foreach($categories as $category)
                    <a href="{{route('compare', ['category' => $category->id])}}" class="tab flex v--start color--gray pb-4 @if($category->id==request()->get('category')) current @endif relative" >
                        {{ $category->t('title') }}<span class="fsz-12 ml-2">({{$categoryCounts[$category->id]}})</span>
                    </a>
                @endforeach
            </div>
            @endif
        </div>
    </div>
    <div class="screen screen-1 active" >
        <div class="fixed-comparison-row">
            <div class="container">

                <div class="custom-swiper-wrapper" >
                    <div class="comparison-fixed-swiper swiper" >
                        <div class="swiper-wrapper">
                            @if (count($products))
                                @foreach($products as $product)
                                    @include('partials.product_compare')
                                @endforeach
                                    <div class="swiper-slide">
                                        <div class="col flex v--center h--center">
                                            <a href="/" class="main-btn green"><strong>+</strong>{{__t('Додати ще товар')}}</a>
                                        </div>
                                    </div>
                            @else
                                <div class="swiper-slide">
                                    <div class="col flex v--center h--center">
                                        <p style="padding: 50px 0; text-align: center">{{__t('Відсутні товари для порівняння')}}</p>
                                    </div>
                                </div>
                                <div class="swiper-slide">
                                    <div class="col flex v--center h--center">
                                        <a href="/" class="main-btn green"><strong>+</strong>{{__t('Додати товар')}}</a>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="swiper-pagination custom-pagiation"></div>
                    </div>
                    <div class="custom-swiper-btn custom-swiper-btn-prev comparison-fixed-swiper-btn-prev"><img src="/assets/images/arrow-blue-left.svg" alt=""></div>
                    <div class="custom-swiper-btn custom-swiper-btn-next comparison-fixed-swiper-btn-next"><img src="/assets/images/arrow-blue-right-1.svg" alt=""></div>
                </div>
            </div>
        </div>
        <div class="comparison__wrap mt-24">
            <div class="container">
                @if (count($products))
                <div class="top-row flex v--center h--between pt-16 pb-16">
                    <div class="toggler-wrapper flex v--center">
                        <p>{{ __t('Тільки відмінності') }}</p>
                        <input type="checkbox" id="toggler-input-1" class="toggler-input" wire:model="showDifferences" wire:change="groupCharacteristics" wire:loading.attr="disabled">
                        <div class="toggler-wrap d-flex">
                            <label for="toggler-input-1" class="toggler"  wire:loading.class="hidden"></label>
                        </div>
                    </div>
                    <div class="clear fsz-15 fw-600 color--blue btn-clear-category" wire:click="clearCategory({{ $category_id }})">
                        <span>{{ __t('Очистити список') }}</span>
                        <span wire:loading.class="spinner" wire:target="clearCategory({{ $category_id }})"></span>
                    </div>
                </div>
                @endif
                <div class="comparison__swiper-wrapper-row custom-swiper-wrapper" wire:loading.class="opacity-50">
                    <div class="comparison-prod-swiper swiper custom-swiper" data-slider="1" >
                        <div class="swiper-wrapper">
                            @if (count($products))
                                @foreach($products as $product)
                                    @include('partials.product',['compare_delete' => true])
                                @endforeach
                                    <div class="swiper-slide">
                                        <a href="/" class="main-btn green"><strong>+</strong>{{ __t('Додати ще товар') }}</a>
                                    </div>
                            @else
                                <div class="swiper-slide">
                                    <div class="col flex v--center h--center">
                                        <p style="padding: 50px 0; text-align: center">{{__t('Відсутні товари для порівняння')}}</p>
                                    </div>
                                </div>
                                <div class="swiper-slide">
                                    <a href="/" class="main-btn green"><strong>+</strong>{{ __t('Додати товар') }}</a>
                                </div>
                            @endif
                        </div>
                        <div class="swiper-pagination custom-pagiation"></div>
                    </div>
                    <div class="custom-swiper-btn custom-swiper-btn-prev comparison-swiper-btn-prev"><img src="/assets/images/arrow-blue-left.svg" alt=""></div>
                    <div class="custom-swiper-btn custom-swiper-btn-next comparison-swiper-btn-next"><img src="/assets/images/arrow-blue-right-1.svg" alt=""></div>
                </div>
            </div>
        </div>

        @if (count($groupedCharacteristics))
            @loop ($groupedCharacteristics as $group)
            <div class="table-section mt-24" wire:loading.class="opacity-50">
                <div class="container">
                    <h3 class="fsz-24 fw-600">{{$group['group_title']}}</h3>
                </div>
                <div class="table-wrapper mt-16 pt-24 pb-24">
                    <div class="container">
                        <div class="table">
                            @loop ($group['characteristics'] as $characteristic)
                            <div class="table-row">
                                <span class="color--gray">{{ $characteristic['title'] }}</span>
                                <div class="table-swiper swiper" >
                                    <div class="swiper-wrapper">

                                        @loop ($characteristic['products'] as $key => $item)
                                        <div class="swiper-slide">
                                            <div class="cell-info">
                                                @if($item->isNotEmpty())
                                                    {!! $item->first() !!}
                                                @else
                                                    -
                                                @endif
                                            </div>
                                        </div>
                                        @endloop
                                    </div>
                                    <div class="swiper-button-prev table-prev"></div>
                                    <div class="swiper-button-next table-next"></div>
                                </div>
                            </div>
                            @endloop
                        </div>
                    </div>
                </div>
            </div>
            @endloop
        @else
            <div class="table-section mt-24" wire:loading.class="opacity-50">
                <div class="table-wrapper mt-16 pt-24 pb-24">
                    <div class="container">
                        <div class="table">
                            <div class="table-row">
                                <div class="table-swiper swiper" >
                                    <div class="swiper-wrapper">
                                        <div class="swiper-slide">

                                        </div>
                                    </div>
                                    <div class="swiper-button-prev table-prev"></div>
                                    <div class="swiper-button-next table-next"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

    </div>
</div>
@script
<script>
    initCompareSlider();
</script>
@endscript

