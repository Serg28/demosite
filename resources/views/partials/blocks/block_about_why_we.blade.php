<div class="benefits mt-40">
    <h3 class="fsz-24 fw-600 heading">{!! $block->h2->t('title') ?? $page->t('title') !!}</h3>
    <div class="column-wrapper">
        @if($block->advantages)
            @foreach($block->advantages as $advantages)
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