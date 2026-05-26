@props(['label' => '', 'required' => false, 'placeholder' => 'example@mail.com'])

@php
    $wireModel = $attributes->get('wire:model', '');
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
           @input.debounce.500ms="validate()"
           @blur="onBlur()"
           class="field @error($wireModel) border-red-400 @enderror"
           :class="clientError ? 'border-red-400' : ''"
           placeholder="{{ $placeholder }}"
           autocomplete="email">
    <p x-show="clientError" x-text="clientError" class="text-red-500 text-xs mt-1"></p>
    @error($wireModel)
        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>
