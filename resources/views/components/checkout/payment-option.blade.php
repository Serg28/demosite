@props(['payMethod', 'selected' => false])

<label class="flex items-start gap-3 p-4 border-2 rounded-xl cursor-pointer transition-colors
    {{ $selected ? 'border-brand bg-brand-light' : 'border-gray-100 hover:border-gray-200' }}">
    <input type="radio"
           wire:model.live="payMethodId"
           value="{{ $payMethod->id }}"
           class="mt-0.5 accent-brand flex-shrink-0">
    <div>
        <p class="text-sm font-semibold">{{ $payMethod->t('title') }}</p>
        @if($payMethod->t('description'))
            <p class="text-xs text-ink-muted">{{ $payMethod->t('description') }}</p>
        @elseif($payMethod->commission_percent > 0)
            <p class="text-xs text-ink-muted">
                {{ __t('Комісія :percent%', ['percent' => $payMethod->commission_percent]) }}
            </p>
        @endif
    </div>
</label>
