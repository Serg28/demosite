<button id="send-requisites-to-sms" class="btn btn-sm btn-warning" type="button"  >
    <i class="glyphicon glyphicon-send"></i> {{__cms("Реквизиты в SMS")}}
</button>

<script>
    $('#send-requisites-to-sms').click(function () {

        if (!$('[name="legal_entities_recipient_id"]').val()) {
            jQuery.SmartMessageBox({
                title : "{{__cms("Реквизиты в SMS")}}",
                content : "{{__cms("Не выбрано юридическое лицо. Выберите его из списка и повторите действие снова")}}",
                buttons : '[{{__cms("Закрыть")}}]'
            });
        } else {
            $.get('{{ route('document.sms', $field->getAllData()) }}', function (data) {
                if (data) {
                    let btnSendSMS = document.getElementById('send-requisites-to-sms');
                    btnSendSMS.setAttribute('disabled', true);
                    jQuery.SmartMessageBox({
                        title: "{{__cms("Реквизиты в SMS")}}",
                        content: data.message,
                        buttons: '[{{__cms("Закрыть")}}]'
                    }, function (ButtonPressed) {
                        if (ButtonPressed === '{{__cms("Закрыть")}}') {

                        }
                    });
                }
            });
        }
    })
</script>
