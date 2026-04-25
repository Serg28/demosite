@if($payments)
    <div class="radio-row flex">
        @foreach($payments as $key => $payment)
                @if(View::exists('livewire.checkout.forms.payment_'.$payment['id']))
                    @include('livewire.checkout.forms.payment_'.$payment['id'])
                @else
                    @include('livewire.checkout.forms.payment_default')
                @endif
            @if(($key + 1) % 2 === 0)
    </div>
    <div class="radio-row flex">
        @endif
        @endforeach
    </div>
@endif
