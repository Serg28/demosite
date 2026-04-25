<div class="left-columns mt-24  ">
    @if (!empty($baseCharacteristics))
        <div class="columns-wrap flex h--wrap  v--center">

            @loop ($baseCharacteristics as $characteristic)
            <div class="column">
                <p class="fsz-20 fw-600">{!! $characteristic["values"] !!}</p>
                <span class="fsz-13 color--gray">{!! $characteristic["title"] !!}</span>
            </div>
            @endloop

        </div>
    @endif
    <div class="btn-row mt-24 flex h--center">
        <a href="#characteristics"
           class="fsz-16 color--blue scroll-to">{{ !empty($baseCharacteristics) ? __t('Показати всі') : __t('Всі характеристики') }}</a>
    </div>

</div>
