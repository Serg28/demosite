Rekvizyty oplaty zamovlennja {{$order->id}}.
@if($order->legal_entities_recipient_id)
{{ $order->recipient->sms_detail }}
@endif
Komentar: zamovlennya {{$order->id}}
Suma: @if(!$order->is_delivery_paid_separately){{$order->cost + $order->price_delivery}}@else{{$order->cost}}@endif UAH
