{{--<label for="payment-{{$payment['id']}}" class="d-flex fd-c">
    <span class="d-flex">
        <input type="radio" id="payment-{{$payment['id']}}" name="pay_method_id"
               data-name="{!! $payment['name'] !!}"
               class="radio" value="{{$payment['id']}}"
        {{ (@$session_payment['pay_method_id'] && collect($payments)->contains('id', $session_payment['pay_method_id'])) ? ((@$session_payment['pay_method_id'] == $payment['id']) ? 'checked' : '') : (($loop->iteration == 1) ? 'checked' : '') }}

        >
        @if(@$payment['picture'])
            <img src="{{$payment['picture']}}" width="24" alt="{!! strip_tags($payment['name']) !!}">
        @endif
        <p>{!! $payment['name'] !!}</p>
    </span>

    <span class="payment-message" data-id="{{$payment['id']}}">

    </span>
</label> --}}

<label wire:key="payment-input-{{$payment['id']}}" for="payment-input-{{$payment['id']}}">
    <input type="radio" id="payment-input-{{$payment['id']}}" name="pay_method_id" class="radio" wire:click="selectPayment({{$payment['id']}})" >
    <span>{!! $payment['name'] !!}</span>
    @if(@$payment['picture'])<img src="{{$payment['picture']}}"  height="24" alt="{!! strip_tags($payment['name']) !!}">@endif

    {{--
    @if($payment['message'])
        {{$payment['message']}}
    @endif
    @if($payment['short_description'])
        {!!  $payment['short_description']!!}
    @endif --}}

</label>
