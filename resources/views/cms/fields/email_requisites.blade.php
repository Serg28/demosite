<button id="send-requisites-to-email" class="btn btn-sm btn-warning" type="button"  >
    <i class="glyphicon glyphicon-envelope"></i> {{__cms("Реквизиты на Email")}}
</button>

<script>
    $('#send-requisites-to-email').click(function () {

        if (!$('[name="legal_entities_recipient_id"]').val()) {
            jQuery.SmartMessageBox({
                title : "{{__cms("Реквизиты на Email")}}",
                content : "{{__cms("Не выбрано юридическое лицо. Выберите его из списка и повторите действие снова")}}",
                buttons : '[{{__cms("Закрыть")}}]'
            });
        } else {
            $.get('{{ route('document.email', $field->getAllData()) }}', function (data) {
                if (data) {
                    let btnSendEmail = document.getElementById('send-requisites-to-email');
                    btnSendEmail.setAttribute('disabled', true);
                    jQuery.SmartMessageBox({
                        title: "{{__cms("Реквизиты на Email")}}",
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
