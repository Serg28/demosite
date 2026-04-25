
<div class="benefits-screen">
    <div class="container">
        <div class="benefits-screen__wrap flex v--center h--between">
            <div class="left">
                <h2 class="fsz-28 fw-600 heading lh-140">{!! $whyWe->h2->t('title') ?? $page->t('title') !!}</h2>
                {!! $whyWe->description->t('description') !!}
                <a href="{{$whyWe->contactsWithMap->t('adress')}}" class="main-btn blue-big">{{$whyWe->contactsWithMap->t('title')}}</a>
            </div>
            <div class="right flex v--start h--between">
                @if($whyWe->advantages)
                    @foreach($whyWe->advantages as $advantages)
                        <div class="column flex v--start">
                            <div class="icon">
                                <img src="{{$advantages->getImgPath(42,42)}}" alt="">
                            </div>
                            <p>@if($advantages->title){{$advantages->t('title')}} @endif</p>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>
