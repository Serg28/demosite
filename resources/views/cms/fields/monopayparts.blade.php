@if($order->pay_method_id==7)
<section class="monopayparts-block">
        {{--<div class="row">
            <div class="col text-center">
                @if(@$status)
                    <div class="alert alert-info">{{$status}}
                        <span style="display:block;font-size:8px">{{$order->mono_payparts_info['state']}} / {{$order->mono_payparts_info['order_sub_state']}}</span>
                    </div>
                @endif
                @if($order->mono_store_order_id && @$order->mono_payparts_info['state']=='SUCCESS')
                    <button id="mono_return" class=" btn btn-sm btn-warning" type="button"
                        @if(@$order->mono_payparts_info['order_sub_state']!=='RETURNED')@else style="display: none"@endif>
                        <i class="glyphicon glyphicon-remove"></i> {{__cms("Возврат товара")}}
                    </button>
                @endif

                @if(@$order->mono_payparts_info['state']!=='SUCCESS')
                    <button id="mono_reject" class=" btn btn-sm btn-danger" type="button"
                         @if(@$order->mono_payparts_info['order_sub_state'] == 'WAITING_FOR_STORE_CONFIRM')@else style="display: none"@endif>
                         <i class="glyphicon glyphicon-remove"></i> {{__cms("Скасування заявки (Товар клієнтові не видано)")}}
                    </button>
                    <button id="mono_confirm" class="mono_confirm btn btn-sm btn-success" type="button"
                        @if(@$order->mono_payparts_info['order_sub_state'] == 'WAITING_FOR_STORE_CONFIRM')@else style="display: none"@endif>
                        <i class="glyphicon glyphicon-ok"></i> {{__cms("Товар выдан клиенту")}}
                    </button>
                @endif
            </div>
        </div> --}}

    <div class="row">
        <div class="col text-center">
            @if(@$status)
                <div class="alert alert-info">{{$status}}
                    <span style="display:block;font-size:8px">{{$state}} / {{$subState}}</span>
                </div>
            @endif
            @if($order->mono_store_order_id && @$state=='SUCCESS')
                <button id="mono_return" class=" btn btn-sm btn-warning" type="button"
                        @if(@$subState!=='RETURNED')@else style="display: none"@endif>
                    <i class="glyphicon glyphicon-remove"></i> {{__cms("Возврат товара")}}
                </button>
            @endif

            @if(@$state!=='SUCCESS')
                <button id="mono_reject" class=" btn btn-sm btn-danger" type="button"
                        @if(@$subState == 'WAITING_FOR_STORE_CONFIRM')@else style="display: none"@endif>
                    <i class="glyphicon glyphicon-remove"></i> {{__cms("Скасування заявки (Товар клієнтові не видано)")}}
                </button>
                <button id="mono_confirm" class="mono_confirm btn btn-sm btn-success" type="button"
                        @if(@$subState == 'WAITING_FOR_STORE_CONFIRM')@else style="display: none"@endif>
                    <i class="glyphicon glyphicon-ok"></i> {{__cms("Товар выдан клиенту")}}
                </button>
            @endif
        </div>
    </div>
</section>

<script>
    $('#mono_reject').click(function () {
        var el = $(this);

        jQuery.SmartMessageBox({
            title: "{{__cms("Подтверждение действия")}}",
            content: "{{__cms("Вы действительно желаете отменить заявку?")}}",
            buttons: '[{{__cms("Отмена")}}][{{__cms("Да, подтверждаю")}}]'
        }, function (ButtonPressed) {
            if (ButtonPressed === '{{__cms("Да, подтверждаю")}}') {

                $.ajax({
                    url: '{{route('monopayparts.reject',['order' => $order])}}',
                    type: 'post',
                    success: function (data, status, xhr) {
                        $("#mono_reject").hide();
                        $("#mono_confirm").hide();
                        $("#mono_return").hide();
                        /*if (data.success) {
                            $("[name=is_online_payed]").val(1)
                        }*/
                        jQuery.SmartMessageBox({
                            title: data.title,
                            content: data.message,
                            buttons: '[{{__cms("Закрыть")}}]'
                        }, function (ButtonPressed) {
                            if (ButtonPressed === '{{__cms("Закрыть")}}') {
                                reloadOrder({{$order->id}}, _this, el);
                            }
                        });
                    }
                });
            }
        });
    });

    $('#mono_return').click(function () {
        var el = $(this);

        jQuery.SmartMessageBox({
            title: "{{__cms("Подтверждение действия")}}",
            content: "{{__cms("Вы действительно желаете осуществить возврат товара?")}}",
            buttons: '[{{__cms("Отмена")}}][{{__cms("Да, подтверждаю")}}]'
        }, function (ButtonPressed) {
            if (ButtonPressed === '{{__cms("Да, подтверждаю")}}') {

                $.ajax({
                    url: '{{route('monopayparts.return',['order' => $order])}}',
                    type: 'post',
                    success: function (data, status, xhr) {
                        $("#mono_reject").hide();
                        $("#mono_confirm").hide();
                        /*if (data.success) {
                            $("[name=is_online_payed]").val(1)
                        }*/
                        jQuery.SmartMessageBox({
                            title: data.title,
                            content: data.message,
                            buttons: '[{{__cms("Закрыть")}}]'
                        }, function (ButtonPressed) {
                            if (ButtonPressed === '{{__cms("Закрыть")}}') {
                                reloadOrder({{$order->id}}, _this, el);
                            }
                        });
                    }
                });
            }
        });
    });

    $('#mono_confirm').click(function () {
        var el = $(this);

        jQuery.SmartMessageBox({
            title: "{{__cms("Подтверждение действия")}}",
            content: "{{__cms("Вы действительно желаете подтвердить выдачу товара?")}}",
            buttons: '[{{__cms("Отмена")}}][{{__cms("Да, подтверждаю")}}]'
        }, function (ButtonPressed) {
            if (ButtonPressed === '{{__cms("Да, подтверждаю")}}') {

                $.ajax({
                    url: '{{route('monopayparts.confirmation',['order' => $order])}}',
                    type: 'post',
                    success: function (data, status, xhr) {
                        $("#mono_reject").hide();
                        $("#mono_return").hide();
                        $("#mono_confirm").hide();
                        jQuery.SmartMessageBox({
                            title: data.title,
                            content: data.message,
                            buttons: '[{{__cms("Закрыть")}}]'
                        }, function (ButtonPressed) {
                            if (ButtonPressed === '{{__cms("Закрыть")}}') {
                                reloadOrder({{$order->id}}, _this, el);
                            }
                        });
                    }
                });
            }
        });
    });


</script>
@endif
