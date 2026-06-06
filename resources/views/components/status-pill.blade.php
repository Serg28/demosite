@props(['status', 'text' => null])
@php
    $label = $text ?? ($status?->t('title') ?? '—');
    $color = $status?->color ?? '#6B7280';
    $bg    = $color . '20';
@endphp
<span class="status-pill" style="background-color: {{ $bg }}; color: {{ $color }};">
    {{ $label }}
</span>
