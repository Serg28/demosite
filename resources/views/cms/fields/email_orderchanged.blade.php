<button id="send-order-to-email" class="btn btn-sm btn-warning" type="button">
    <i class="glyphicon glyphicon-envelope"></i> {{__cms("Обновленный заказ на Email")}}
</button>

<script>
    $('#send-order-to-email').click(function () {
        jQuery.SmartMessageBox({
            title: "{{__cms("Обновленный заказ на Email")}}",
            content: "{{__cms("Вы действительно хотите отправить клиенту письмо с обновленным составом заказа?")}}",
            buttons: '[{{__cms("Нет")}}][{{__cms("Да, отправить письмо")}}]'
        }, function (ButtonPressed) {
            if (ButtonPressed === '{{__cms("Да, отправить письмо")}}') {
                $.get('{{ route('document.order.changed.email', $field->getAllData()) }}', function (data) {
                    if (data.message) {

                        let btnOrderSendEmail = document.getElementById('send-order-to-email');
                        btnOrderSendEmail.setAttribute('disabled', true);

                        setTimeout(function(){
                            jQuery.SmartMessageBox({
                                title: "{{__cms("Обновленный заказ на Email")}}",
                                content: data.message,
                                buttons: '[{{__cms("Закрыть")}}]'
                            }, function (ButtonPressed) {
                                if (ButtonPressed === '{{__cms("Закрыть")}}') {

                                }
                            });
                        }, 1000);
                    }
                });
            }
        });
    })
</script>
