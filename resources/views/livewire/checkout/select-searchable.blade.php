<div>
    <div x-data="{ defaultValue: '{{$defaultValue}}', show: false, value: @entangle('value'), items: @entangle('options'), showNoResults: @entangle('showNoResults'), showStart: @entangle('showStart') }" class="select-searchable">
        {{-- Скрытое поле для хранения значения модели --}}
        <input type="hidden" x-model="value" wire:model.live="value" >
        {{-- / --}}
        <div @click="show = true" @keydown.enter="show = !show" class="search-input">
            {{-- Поле для ввода поискового запроса и последующего отображения текстового значения выбранной опции--}}
            <input
                    type="text"
                    placeholder="{{$placeholder}}"
                    wire:keydown.enter.prevent=""
                    wire:model.live.debounce.300ms="text"
                    x-on:click="show = true"
                    x-on:keydown="show = true"
                    wire:click.away="restoreLastValue"
                    wire:loading.attr="disabled"
                    wire:target="value"
                    class="input @error($model) error @enderror ">
            {{-- / --}}
        </div>

        {{-- Отображение результатов поиска --}}
        <div x-show="show" x-cloak @click.away="show = false">
            <div class="dropdown" x-show="items">
                <template x-for="item in items">
                    <div wire:click="select(item.value, item.escaped_text)"
                         @click="show = false, showNoResults = false"
                         x-text="item.text"
                         class="option">
                    </div>
                </template>

                <template x-if="showNoResults">
                    <div class="message">
                        {{__t('Нічого не знайдено')}}
                    </div>
                </template>

                <template x-if="showStart">
                    <div class="message">
                        {{__t('Почніть вводити текст')}}
                    </div>
                </template>
            </div>
        </div>
        {{-- / --}}
    </div>

</div>

