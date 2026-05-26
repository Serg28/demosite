@props(['label' => '', 'required' => false])

@php
    $wireModel = $attributes->get('wire:model', '');
@endphp

<div x-data="phoneInput('{{ $wireModel }}')">
    @if($label)
        <label class="text-sm font-medium mb-1.5 block">
            {!! $label !!}
        </label>
    @endif
    <input type="tel"
           x-ref="input"
           @focus="onFocus()"
           @input="onInput($event)"
           @blur="onBlur()"
           class="field @error($wireModel) border-red-400 @enderror"
           placeholder="+38 (0__) ___-__-__"
           autocomplete="tel">
    @error($wireModel)
        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>
