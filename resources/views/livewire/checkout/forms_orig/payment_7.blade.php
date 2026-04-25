<label for="payment-{{$payment['id']}}" class="d-flex ai-c gap-30 @if($payment['hidden']) notactive @endif">
    <span class="d-flex ai-c @if($payment['hidden']) disabled @endif">
        @if(!$payment['hidden'])
        <input type="radio" id="payment-{{$payment['id']}}" name="pay_method_id"
               data-name="{!! $payment['name'] !!}"
               class="radio" value="{{$payment['id']}}"
               {{--@if ($loop->iteration == 1)checked="checked"@endif --}} {{-- или если есть в сессии --}}
            {{ (@$session_payment['pay_method_id'] && collect($payments)->contains('id', $session_payment['pay_method_id'])) ? ((@$session_payment['pay_method_id'] == $payment['id']) ? 'checked' : '') : (($loop->iteration == 1) ? 'checked' : '') }}

        >
        @endif
        <span class="d-flex ai-c gap-10">
            @if(@$payment['picture'])
               <img src="{{$payment['picture']}}"
                    @if($payment['hidden'])style="margin-left:5px" @endif
                    width="24"
                    alt="{!! strip_tags($payment['name']) !!}"
               >
            @endif
            <p>{!! $payment['name'] !!}</p>
        </span>
    </span>
    <span class="payment-message" data-id="{{$payment['id']}}">

        @if(@$payment['message'])
            {{$payment['message']}}
        @endif
        @if($payment['hidden'])
                <span class="error">{{__t('Метод оплаты недоступен, поскольку указанный номер телефона не найден в базе данных клиентов Монобанка.')}}</span>
        @endif
    </span>
</label>

