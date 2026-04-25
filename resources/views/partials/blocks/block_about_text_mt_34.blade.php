<div class="text mt-34">
    <h2>{!! $block->h2->t('title') ?? $page->t('title') !!}</h3>
    @if($block->description){!! $block->description->t('description') !!}@endif
</div>
