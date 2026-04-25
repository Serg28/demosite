<div class="del-section-wrapper flex fd--column">
    <div class="heading-row flex v--center">
        @if($block->picture)<div class="icon flex v--center h--center"><img src="{!! $block->picture->t('picture') ?: '/assets/images/trans.svg' !!}" alt=""></div>@endif
        <h3 class="fsz-28 fw-600">{!! $block->h2->t('title') ?? $page->t('title') !!}</h3>
    </div>
    @if($block->delivery_list_text)
        @foreach ($block->delivery_list_text as $item)
            <div class="del-section-row flex fd--column">
                <h3>{{$item->t('title')}}</h3>
                @if($item->description){!! $item->t('description') !!}@endif
            </div>
        @endforeach
    @endif
</div>