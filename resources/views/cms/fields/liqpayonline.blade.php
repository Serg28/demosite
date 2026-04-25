<section class="liqpayonline-block">
        <div class="row" @if(!in_array($order->pay_method_id, [2,8]) || $order->is_paid())style="display: none"@endif>
            <div class="col text-center">
                <label class="label" for="platezi">{{__cms("Оплата LiqPayOnline")}}</label>
                <button class="hold_completion btn btn-sm btn-success" type="button"
                        @if($order->is_online_payed==2 && @$order->liqpay_info['status'] == 'hold_wait')@else style="display: none"@endif>
                    <i class="glyphicon glyphicon-ok"></i> {{__cms("Подтвердить списание LiqPay")}}
                </button>
                <button class="butrefund btn btn-sm btn-danger" type="button"
                        @if($order->is_online_payed==2 && @$order->liqpay_info['status'] == 'hold_wait')@else style="display: none"@endif>
                    <i class="glyphicon glyphicon-remove"></i> {{__cms("Возврат платежа")}}
                </button>
                <button class="newpaylink btn btn-sm btn-warning" type="button"
                        @if($order->is_online_payed==2 && @$order->liqpay_info['status'] == 'hold_wait') style="display: none"@endif>
                    <i class="glyphicon glyphicon-send"></i> {{__cms("Выставить новый счет на оплату")}}
                </button>
                <a class="openpaylink btn btn-sm btn-info"
                   href="{{route('payment.init',['order'=>$order])}}" role="button"
                   @if($order->is_online_payed==2 && @$order->liqpay_info['status'] == 'hold_wait') style="display: none"
                   @endif
                   target="_blank">
                    <i class="glyphicon glyphicon-link"></i> {{__cms("Открыть сcылку на оплату")}}
                </a>
            </div>
        </div>

        @if(in_array($order->pay_method_id, [2,8]) && $order->is_paid())
            <div class="alert alert-info">{{__t('Замовлення оплачене, тому керування оплатою недоступне')}}</div>
        @endif
</section>

<script>
    var _this = $(this);
    $('.hold_completion').click(function (ev) {

        var el = $(this);
        ev.preventDefault();
        ev.stopPropagation();
        var cost = $('[name="cost"]').val();


        if (!cost) {
            jQuery.SmartMessageBox({
                title: "{{__cms("Ошибка")}}",
                content: "{{__cms("Сумма заказа равна нулю. Возможно, состав заказа был изменен. Сначала подкорректируйте его, осуществите возврат средств и создайте новую ссылку на оплату")}}",
                buttons: '[{{__cms("Отмена")}}]'
            }, function (ButtonPressed) {

            });

        } else {

            jQuery.SmartMessageBox({
                title: "{{__cms("Подтверждение действия")}}",
                content: "{{__cms("Вы действительно желаете подтвердить списание блокированных средств в счет оплаты заказа?")}}",
                buttons: '[{{__cms("Отмена")}}][{{__cms("Да, подтвердить списание")}}]'
            }, function (ButtonPressed) {
                if (ButtonPressed === '{{__cms("Да, подтвердить списание")}}') {

                    $.ajax({
                        url: '{{route('liqpayonline.holdcompletion',['order' => $order])}}',
                        type: 'post',
                        success: function (data, status, xhr) {
                            $(".hold_completion").hide();
                            $(".butrefund").hide();
                            if (data.success) {
                                $("[name=is_online_payed]").val(1)
                            }
                            jQuery.SmartMessageBox({
                                title: data.title,
                                content: data.message,
                                buttons: '[{{__cms("Закрыть")}}]'
                            }, function (ButtonPressed) {
                                if (ButtonPressed === '{{__cms("Закрыть")}}') {
                                    if (typeof reloadOrder == 'function') {
                                        reloadOrder({{$order->id}}, _this, el);
                                    }
                                }
                            });
                        }
                    });
                }
            });
        }
    });

    $('.butrefund').click(function (ev) {

        var el = $(this);
        ev.preventDefault();
        ev.stopPropagation();

        jQuery.SmartMessageBox({
            title: "{{__cms("Подтверждение действия")}}",
            content: "{{__cms("Вы действительно желаете сделать возврат заблокированных средств?")}}",
            buttons: '[{{__cms("Отмена")}}][{{__cms("Да, подтвердить возврат")}}]'
        }, function (ButtonPressed) {
            if (ButtonPressed === '{{__cms("Да, подтвердить возврат")}}') {
                $.ajax({
                    url: '{{route('liqpayonline.refund',['order' => $order])}}',
                    type: 'post',
                    success: function (data, status, xhr) {
                        $(".hold_completion").hide();
                        $(".butrefund").hide();
                        if (data.success) {
                            $("[name=is_online_payed]").val(0)
                        }
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

    // Выставить новый счет
    $('.newpaylink').click(function (ev) {

        var el = $(this);
        ev.preventDefault();
        ev.stopPropagation();

        jQuery.SmartMessageBox({
            title: "{{__cms("Подтверждение действия")}}",
            content: "{{__cms("Вы действительно желаете отправить пользователю новую ссылку на оплату?")}}",
            buttons: '[{{__cms("Отмена")}}][{{__cms("Да, отправить")}}]'
        }, function (ButtonPressed) {
            if (ButtonPressed === '{{__cms("Да, отправить")}}') {
                $.ajax({
                    url: '{{route('liqpayonline.email.newlink',['order' => $order])}}',
                    type: 'post',
                    success: function (data, status, xhr) {
                        $(".hold_completion").hide();
                        $(".butrefund").hide();
                        jQuery.SmartMessageBox({
                            title: data.title,
                            content: data.message,
                            buttons: '[{{__cms("Закрыть")}}]'
                        }, function (ButtonPressed) {
                            if (ButtonPressed === '{{__cms("Закрыть")}}') {
                                /*if (typeof reloadOrder == 'function') {
                                    reloadOrder({{$order->id}}, _this, el);
                                }*/
                                if (typeof reloadPage == 'function') {
                                    reloadPage({{$order->id}});
                                }
                            }
                        });
                    }
                });
            }
        });
    });
    {{--}
    @if(!request()->get('o'))
        function reloadOrder(orderid, target, el) {
            return;
            //window.location.href = window.location.href;
            TableBuilder.hidePreloader();
            TableBuilder.getEditForm(orderid, target, el);
            setTimeout(function () {
                var id = $(el).parents('.tab-pane').attr('id');
                if (id) {
                    $('[href="#' + id + '"]').click();
                }
            }, 2000);
        }
    @endif
    --}}
</script>
