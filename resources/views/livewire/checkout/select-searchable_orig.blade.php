<div>
    <div x-data="{ value: '{{$defaultValue}}', show: @entangle('show')}" class="input-row">
        <div class="left">
            {{-- Скрытое поле для хранения значения модели --}}
            <input type="text" _x-model="{{$model}}" wire:model.live="value" >
            {{-- / --}}

            <div @click="show = true" @keydown.enter="show = !show" tabindex="3" style="position:relative">
                {{-- Поле для ввода поискового запроса и последующего отображения текстового значения выбранной опции--}}
                <input
                        type="text"
                        placeholder="{{$placeholder}}"
                        wire:keydown.enter.prevent=""
                        wire:model.live.debounce.100ms="text"
                        x-on:click="show = true"
                        x-on:keydown="show = true"
                        wire:blur="restoreLastValue"
                        @error($model) class="error" @enderror>
                {{-- / --}}
            </div>

            {{-- Отображение результатов поиска --}}
            <div style="position:absolute; z-index:100">
                <div x-show="show" x-cloak @click.away="show = false">
                        @if(count($options)>0)
                            @foreach($options as $option)
                                <div wire:click="select({{ $option['value'] }}, '{{ $option['escaped_text'] }}'); show=false">{{ $option['text'] }}</div>
                            @endforeach
                        @else
                        {{$text}}
                            <div>@if($text) Ничего не найдено @else Введите название @endif</div>
                        @endif
                </div>
            </div>
            {{-- / --}}
        </div>
    </div>
</div>