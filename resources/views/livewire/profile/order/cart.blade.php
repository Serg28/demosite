<div class="empty-block" x-data="{open: true}">
@if($productsInCart->isNotEmpty())
<div class="info-section br--br-4 bg--white mt-24" x-show="open">
    <div class="top-row flex v--center h--between">
        <p class="fsz-14">{!!  str_replace(['[count]', '[plural]'], [$cartCount, inflection($cartCount, [__('товар'), __('товара'), __('товарів')])], __t('У вашому кошику [count] [plural]')) !!}</p>
        <div class="closer" x-on:click="open = false"><img src="/assets/images/closer.svg" alt=""></div>
    </div>
    <div class="bottom-row flex fd--column">
            @foreach($productsInCart as $product)
                @php
                    $model = $product->model;
                @endphp
                <div class="prod-row flex v--center h--between">
                    <div class="left flex v--center">
                        <div class="img">
                            @if(!empty($model->picture))
                                {!! $model->getImg(60, '') !!}
                            @else
                                <img loading="lazy" src="{!! glide($model->firstOtherPicture, ['w'=>120, 'h'=>120]) !!}" alt="{{ e($model->t('title')) }}">
                            @endif

                        </div>
                        <div class="desc">
                            <a class="name color--black" href="{{$model->getUrl()}}">{{$model->t('title')}}</a>
                            <div class="row flex v--center mt-8">
                                <span class="fsz-12 color--gray">{{ __t('Код товару:') }} {{$model->code}}</span>
                                <span class="raiting flex v--center ml-24">
                                    <rating-stars>{{round($model->rating)}}</rating-stars>
                                    <span class="num color--black fsz-13 ml-4">{{ $model->count_comments }}</span>
                                </span>
                            </div>
                        </div>
                    </div>
                    @if($loop->first)
                    <div class="right flex v--center">
                        <p style="white-space: nowrap;">{{ __t('Сума:') }} <span>@money($cartTotal) {{ setting('currency') }}</span></p>
                        <a href="{{ route('checkout') }}" class="main-btn blue-small ml-24">{{ __t('Оформити') }}</a>
                    </div>
                    @endif
                </div>
            @endforeach
    </div>
</div>
@endif
</div>