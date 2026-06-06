@props(['label' => '', 'required' => false, 'placeholder' => 'example@mail.com', 'error' => null])

@php
    $wireModel  = $attributes->get('wire:model', '');
    $fieldError = $error ?? ($wireModel ? $errors->first($wireModel) : '');
@endphp

<div x-data="emailInput('{{ $wireModel }}', '{{ __t('Невірний формат email') }}')">
    @if($label)
        <label class="text-sm font-medium mb-1.5 block">
            {!! $label !!}
        </label>
    @endif
    <input type="text"
           inputmode="email"
           x-model="value"
           @input="onInput($event)"
           @blur="onBlur()"
           class="field {{ $fieldError ? 'border-red-400' : '' }}"
           :class="clientError ? 'border-red-400' : ''"
           placeholder="{{ $placeholder }}"
           autocomplete="email">
    <p x-show="clientError" x-text="clientError" class="text-red-500 text-xs mt-1"></p>
    @if($fieldError)
        <p class="text-red-500 text-xs mt-1">{{ $fieldError }}</p>
    @endif
</div>
