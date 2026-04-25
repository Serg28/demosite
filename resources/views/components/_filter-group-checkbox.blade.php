{{-- resources/views/components/filter-group-checkbox.blade.php --}}
<div class="block_accordion filters_category {{ $isOpened ? 'active' : '' }}" wire:key="char-{{$characteristic->id}}">
    <div class="title_accordion {{ $isOpened ? 'open' : '' }}">
        <h3>{!! $characteristic->characteristicTitle() !!}</h3>
    </div>
    <div class="main_accordion" x-data="searchItems('{{ $characteristic->id }}')" x-init="init" data-id="{{ $characteristic->id }}">
        <div class="flex-row">
            <input type="text" placeholder="{{ __('Введіть назву') }}" class="header__search-input" x-model="search" @input="updateVisibility">
            <button class="search-button-icon"></button>
        </div>
        <div class="checkbox_bl hid_" x-ref="itemsContainer" style="overflow:hidden;">
            @foreach ($options as $option)
                @php
                    $countOptions = $results['options'][$option->id] ?? 0;
                    $isChecked = $filter->isChecked($option);
                @endphp

                <label for="{{ $option->localizedSlug }}" class="checkbox {{ !$countOptions ? 'disabled' : '' }} {{ $isChecked ? 'active' : '' }}" data-item>
                    <input type="checkbox" id="{{ $option->localizedSlug }}"
                           {{ $isChecked ? 'checked' : '' }}
                           {{ !$countOptions ? 'disabled' : '' }}
                           @if($countOptions) data-url="{{ $filter->urlFilter($option) }}" @endif
                           value="{{ $option->id }}">
                    <span class="checkbox_text">
                        <span class="ch_flex">{{ $option->t('title') }} <span class="quantity">({{ $countOptions }})</span></span>
                    </span>
                </label>
            @endforeach
        </div>
        <p x-cloak x-show="!items.filter(item => show(item)).length" wire:transition style="display: none">{{ __('Нічого не знайдено.') }}</p>
        <div x-cloak class="show-more-wrap start" x-show="hiddenCount > 0" style="display: none">
            <button class="show-more" @click="toggleShowMore()">
                <span x-show="!showMoreClicked">{{ __('Показати ще') }} {{--<span x-text="hiddenCount"></span>--}}</span>
                <span x-show="showMoreClicked">{{ __('Приховати') }}</span>
                <img src="/img/arrow-down-orange.svg" alt="">
            </button>
        </div>
    </div>
</div>