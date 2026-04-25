<section class="nova-poshta-block"
         @if($order->delivery_id!==2)style="display: none"@endif>
        <div class="row">
            <div class="col" style="width: 100%">
                <label class="label" for="platezi">{{__cms("Новая Почта")}}</label>
                <button id="np-form-create-en" onclick="npFormCreateEn()" type="button" value="{{$id}}" class="btn btn-sm btn-primary" style="margin-bottom: 5px"><span
                        class="glyphicon glyphicon-inbox"></span> {{ __cms("Создание/изменение ЭН Новой почты") }}
                </button>
                @if($order->tracking_num)

                    <a class="btn btn-sm btn-info"
                       href="{{route('novaposhta.print',['order'=>$order, 'type'=>'html'])}}" role="button"
                       target="_blank"  style="margin-bottom: 5px">
                        <i class="glyphicon glyphicon-print"></i> {{ __cms("Накладная HTML") }}
                    </a>
                    <a class="btn btn-sm btn-warning"
                       href="{{route('novaposhta.print',['order'=>$order, 'type'=>'pdf'])}}" role="button"
                       target="_blank"  style="margin-bottom: 5px">
                        <i class="glyphicon glyphicon-print"></i> {{ __cms("Накладная PDF") }}
                    </a>
                    <button id="np-tracking" onclick="npTracking()" class="btn btn-sm btn-success"
                            href="{{route('novaposhta.tracking',['order'=>$order])}}" type="button"  style="margin-bottom: 5px">
                        <i class="glyphicon glyphicon-question-sign"></i> {{ __cms("Проверить статус посылки") }}
                    </button>

                    <div class="alert alert-info" style="margin-top: 15px">{{__cms("Внимание! После печати ТТН ее редактирование будет недоступно. Возможно только удаление и создание новой ТТН")}}</div>
                @endif
            </div>
        </div>
</section>

<script>

    $('[name="delivery_id"]').change(function () {
        if (($(this).val() == 2) &&
            ($('[name=city_id]').val() && $('[name=np_warehouse_id]').val())
        ) {
            $('.nova-poshta-block').show();
        } else {
            $('.nova-poshta-block').hide();
        }
    });


    function doSaveOrder(order) {
        TableBuilder.doEdit(
            order,
            "orders",
            '{{request('foreign_field_id')}}',
            '{!! request('foreign_attributes')!!}'
        );
    }

    function npFormCreateEn() {
        // $('#np-form-create-en').click(function () {
        //doSaveOrder({{$order->id}});
        var city_id = $('[name=city_id]').val();
        var np_warehouse_id = $('[name=np_warehouse_id]').val();

        if (!city_id || !np_warehouse_id) {
            if (!city_id) {
                $('[name="city_id"]').parents('.input_content').addClass('has-error');
            } else {
                $('[name="city_id"]').parents('.input_content').removeClass('has-error');
            }
            if (!np_warehouse_id) {
                $('[name="np_warehouse_id"]').parents('.input_content').addClass('has-error');
            } else {
                $('[name="np_warehouse_id"]').parents('.input_content').removeClass('has-error');
            }

            jQuery.SmartMessageBox({
                title: "{{__cms("ЭН Новой почты")}}",
                content: "{{__cms("Заполните, пожалуйста, Город и Отделение Новой почты, прежде чем продолжить")}}",
                buttons: '[{{__cms("Закрыть")}}]'
            });

        } else {
            //showPreloader();
            $.get('/admin/np/form/{{$order->id}}', { "_": $.now() }, function (data) {
                if (data.html) {
                    $('#modal_form_edit').append(data.html);
                    //$('body').append(data.html);
                    $('#npttn-create-modal').modal('show');
                    hidePreloader();
                }
            });
        }
        //});
    }

    function npTracking() {
        //$('#np-tracking').click(function (e) {
        $.get('{{route('novaposhta.tracking',['order'=>$order])}}', function (data) {
            if (data.success) {
                jQuery.SmartMessageBox({
                    title: "<h2>{{__cms("Трекинг посылки")}} <strong>" + data.data.Number + "</strong></h2>",
                    content: data.data.Status,
                    buttons: '[{{__cms("Закрыть")}}]'
                });
            }
        });
        //});
    }

</script>
