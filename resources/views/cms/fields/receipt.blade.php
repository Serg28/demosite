<section>
    <div class="row">
        <div class="col text-center">
            @if(!$order->getPrepaymentAmount())
                {{-- Обычные заказы без предоплаты --}}
                @if(!$receipt)
                    <button id_="send-to-checkbox" type="button" value="{{$id}}"
                            class="send-to-checkbox btn btn-success">{{ __t('Фискализация чека') }} Checkbox.ua
                    </button><br/>
                @else
                    <p>Чеки Checkbox.ua</p>
                    <a class="btn btn-sm btn-warning" href="{{$receipt->formPrintPath() .'/html'}}" role="button"
                       target="_blank">
                        <i class="glyphicon glyphicon-print"></i> {{ __t('HTML чек') }}
                    </a>
                    <a class="btn btn-sm btn-info" href="{{$receipt->formPrintPath() .'/pdf'}}" role="button"
                       target="_blank">
                        <i class="glyphicon glyphicon-print"></i> {{ __t('PDF чек') }}
                    </a>
                    <a class="btn btn-sm btn-primary" href="{{$receipt->formPrintPath() .'/text'}}" role="button"
                       target="_blank">
                        <i class="glyphicon glyphicon-print"></i> {{ __t('TXT чек') }}
                    </a>
                    <a class="btn btn-sm btn-success" href="{{$receipt->formPrintPath() .'/qrcode'}}" role="button">
                        <i class="glyphicon glyphicon-qrcode"></i> {{ __t('QR код чека') }}
                    </a>
                @endif
            @else
                {{-- Заказы с предоплатой --}}
                <div class="row text-left">
                    <fieldset>
                        @if(!$receipt_prepayment)
                            <button id_="send-to-checkbox" type="button" value="{{$id}}" data-type="prepayment"
                                    class="send-to-checkbox btn btn-success">{!!  str_replace(['[amount]','[currency]'],[$order->getPrepaymentAmount(), setting('currency')],__cms("Фискализация предоплаты [amount] [currency]") ) !!}</button>
                            <br/>
                        @else
                            <p>{!! str_replace(['[amount]','[currency]'],[$order->getPrepaymentAmount(), setting('currency')],__cms("Чеки Checkbox.ua предоплата [amount] [currency]")) !!}</p>
                            <a class="btn btn-sm btn-warning" href="{{$receipt_prepayment->formPrintPath() .'/html'}}"
                               role="button" target="_blank">
                                <i class="glyphicon glyphicon-print"></i> {{ __t('HTML чек') }}
                            </a>
                            <a class="btn btn-sm btn-info" href="{{$receipt_prepayment->formPrintPath() .'/pdf'}}"
                               role="button" target="_blank">
                                <i class="glyphicon glyphicon-print"></i> {{ __t('PDF чек') }}
                            </a>
                            <a class="btn btn-sm btn-primary" href="{{$receipt_prepayment->formPrintPath() .'/text'}}"
                               role="button" target="_blank">
                                <i class="glyphicon glyphicon-print"></i> {{ __t('TXT чек') }}
                            </a>
                            <a class="btn btn-sm btn-success" href="{{$receipt_prepayment->formPrintPath() .'/qrcode'}}"
                               role="button">
                                <i class="glyphicon glyphicon-qrcode"></i> {{ __t('QR код чека') }}
                            </a>
                        @endif
                    </fieldset>
                </div>

                <div class="row text-left">
                    <fieldset>
                        @if(!$receipt_main_payment)
                            <button id_="send-to-checkbox" type="button" value="{{$id}}"
                                    class="send-to-checkbox btn btn-success">{!!  str_replace(['[amount]','[currency]'],[$order->getTotalWithoutPrepayment(), setting('currency')],__cms("Фискализация оплаты [amount] [currency]") ) !!}</button>
                            <br/>
                        @else
                            <p>{!! str_replace(['[amount]','[currency]'],[$order->getTotalWithoutPrepayment(), setting('currency')],__cms("Чеки Checkbox.ua оплата [amount] [currency]")) !!}</p>
                            <a class="btn btn-sm btn-warning" href="{{$receipt_main_payment->formPrintPath() .'/html'}}"
                               role="button" target="_blank">
                                <i class="glyphicon glyphicon-print"></i> {{ __t('HTML чек') }}
                            </a>
                            <a class="btn btn-sm btn-info" href="{{$receipt_main_payment->formPrintPath() .'/pdf'}}"
                               role="button" target="_blank">
                                <i class="glyphicon glyphicon-print"></i> {{ __t('PDF чек') }}
                            </a>
                            <a class="btn btn-sm btn-primary" href="{{$receipt_main_payment->formPrintPath() .'/text'}}"
                               role="button" target="_blank">
                                <i class="glyphicon glyphicon-print"></i> {{ __t('TXT чек') }}
                            </a>
                            <a class="btn btn-sm btn-success"
                               href="{{$receipt_main_payment->formPrintPath() .'/qrcode'}}" role="button">
                                <i class="glyphicon glyphicon-qrcode"></i> {{ __t('QR код чека') }}
                            </a>
                    </fieldset>
                    @endif
                </div>
            @endif
        </div>
    </div>
</section>
<script>
    const _this = $(this);

    $('.send-to-checkbox').click(function () {
        const el = $(this);
        const orderid = el.val();
        const type = el.data('type') || 'main_payment';
        let fop = $('[name="legal_entities_recipient_id"] option:selected').text();

        if (!$('[name="legal_entities_recipient_id"]').val()) {
            jQuery.SmartMessageBox({
                title: "{{__cms("Фискализация чека")}}",
                content: "{{__cms("Не выбрано юридическое лицо. Выберите его из списка и повторите фискализацию снова")}}",
                buttons: '[{{__cms("Закрыть")}}]'
            });
        } else {
            jQuery.SmartMessageBox({
                title: "{{__cms("Подтверждение действия")}}",
                content: "{{__cms("Вы инициируете фискализацию чека")}} <b>" + fop +"</b>",
                buttons: '[{{__cms("Отмена")}}][{{__cms("Да, сделать фискализацию")}}]'
            }, function (ButtonPressed) {
                if (ButtonPressed === '{{__cms("Да, сделать фискализацию")}}') {
                    startCheckboxRequest(orderid, type);
                }
            });
        }
    });

    function startCheckboxRequest(orderid, type )
    {
        $.get('/admin/send/order/' + orderid + '/' + type, function (data) {

            if (data) {
                //let btnSend = document.getElementById('send-to-checkbox');
                //el.attr('disabled', true);
                let message = '';
                if (data.message) {
                    message = data.message;
                } else if (data.receipt) {
                    message = '{{__cms("Фискализация прошла успешно. Перезайдите в этот заказ, чтобы увидеть ссылки на чеки")}}';
                    $('[name="is_online_payed"]').val(1); //Статус оплаты Оплачен
                } else {
                    message = '{{__cms("Что-то пошло не так, попробуйте позже")}}';
                }
                jQuery.SmartMessageBox({
                    title: "{{__cms("Фискализация чека")}}",
                    content: message,
                    buttons: '[{{__cms("Закрыть")}}]'
                }, function (ButtonPressed) {
                    if (ButtonPressed === '{{__cms("Закрыть")}}') {
                        if (typeof reloadPage == 'function') {
                            reloadPage(orderid);
                        }
                    }
                });
            }
        });
    }

</script>
