@if($payments)
    @foreach($payments as $key => $payment)
        @includeFirst(['livewire.checkout.forms.payment_'.$payment['id'], 'livewire.checkout.forms.payment_default'])
    @endforeach
@else
    {{__t('Для этого метода доставки нет методов оплаты')}}
@endif
