<div>
    {{-- Hero cards --}}
    <div class="grid sm:grid-cols-2 gap-4 mb-4">
        <div class="card p-6 bg-gradient-to-br from-brand to-brand-dark text-white">
            <p class="text-xs uppercase tracking-wide opacity-80 mb-2 font-semibold">{{ __t('Накопичена знижка') }}</p>
            <p class="text-5xl font-black mb-1">{{ $this->bestDiscount > 0 ? $this->bestDiscount . '%' : '—' }}</p>
            <p class="text-sm opacity-80">{{ __t('на всі товари') }}</p>
            @if($this->cumulativeDiscount > 0)
                <p class="text-xs opacity-70 mt-1">{{ __t('з накопичувальної програми') }}</p>
            @endif
        </div>
        <div class="card p-6 bg-gradient-to-br from-amber-400 to-amber-600 text-white">
            <p class="text-xs uppercase tracking-wide opacity-80 mb-2 font-semibold">{{ __t('Бонусні бали') }}</p>
            <p class="text-5xl font-black mb-1">0</p>
            <p class="text-sm opacity-80">{{ __t('1 бал = 1 ₴') }}</p>
        </div>
    </div>

    {{-- Cumulative levels --}}
    @if($this->cumulativeLevels->isNotEmpty())
        <div class="card p-5 mb-4">
            <h3 class="font-bold mb-1">{{ __t('Накопичувальна програма') }}</h3>
            <p class="text-sm text-ink-muted mb-4">
                {{ __t('Ваша сума виконаних замовлень') }}:
                <strong>@money($this->completedOrdersSum) {{ setting('currency') }}</strong>
            </p>
            <div class="space-y-2">
                @foreach($this->cumulativeLevels as $level)
                    @php $active = $this->cumulativeDiscount >= $level->discount && $this->completedOrdersSum >= $level->total_sum; @endphp
                    <div class="flex items-center justify-between p-3 rounded-xl border {{ $active ? 'border-brand bg-brand/5' : 'border-gray-100 bg-gray-50' }}">
                        <div class="text-sm">
                            {{ __t('від') }} <strong>@money($level->total_sum) {{ setting('currency') }}</strong>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-brand text-lg">{{ $level->discount }}%</span>
                            @if($active)
                                <span class="badge bdg-new text-[10px]">{{ __t('Активно') }}</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Discount cards --}}
    @if($this->discountCards->isEmpty())
        <div class="card p-10 text-center text-ink-muted">
            <div class="text-4xl mb-3">🎁</div>
            <p>{{ __t('Дисконтних карток поки немає') }}</p>
        </div>
    @else
        <div class="card p-5">
            <h3 class="font-bold mb-4">{{ __t('Ваші дисконтні картки') }}</h3>
            <div class="space-y-3">
                @foreach($this->discountCards as $card)
                    <div class="flex items-center justify-between p-4 rounded-xl border border-gray-100 bg-gray-50" wire:key="dc-{{ $card->id }}">
                        <div>
                            <p class="font-bold text-sm">{{ $card->code }}</p>
                            @if($card->name)
                                <p class="text-xs text-ink-muted">{{ $card->name }}</p>
                            @endif
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-brand text-lg">
                                @if($card->type === 'percent')
                                    {{ $card->value }}%
                                @else
                                    @money($card->value) {{ setting('currency') }}
                                @endif
                            </p>
                            <p class="text-xs text-ink-muted">
                                {{ $card->type === 'percent' ? __t('знижка') : __t('фіксована знижка') }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
