{{-- resources/views/components/filter-group-checkbox.blade.php --}}
<div class="cat-cell filters_category {{ $isOpened ? 'active' : '' }}" wire:key="char-{{$characteristic->id}}" x-data="{ expanded: false }">
    <div class="visible-row flex v--center h--between">
        <p class="fw-600 fsz-16">{!! $characteristic->characteristicTitle() !!}</p>
        <div class="icon">
            <img src="/assets/images/cat-arrow.svg" alt="">
        </div>
    </div>
    <div class="hidden-row" :class="expanded ? 'full' : ''" x-transition>
        @loop ($options as $option)
            @php
                $countOptions = $results['options'][$option->id] ?? 0;
                $isChecked = $filter->isChecked($option);
            @endphp

            <label for="{{ $option->localizedSlug ?? 'char-'.$option->id }}" class="flex v--center checkbox {{ !$countOptions ? 'disabled' : '' }} {{ $isChecked ? 'active' : '' }}">
                <input type="checkbox" id="{{ $option->localizedSlug ?? 'char-'.$option->id }}"
                       {{ $isChecked ? 'checked' : '' }}
                       {{ !$countOptions ? 'disabled' : '' }}
                       @if($countOptions) data-url="{{ $filter->urlFilter($option) }}" @endif
                       value="{{ $option->id }}">
                <p class="flex v--start color--gray">{{ $option->t('title') }}<span class="fsz-12">({{ $countOptions }})</span></p>
            </label>
        @endloop
    </div>
    @if(count($options)>4)
    <button class="show-all-label flex v--center" @click="expanded = ! expanded" :class="expanded ? 'active' : ''">
        <span class="visible">{{ __('Показати ще') }}</span>
        <span class="hidden">{{ __('Приховати') }}</span>
        <span class="icon"><img src="/assets/images/cat-arrow-down-blue.svg" alt=""></span>
    </button>
    @endif
</div>