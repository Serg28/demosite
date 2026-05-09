<div>
    @if(!empty($this->installments))
    <div class="mt-4 p-4 bg-blue-50 border border-blue-100 rounded-xl">
        <p class="text-sm font-semibold text-blue-800 mb-3">{{ __t('Розрахунок розстрочки') }}</p>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-xs text-ink-muted border-b">
                        <th class="text-left pb-2 font-medium">{{ __t('Термін') }}</th>
                        <th class="text-right pb-2 font-medium">{{ __t('Щомісяця') }}</th>
                        <th class="text-right pb-2 font-medium">{{ __t('Разом') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-blue-100">
                    @foreach($this->installments as $installment)
                        <tr>
                            <td class="py-1.5">{{ $installment['months'] }} {{ __t('міс.') }}</td>
                            <td class="py-1.5 text-right font-medium">@money($installment['monthly'])</td>
                            <td class="py-1.5 text-right text-ink-muted">@money($installment['total'])</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
