<div class="reviews-screen {{$class ?? ''}}">
    @if($comments)
        <div class="container">
            <h2 class="heading fsz-28 fw-600">{{__t('Відгуки про магазин')}}</h2>
            <div class="columns flex v--center h--between">
                <div class="column">
                    <div class="column-heading flex v--center">
                        <img loading="lazy" src="/assets/images/blue-star.svg" alt="">
                        <p class="fsz-38 fw-600">{{ number_format($averageRating, 1) }}/<span class="fsz-24">5</span></p>
                    </div>
                    <p>{!! __t('у нас відмінний індекс <br> надійності') !!}</p>
                </div>
                <div class="column">
                    <div class="column-heading flex v--center">
                        <p class="fsz-38 fw-600">100%</p>
                    </div>
                    <p>{{__t('відгуків справжні та проходять лише перевірку цензурою')}}</p>
                </div>
                <div class="column">
                    <div class="column-heading flex v--center">
                        <p class="fsz-38 fw-600">92%</p>
                    </div>
                    <p>{{__t('клієнтів повертаються, щоб зробити повторні покупки')}} </p>
                </div>
                <div class="column">
                    <div class="column-heading flex v--center">
                        <p class="fsz-38 fw-600">+ 4 500</p>
                    </div>
                    <p>{{__t('відгуків та оцінок залишено нашими клієнтами')}}</p>
                </div>
            </div>
            <div class="swiper-riviews-wapper custom-swiper-wrapper">
                <div class="reviews-swiper swiper">
                    <div class="swiper-wrapper">
                        @loop($comments as $comment)
                        @php
                            $username = Str::ucfirst($comment->user?->first_name ?: ($comment->name ?: __t('Анонімний відвідувач')));
                        @endphp
                        <div class="swiper-slide">
                            <div class="review">
                                <div class="top-row flex v--center h--between">
                                    <div class="left a">a</div>
                                    <div class="right">
                                        <p class="name fsz-16 fw-600">{{$username}}</p>
                                        <div class="flex-row flex v--center h--between">
                                            <span class="fsz-14 color--gray date">{{$comment->humanDate() }}</span>
                                            <rating-stars size="big">{{$comment->rating}}</rating-stars>
                                        </div>
                                    </div>
                                </div>
                                <p class="description">{!! Str::limit($comment->body, 300) !!}</p>
                            </div>
                        </div>
                        @endloop
                    </div>
                    <div class="swiper-pagination custom-pagiation"></div>
                </div>
                <div class="custom-swiper-btn custom-swiper-btn-prev reviews-product-swiper-btn-prev">
                    <img src="/assets/images/arrow-blue-left.svg" alt="">
                </div>
                <div class="custom-swiper-btn custom-swiper-btn-next reviews-product-swiper-btn-next">
                    <img src="/assets/images/arrow-blue-right-1.svg" alt="">
                </div>
            </div>
            <div class="button-row text--center mt-24">
                <a href="{{geturl('/otzyvy-o-magazine')}}" class="main-btn blue-small">{{__t('Всі відгуки')}}</a>
            </div>
        </div>
    @endif
</div>
