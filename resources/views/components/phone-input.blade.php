@props(['label' => '', 'required' => false, 'error' => null])

@php
    $wireModel  = $attributes->get('wire:model', '');
    $fieldError = $error ?? ($wireModel ? $errors->first($wireModel) : '');
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
           class="field {{ $fieldError ? 'border-red-400' : '' }}"
           placeholder="+38 (0__) ___-__-__"
           autocomplete="tel">
    @if($fieldError)
        <p class="text-red-500 text-xs mt-1">{{ $fieldError }}</p>
    @endif
</div>
