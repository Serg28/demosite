@props(['type' => 'info', 'title' => null, 'dismissible' => false])
@php
    $config = match($type) {
        'success' => ['border-green-200', 'bg-green-50', 'text-green-500', 'text-green-800', 'text-green-600', 'text-green-400 hover:text-green-600', '✓'],
        'error'   => ['border-red-200',   'bg-red-50',   'text-red-500',   'text-red-800',   'text-red-600',   'text-red-300 hover:text-red-500',      '✗'],
        'warning' => ['border-amber-200', 'bg-amber-50', 'text-amber-500', 'text-amber-800', 'text-amber-600', 'text-amber-400 hover:text-amber-600',  '⚠'],
        default   => ['border-blue-200',  'bg-blue-50',  'text-blue-500',  'text-blue-800',  'text-blue-600',  'text-blue-400 hover:text-blue-600',    'ℹ'],
    };
    [$borderCls, $bgCls, $iconCls, $titleCls, $textCls, $closeCls, $icon] = $config;
@endphp
<div {{ $attributes->merge(['class' => "rounded-xl border $borderCls $bgCls px-5 py-4 flex items-start gap-3"]) }}
     @if($dismissible) x-data="{ show: true }" x-show="show" @endif role="alert">
    <span class="{{ $iconCls }} text-xl flex-shrink-0">{{ $icon }}</span>
    <div class="flex-1 min-w-0">
        @if($title)
            <p class="font-semibold {{ $titleCls }}">{{ $title }}</p>
            <p class="text-sm {{ $textCls }} mt-0.5">{{ $slot }}</p>
        @else
            <p class="font-semibold {{ $titleCls }}">{{ $slot }}</p>
        @endif
    </div>
    @if($dismissible)
        <button @click="show = false" class="{{ $closeCls }} flex-shrink-0 ml-auto text-lg leading-none">✕</button>
    @endif
</div>
