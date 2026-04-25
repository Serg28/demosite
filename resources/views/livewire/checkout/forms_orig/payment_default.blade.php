    <label wire:key="payment-input-{{$payment['id']}}" for="payment-input-{{$payment['id']}}">
        <input type="radio" id="payment-input-{{$payment['id']}}" name="pay_method_id" class="radio" wire:click="selectPayment({{$payment['id']}})"  >
        <span>{!! strip_tags($payment['name']) !!}</span>
        @if(@$payment['picture'])<img src="{{$payment['picture']}}"  height="24" alt="{!! strip_tags($payment['name']) !!}">@endif

        {{--
        @if($payment['message'])
            {{$payment['message']}}
        @endif
        @if($payment['short_description'])
            {!!  $payment['short_description']!!}
        @endif --}}
    </label>

