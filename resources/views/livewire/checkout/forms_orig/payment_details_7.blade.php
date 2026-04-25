<select name="payparts_count">
    @foreach(range(3, $payment['partscount']) as $number)
        <option value="{{$number}}"
                @if ($number == $payment['cur_partscount'])selected="selected"@endif >{{$number}} {{trans_choice(__t('{0}платежей|[1]платеж|[2,4]платежа|[5,*]платежей'),$number)}}</option>
    @endforeach
</select>
