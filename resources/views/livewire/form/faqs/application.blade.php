<div class="help__wrap p-24 mt-24">
    <form wire:submit="getSearch" class="flex relative search-form" autocomplete="off">
        <label class="input">
            <input wire:model.lazy="keyword" type="text" name="search" id="search" placeholder=" ">
            <span>{{__t('Знайдіть питання, яке вас цікавить')}}</span>
        </label>
        <div type="button" class="search-button flex v--center h--center">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                <path d="M13.5507 13.5943C14.9318 12.2616 15.7897 10.398 15.7897 8.33569C15.7897 4.2843 12.4789 1 8.39487 1C4.3108 1 1 4.2843 1 8.33569C1 12.3871 4.3108 15.6714 8.39487 15.6714C10.4 15.6714 12.2187 14.8797 13.5507 13.5943ZM13.5507 13.5943L19 19" stroke="#AFB1C4" stroke-width="2"/>
            </svg>
        </div>
    </form>

    <h3 class="fsz-28 fw-600 mt-40 faq-heading">{{__t('Питання та відповіді')}}</h3>
    <div class="faq flex fd--column">
        @if ($searchResults->isNotEmpty())
            @foreach ($searchResults as $key => $searchResult)
                <div class="faq-row" wire:key="faq-row-{{$key}}">
                    <div class="top relative flex v--center h--between">
                        <p class="fw-600">{!! $searchResult->t('title') ?? '' !!}</p>
                        <div class="icon relative"></div>
                    </div>
                    <div class="bottom">
                        <p>{!! $searchResult->t('description') ?? '' !!}</p>
                    </div>
                </div>
            @endforeach
        @else
            <div class="faq-row active">
                <div class="top relative flex v--center h--between">
                    <p class="fw-600">{{__t('На ваш запит нічого не знайшлося.')}}</p>
                </div>
                <div class="bottom"></div>
            </div>
        @endif

        <div class="picture-box-background" wire:loading.class="load-show">
                <svg xmlns:svg="http://www.w3.org/2000/svg" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.0" width="56px" height="14px" viewBox="0 0 128 32" xml:space="preserve">
                <circle fill="#CF051D" cx="0" cy="0" r="11" transform="translate(16 16)"><animateTransform attributeName="transform" type="scale" additive="sum" values="1;1.42;1;1;1;1;1;1;1;1" dur="750ms" repeatCount="indefinite"></animateTransform></circle>
                    <circle fill="#CF051D" cx="0" cy="0" r="11" transform="translate(64 16)"><animateTransform attributeName="transform" type="scale" additive="sum" values="1;1;1;1;1.42;1;1;1;1;1" dur="750ms" repeatCount="indefinite"></animateTransform></circle>
                    <circle fill="#CF051D" cx="0" cy="0" r="11" transform="translate(112 16)"><animateTransform attributeName="transform" type="scale" additive="sum" values="1;1;1;1;1;1;1;1.42;1;1" dur="750ms" repeatCount="indefinite"></animateTransform></circle>
                </svg>
            </div>
    </div>
</div>
