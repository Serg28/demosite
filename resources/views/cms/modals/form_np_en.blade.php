<div class="modal fade" id="npttn-create-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true" data-backdrop="static" style="z-index: 200000">

    <div class="modal-dialog" style="max-width: 1200px; width: auto">
        <div class="form-preloader smoke_lol"><i class="fa fa-gear fa-4x fa-spin"></i></div>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" onclick="closeNPModal()"
                        _data-dismiss="modal" aria-hidden="true">×
                </button>
                <h4 class="modal-title" id="myModalLabel">{{__cms("Создание ТТН Новой почты")}}</h4>
            </div>
            <div class="modal-body">
                @csrf
                <div id="np-loading" style="display: none">
                    <span>{{__cms("Отправляем данные в Новую почту, подождите!")}}</span>
                </div>

                <form id="npttn-form">
                    <div class="row form-horizontal form-slim">

                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="col-sm-4 control-label "><i
                                        class="icon-user2"></i> {{__cms("Номер заказа")	}}</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name="np_order_id" readonly
                                           value="{!! isset($np_info['np_order_id']) ? $np_info['np_order_id'] : $order->id !!}"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label "><i
                                        class="icon-user2"></i> {{__cms("Имя получателя")}} *</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name="name" required
                                           value="{!! isset($np_info['firstname']) ? e($np_info['firstname']) : ($order->receiver=='other' ? e($order->receiver_first_name) : e($order->first_name))!!}"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label "><i
                                        class="icon-user2"></i> {{__cms("Фамилия получателя")}} *</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name="lastname" required
                                           value="{!! isset($np_info['lastname']) ? e($np_info['lastname']) : ($order->receiver=='other' ? e($order->receiver_last_name) : e($order->last_name))!!}"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label "><i
                                        class="icon-user2"></i> {{__cms("Телефон получателя")}} *</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name="phone" required
                                           value="{!! isset($np_info['phone']) ? $np_info['phone'] : ($order->receiver=='other' ? $order->receiver_phone : $order->phone)!!}"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label "><i
                                        class="icon-user2"></i> {{__cms("Город получателя")}} *</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name="city" required
                                           value="{!! isset($np_info['city']) ? e($np_info['city']) : (e($order->city->t('origin')) ?? '')!!}"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label "><i
                                        class="icon-user2"></i> {{__cms("Отделение получателя")}} *</label>
                                <div class="col-sm-8">
                                 {{--   <input type="text" class="form-control" name="sel2" required
                                           value="{!! isset($np_info['warehouse']) ? $np_info['warehouse'] : str_replace(array('(',')'), '', $order->pickUpTheGoods())!!}"/> --}}
                                    <input type="text" class="form-control" name="sel2" required
                                           value="{!! isset($np_info['warehouse']) ? e($np_info['warehouse']) : (e($order->npWarehouse->t('title')) ?? '') !!}"/>
                                </div>
                            </div>
{{--
                            <div class="form-group">
                                <label class="col-sm-4 control-label "><i
                                        class="icon-user2"></i> {{__cms("Отделение отправки")}} *</label>
                                <div class="col-sm-8">
                                    @php
                                        $client_np_ref = isset($np_info['np_client_np_ref']) ? $np_info['np_client_np_ref'] : setting('client_np_ref');
                                    @endphp
                                    <select data-placeholder="{{__cms("Отделение отправки")}}..."
                                            name="np_client_np_ref" class="form-control select2" required
                                            tabindex="2">

                                        <option value=""> - </option>
                                        @foreach($departments as $department)
                                            <option value="{{$department->ref}}"
                                                    @if($department->ref==@$client_np_ref)selected="selected"@endif>{{$department->t('title')}}</option>
                                        @endforeach
                                    </select>

                                </div>
                            </div>

--}}

                            <div class="form-group">
                                <label class="col-sm-4 control-label "><i
                                        class="icon-user2"></i> {{__cms("Город отправки")}} *</label>
                                <div class="col-sm-8">
                                    <input class="np_client_np_cityref select2-enabled" type="hidden" style="width: 100%" name="np_client_np_cityref" value="{{$np_info['np_client_np_cityref'] ?? setting('client_np_cityref')}}">
                                </div>
                            </div>
                            <script>
                                $(document).ready(function() {
                                    $('.np_client_np_cityref').select2({
                                        ajax: {
                                            url: '/admin/np/sender_cities/',
                                            dataType: 'json',
                                            quietMillis: {{ $search['quiet_millis'] ?? '350' }},
                                            data: function (term, page) { // page is the one-based page number tracked by Select2
                                                return {
                                                    currentVal: '{{@$np_info['np_client_np_cityref']}}',
                                                    q: term, //search term
                                                    limit: {{ $search['per_page'] ?? '20' }}, // page size
                                                    page: page, // page number
                                                    @if (isset($row['id']))
                                                    page_id : {{$row['id']}},
                                                    @endif
                                                    ident: 'np_client_np_cityref',
                                                    query_type: 'many_to_many_ajax_search',
                                                };
                                            },
                                            delay: 250,
                                            results: function (t, page) {
                                                return {
                                                    results: $.map(t, (function(t) {
                                                        return {
                                                            text: t.text,
                                                            id: t.id
                                                        }
                                                    }))
                                                }
                                            },
                                            cache: true
                                        },
                                        formatResult: function(item) {
                                            return item.text;
                                        },
                                        formatSelection: function(item) {
                                            //$('.np_sender_warehouse').val('').trigger('change');
                                            $('.np_sender_warehouse').select2("data", '');
                                            return item.text;
                                        },
                                        formatNoMatches : function () {
                                            return 'По результату поиска ничего не найдено';
                                        },
                                        formatSearching: function () { return "Ищет..."; },
                                        formatInputTooShort: function (input, min) { var n = min - input.length; return "Введите еще " + n + "   символ "; },
                                        placeholder: '{{__cms("Выберите город отправки")}}',
                                        allowClear: true,
                                        dropdownCssClass: "bigdrop", // apply css that makes the dropdown taller
                                        dropdownParent: $('#npttn-create-modal'),
                                        escapeMarkup: function (m) { return m; } // we do not want to escape markup since we are displaying html in results
                                    });

                                    $('#npttn-form').click(function(event) {
                                        var target = $(event.target);

                                        if (!target.closest('.select2-container').length) {
                                            $('.np_client_np_cityref').select2('close');
                                        }
                                    });

                                    @if($senderNPCity)
                                    $('.np_client_np_cityref').select2("data",@js($senderNPCity));
                                    @endif
                                });
                            </script>





                            <div class="form-group">
                                <label class="col-sm-4 control-label "><i
                                        class="icon-user2"></i> {{__cms("Отделение отправки")}} *</label>
                                <div class="col-sm-8">
                                    <input class="np_sender_warehouse select2-enabled" type="hidden" style="width: 100%" name="np_client_np_ref" value="{{@$client_np_ref ?: setting('client_np_ref')}}">
                                </div>
                            </div>

                            <script>
                                $(document).ready(function() {
                                    $('.np_sender_warehouse').select2({
                                        ajax: {
                                            url: '/admin/np/sender_warehouses/',
                                            dataType: 'json',
                                            quietMillis: {{ $search['quiet_millis'] ?? '350' }},
                                            data: function (term, page) { // page is the one-based page number tracked by Select2
                                                return {
                                                    currentVal: '{{@$client_np_ref ?: setting('client_np_ref')}}',
                                                    q: term, //search term
                                                    limit: {{ $search['per_page'] ?? '20' }}, // page size
                                                    page: page, // page number
                                                    city: $('[name="np_client_np_cityref"]').val() ? $('[name="np_client_np_cityref"]').val() : '{{$np_info['np_client_np_cityref'] ?? setting('client_np_cityref')}}',
                                                    @if (isset($row['id']))
                                                    page_id : {{$row['id']}},
                                                    @endif
                                                    ident: 'np_client_np_ref',
                                                    query_type: 'many_to_many_ajax_search',
                                                };
                                            },
                                            delay: 250,
                                            results: function (t, page) {
                                                return {
                                                    results: $.map(t, (function(t) {
                                                        return {
                                                            text: t.text,
                                                            id: t.id
                                                        }
                                                    }))
                                                }
                                            },
                                            cache: true
                                        },
                                        formatResult: function(item) {
                                            return item.text;
                                        },
                                        formatSelection: function(item) {
                                            return item.text;
                                        },
                                        formatNoMatches : function () {
                                            return 'По результату поиска ничего не найдено';
                                        },
                                        formatSearching: function () { return "Ищет..."; },
                                        formatInputTooShort: function (input, min) { var n = min - input.length; return "Введите еще " + n + "   символ "; },
                                        placeholder: '{{__cms("Выберите отделение отправки")}}',
                                        allowClear: true,
                                        dropdownCssClass: "bigdrop", // apply css that makes the dropdown taller
                                        dropdownParent: $('#npttn-create-modal'),
                                        escapeMarkup: function (m) { return m; } // we do not want to escape markup since we are displaying html in results
                                    });

                                    $('#npttn-form').click(function(event) {
                                        var target = $(event.target);

                                        if (!target.closest('.select2-container').length) {
                                            $('.np_sender_warehouse').select2('close');
                                        }
                                    });

                                    @if($senderNPWarehouse)
                                    $('.np_sender_warehouse').select2("data",@js($senderNPWarehouse));
                                    @endif
                                });
                            </script>


















                        </div>
                        <div class="col-sm-6">
                            <!--<h6>Нова пошта:</h6>-->

                            <div class="form-group">
                                <label class="col-lg-4 control-label "><i
                                        class="icon-user2"></i> {{__cms("Объявленная стоимость")}} *</label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control" name="np_price" required
                                           value="{{ isset($np_info['np_price']) ? $np_info['np_price'] : ''}}"/>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-lg-4 control-label "><i
                                        class="icon-coin"></i> {{__cms("Кто оплачивает доставку")}} *</label>
                                <div class="col-lg-8">
                                    <select data-placeholder="{{__cms("Кто оплачивает доставку")}}..."
                                            name="np_delivery_pay" class="form-control" required
                                            tabindex="2">
                                        <option value=""> -</option>
                                        @foreach(['Одержувач'=>'Получатель','Відправник'=>'Отправитель'] as $k=>$v)
                                            <option value="{{$k}}"
                                                    @if(isset($np_info['np_delivery_pay']) && $k == $np_info['np_delivery_pay']) selected="selected" @endif>{{__cms($v)}}</option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>


                            <div class="form-group">
                                <label class="col-lg-4 control-label "><i
                                        class="icon-user2"></i> {{__cms("Описание посылки")}} *</label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control" name="np_comment" required
                                           value="{{isset($np_info['np_comment']) ? $np_info['np_comment'] : ''}}"/>
                                </div>
                            </div>


                            <div class="form-group">
                                <label class="col-lg-4 control-label "><i
                                        class="icon-user2"></i> {{__cms("Наложенный платеж")}}</label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control" name="np_after_payment_cost"
                                           value="{{isset($np_info['np_after_payment_cost']) ? $np_info['np_after_payment_cost'] : ''}}"/>
                                </div>
                            </div>
                            @if($order)
                                <div class="form-group">
                                <div class="col-lg-8 col-lg-offset-4">
                                    <p class="alert alert-info" >
                                        {{__cms("Сумма заказа")}}: <strong>{{$order->getPriceForDocumentsAttribute()}}</strong> {{ setting('currency') }}<br>
                                        {{__cms("Итого оплачено")}}: <strong>{{$order->payment()->cost()}}</strong> {{ setting('currency') }}<br>
                                        {{__cms("Итого остаток")}}: <strong>{{$order->payment()->remaining()}}</strong> {{ setting('currency') }}
                                    </p>
                                </div>
                                </div>
                            @endif

                        </div>
                        <div class="col-sm-6">

                            <div class="form-group">
                                <label class="col-lg-4 control-label "><i class="icon-user2"></i> {{__cms("Общий вес")}}
                                    *
                                </label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control" name="np_weight_general_kg" required
                                           readonly="readonly"
                                           value="{{isset($np_info['np_weight_general_kg']) ? $np_info['np_weight_general_kg'] : ''}}"/>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-lg-4 control-label "><i
                                        class="icon-user2"></i> {{__cms("Вес объемный (Ширина х Длина х Высота/4000)")}}
                                    *
                                </label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control" name="np_volume_general_kg" required
                                           readonly="readonly"
                                           value="{{isset($np_info['np_volume_general_kg']) ? $np_info['np_volume_general_kg'] : ''}}"/>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-lg-4 control-label "><i
                                        class="icon-user2"></i> {{__cms("Общий объем отправления")}} *</label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control" name="np_volume_general_m3" required
                                           readonly="readonly"
                                           value="{{isset($np_info['np_volume_general_m3']) ? $np_info['np_volume_general_m3'] : ''}}"/>
                                </div>
                            </div>


                        </div>

                        <div class="col-sm-12">

                            <div class="form-group">
                                <label class="col-sm-4 control-label "><i
                                        class="icon-user2"></i> {{__cms("Количество мест")}} *</label>
                                <div class="col-sm-8">
                                    <select data-placeholder="{{__cms("Количество мест")}}..." class="form-control"
                                            required
                                            name="np_places">
                                        <option value=""> -</option>
                                        @foreach([1, 2, 3, 4, 5, 6, 7, 8, 9, 10] as $i)
                                            <option value="{{$i}}"
                                                    @if(isset($np_info['np_places']) && $i == $np_info['np_places']) selected="selected" @endif>
                                                {{$i}}
                                            </option>
                                        @endforeach

                                    </select>
                                </div>

                            </div>

                            <div class="form-group">
                                <div class="col-sm-12">
                                    <table style="width:100%;display:none" id="count_places">
                                        <thead>
                                        <tr>
                                            <td>{{__cms("Ширина (см.)")}}</td>
                                            <td>{{__cms("Длина (см.)")}}</td>
                                            <td>{{__cms("Высота (см.)")}}</td>
                                            <td>{{__cms("Вес фактический (кг.)")}}</td>
                                            <td>{{__cms("Вес объемный (кг)")}}</td>
                                            <td>{{__cms("Об`єм (м³)")}}</td>
                                        </tr>
                                        </thead>
                                        <tbody>

                                        @if(isset($np_info['np_places_array']))
                                            @foreach($np_info['np_places_array'] as $key => $v)
                                                <tr>
                                                    <td><input type="number" class="form-control"
                                                               name="np_places_width_{{$key}}"
                                                               value="{{$v[0]['np_places_width']}}"></td>
                                                    <td><input type="number" class="form-control"
                                                               name="np_places_length_{{$key}}"
                                                               value="{{$v[0]['np_places_length']}}"></td>
                                                    <td><input type="number" class="form-control"
                                                               name="np_places_height_{{$key}}"
                                                               value="{{$v[0]['np_places_height']}}"></td>
                                                    <td><input type="number" class="form-control"
                                                               name="np_places_weight_{{$key}}"
                                                               value="{{$v[0]['np_places_weight']}}"></td>
                                                    <td><input type="number" class="form-control"
                                                               name="np_places_weight_volume_{{$key}}"
                                                               readonly="readonly"
                                                               value="{{$v[0]['np_places_weight_volume']}}"></td>
                                                    <td><input type="number" class="form-control"
                                                               name="np_places_volume_{{$key}}" readonly="readonly"
                                                               value="{{$v[0]['np_places_volume']}}"></td>
                                                </tr>
                                            @endforeach
                                        @endif
                                        </tbody>
                                    </table>
                                    {{--<button type="button" class="btn btn-primary  btn-sm" id="calculate_weight"><span
                                            class="glyphicon glyphicon-stats"></span> {{__cms("Рассчитать вес")}}
                                    </button>--}}
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <button type="button" class="btn btn-primary  btn-sm" id="calculate_weight"><span
                                    class="glyphicon glyphicon-stats"></span> {{__cms("Рассчитать вес")}}
                            </button>
                        </div>
                        <div class="col-sm-12">
                            <div id="errorNpmessage" style="display: none">
                            </div>
                        </div>
                    </div>
                </form>

            </div>
            <div class="modal-footer">
                <!--<a onclick="Tree.doCreateNode();" href="javascript:void(0);" class="btn btn-success btn-sm">
                    <span class="glyphicon glyphicon-floppy-disk"></span> Сохранить
                </a>-->
                <div class="btn-group" style="width: 100%">


                    <button type="button" class="btn btn-info btn-sm text-left" id="np_createEN"
                            @if(isset($np_info['ref']))style="display: none"@endif><span
                            class="glyphicon glyphicon-plus"></span>
                        {{__cms("Создать ЭН")}}
                    </button>

                    <button type="button" class="btn btn-info btn-sm text-left" id="np_updateEN"
                            @if(!isset($np_info['ref']))style="display: none"@endif><span
                            class="glyphicon glyphicon-edit"></span>
                        {{__cms("Обновить ЭН")}}
                    </button>
                    <button type="button" class="btn btn-danger btn-sm text-left" id="np_deleteEN"
                            @if(!isset($np_info['ref']))style="display: none"@endif><span
                            class="glyphicon glyphicon-trash"></span>
                        {{__cms("Удалить ЭН")}}
                    </button>

                    <a href="javascript:void(0);" class="btn btn-default" data-dismiss_="modal"
                       style="position: absolute;right: 0"
                       onclick="closeNPModal()">
                        {{__cms("Отменить")}}
                    </a>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>

<script>

    $(document).ready(function () {
        /*NP*/

        /*if ($('[name="delivery"]').val() == 'Нова Пошта') {
            $('.nova-poshta-block').show();
            $.uniform.update();
        }*/
        if ($('[name="np_places"]').val() != '') {
            $('#count_places').show();
            //$.uniform.update();
        }

        var np_ref = '{!! isset($np_info['np_price']) ? $np_info['np_price'] : '' !!}';
        if (np_ref == '') {
            $('#np_deleteEN').hide();
        }

        var np_ref = '{!! isset($np_info['ref']) ? $np_info['ref'] : '' !!}';
        if (np_ref != '') {
            $('#np_createEN').hide();
        } else {
            $('#np_updateEN').hide();
            $('#np_deleteEN').hide();
        }

        $('[name="np_places"]').on('change', function () {
            $('#count_places').show();
            var count = $(this).val();
            var html = '';
            $('#count_places tbody').empty();


            for (var i = 0; i < count; i++) {
                var html = '	<tr> ' +
                    '<td><input type="number" class="form-control" name="np_places_width_' + i + '" value=""/></td>' +
                    '<td><input type="number" class="form-control" name="np_places_length_' + i + '" value=""/></td>' +
                    '<td><input type="number" class="form-control" name="np_places_height_' + i + '" value=""/></td>' +
                    '<td><input type="number" class="form-control" name="np_places_weight_' + i + '" value=""/></td>' +
                    '<td><input type="text" class="form-control" name="np_places_weight_volume_' + i + '" readonly="readonly" value=""/></td>' +
                    '<td><input type="text" class="form-control" name="np_places_volume_' + i + '" readonly="readonly" value=""/></td>' +
                    '</tr>';
                $('#count_places tbody').append(html);
            }
        });

        $("#calculate_weight").on('click', function () {
            //считаем количество tr
            var rowCount = $('#count_places tbody tr').length;
            var smallValid = '';
            var totalSum = 0;
            var totalWeight = 0;
            var totalSumM3 = 0;


            for (var i = 0; i < rowCount; i++) {

                var np_places_width = $('[name="np_places_width_' + i + '"]').val();
                var np_places_length = $('[name="np_places_length_' + i + '"]').val();
                var np_places_height = $('[name="np_places_height_' + i + '"]').val();
                var np_places_weight = $('[name="np_places_weight_' + i + '"]').val();

                /*	if(np_places_width.isNumeric || np_places_width != null){

                    }els
                    */
                var sum = np_places_width * np_places_length * np_places_height / 4000;
                var sum_volume = np_places_width * np_places_length * np_places_height / 1000;

                totalSum += parseFloat(sum);
                totalWeight += parseFloat(np_places_weight);
                totalSumM3 += parseFloat(sum_volume);

                $('[name="np_places_weight_volume_' + i + '"]').val(sum);
                //$('[name="np_places_volume_' + i + '"]').val(sum_volume);
                $('[name="np_places_volume_' + i + '"]').val(sum_volume / 1000);

            }

            $('[name="np_weight_general_kg"]').val(totalWeight);
            $('[name="np_volume_general_kg"]').val(totalSum);
            $('[name="np_volume_general_m3"]').val(totalSumM3 / 1000);
        });


        //создаем электронную накладную + сохраняем все в базе данных
        $("#np_createEN").on('click', function (event) {
            processForm(event, '{{route('novaposhta.store')}}');
        });

        //обновляем электронную накладную + сохраняем все в базе данных
        $("#np_updateEN").on('click', function (event) {
            processForm(event, '{{route('novaposhta.update')}}');
        });
    });

    //Удаляем ЭН с НП и удаляем с БД
    $("#np_deleteEN").on('click', function () {

        $.SmartMessageBox({
            title: "{{__cms("ЭН Новой почты")}}",
            content: '{{__cms("Вы действительно желаете удалить данную ЭН из базы данных Новой Почты? Это действие нельзя отменить")}}',
            buttons: '[{{__cms("Отмена")}}][{{__cms("Удалить ЭН")}}]'
        }, function (ButtonPressed) {
            if (ButtonPressed === '{{__cms("Удалить ЭН")}}') {
                $.ajax({
                    type: "POST",
                    url: "{{route('novaposhta.delete')}}",
                    data: {orderid: '{{$order->id}}'},
                    success: function (data) {
                        if (data.success) {
                            $('[name="tracking_num"]').val('');
                        }
                        $.SmartMessageBox({
                            title: "{{__cms("ЭН Новой почты")}}",
                            content: (data.success) ? data.message : data.error,
                            buttons: '[{{__cms("Закрыть")}}]'
                        }, function (ButtonPressed) {
                            if (ButtonPressed === '{{__cms("Закрыть")}}') {
                                //$('#npttn-create-modal').modal('hide');
                                closeNPModal();
                            }
                        });
                    }
                });
            }
        });
    });


    function processForm(event, url) {
        //получаем все данные с нужных нам полей
        // данные пользователя
        var form = $('#npttn-form');
        /*var firstname = $('[name="name"]').val();
        lastname = $('[name="lastname"]').val();
        phone = $('[name="phone"]').val();
        city = $('[name="city"]').val();
        warehouse = $('[name="sel2"]').val();
        //данные о доставке
        var np_places = $('[name="np_places"]').val();
        np_price = $('[name="np_price"]').val();
        np_comment = $('[name="np_comment"]').val();
        np_delivery_pay = $('[name="np_delivery_pay"]').val();
        np_weight_general_kg = $('[name="np_weight_general_kg"]').val();
        np_volume_general_kg = $('[name="np_volume_general_kg"]').val();
        np_volume_general_m3 = $('[name="np_volume_general_m3"]').val();
        np_after_payment_cost = $('[name="np_after_payment_cost"]').val();
        np_client_np_ref = $('[name="np_client_np_ref"]').val();*/

        const firstname = form.find('[name="name"]').val();
        const lastname = form.find('[name="lastname"]').val();
        const phone = form.find('[name="phone"]').val();
        const city = form.find('[name="city"]').val();
        const warehouse = form.find('[name="sel2"]').val();

        // Данные о доставке
        const np_places = form.find('[name="np_places"]').val();
        const np_price = form.find('[name="np_price"]').val();
        const np_comment = form.find('[name="np_comment"]').val();
        const np_delivery_pay = form.find('[name="np_delivery_pay"]').val();
        const np_weight_general_kg = form.find('[name="np_weight_general_kg"]').val();
        const np_volume_general_kg = form.find('[name="np_volume_general_kg"]').val();
        const np_volume_general_m3 = form.find('[name="np_volume_general_m3"]').val();
        const np_after_payment_cost = form.find('[name="np_after_payment_cost"]').val();
        const np_client_np_ref = form.find('[name="np_client_np_ref"]').val();
        const np_client_np_cityref = form.find('[name="np_client_np_cityref"]').val();


        form.find('[required]').each(function (index, value) {
            if (!$(this).val()) {
                $(this).parents('.form-group').addClass('has-error');
            } else {
                $(this).parents('.form-group').removeClass('has-error');
            }
        });

        if (form.find('.has-error').length > 0) {
            event.preventDefault();
            event.stopPropagation();
            return false;
        }

        var places = [];
        $("#count_places tbody tr").each(function (n) {

            var np_places_width = $(this).find('[name=np_places_width_' + n + ']').val();
            np_places_length = $(this).find('[name=np_places_length_' + n + ']').val();
            np_places_height = $(this).find('[name=np_places_height_' + n + ']').val();
            np_places_weight = $(this).find('[name=np_places_weight_' + n + ']').val();
            np_places_weight_volume = $(this).find('[name=np_places_weight_volume_' + n + ']').val();
            np_places_volume = $(this).find('[name=np_places_volume_' + n + ']').val();

            places[n] = [
                {
                    "np_places_width": np_places_width,
                    "np_places_length": np_places_length,
                    //"np_places_height": np_places_width,
                    "np_places_height": np_places_height,
                    "np_places_weight": np_places_weight,
                    "np_places_weight_volume": np_places_weight_volume,
                    "np_places_volume": np_places_volume,
                }
            ]
        });

        np_places_array = places;

        $('#np-loading').show(500);
        $.ajax({
            type: "POST",
            url: url,
            data: {
                firstname: firstname,
                lastname: lastname,
                phone: phone,
                city: city,
                warehouse: warehouse,
                np_places: np_places,
                np_price: np_price,
                np_comment: np_comment,
                np_delivery_pay: np_delivery_pay,
                np_places_array: np_places_array,
                orderid: '{{$order->id}}',
                np_order_id: '{{$order->id}}',
                np_weight_general_kg: np_weight_general_kg,
                np_volume_general_kg: np_volume_general_kg,
                np_volume_general_m3: np_volume_general_m3,
                np_after_payment_cost: np_after_payment_cost,
                np_client_np_ref: np_client_np_ref,
                np_client_np_cityref: np_client_np_cityref
            },
            success: function (data) {
                console.log(data);
                let resultObj = data;
                let message = '';

                if (resultObj.success == true) {

                    var CostOnSite = resultObj.CostOnSite;
                    var IntDocNumber = resultObj.IntDocNumber;

                    if (CostOnSite != '') {
                        //$('[name="delivery_cost"]').val(CostOnSite);
                        //$('[name="delivery_cost"]').addClass({"background": "99CC99"});
                    }
                    if (IntDocNumber != '') {
                        $('[name="tracking_num"]').val(IntDocNumber);
                        $('[name="tracking_num"]').css("background", "99CC99");
                        $('[name="order_status_id"]').val(3); //Статус Готов к отправке
                    }

                    $('#np_createEN').hide();
                    $('#np_updateEN').show();
                    $('#np_deleteEN').show();

                    //message = '<strong>Все добре!</strong> ЕН створена: трекінг номер - ' + IntDocNumber + ', вартість доставки - ' + CostOnSite + ' грн. <br> Не забудьте збереги замовлення!';
                    message = resultObj.message;
                } else {
                    //message = '<strong>Виникла помилка</strong>: ' + resultObj.error + '. Спробуйте ще раз, або зверніться до адміністратора';
                    message = resultObj.error;
                }

                $("#errorNpmessage").html(message);
                jQuery.SmartMessageBox({
                    title: "{{__cms("ЭН Новой почты")}}",
                    content: message,
                    buttons: '[{{__cms("Закрыть")}}]'
                }, function (ButtonPressed) {
                    if (ButtonPressed === '{{__cms("Закрыть")}}') {
                        if (typeof reloadPage == 'function') {
                            reloadPage({{$order->id}});
                        }
                    }
                });
            },
            complete: function () {

                $('#np-loading').hide(500);

            },
            error: function (jqXHR, status, e) {
                if (jqXHR.status === 0) {
                    msg = 'Немає зв`язку з сервером Нової Пошти';
                } else if (jqXHR.status == 404) {
                    msg = 'Сервер Нової Пошти не відповідає, код 404';
                } else if (jqXHR.status == 500) {
                    msg = 'Сервер Нової Пошти не відповідає, код 500';
                } else if (exception === 'parsererror') {
                    msg = 'Помилка';
                } else if (exception === 'timeout') {
                    msg = 'Сервер Нової Пошти не відповідає, перевищено час обробки інформації.';
                } else if (exception === 'abort') {
                    msg = 'Запит було перервано.';
                } else {
                    msg = 'Інша помилка.\n' + jqXHR.responseText;
                }
                $('#np-loading').hide(500);
                $("#errorNpmessage").html('<div class="alert alert-danger">' + msg + '</div>');
                jQuery.SmartMessageBox({
                    title: "{{__cms("ЭН Новой почты")}}",
                    content: msg,
                    buttons: '[{{__cms("Закрыть")}}]'
                }, function (ButtonPressed) {
                    if (ButtonPressed === '{{__cms("Закрыть")}}') {
                        if (typeof reloadPage == 'function') {
                            reloadPage({{$order->id}});
                        }
                    }
                });
            },
            timeout: 20000 /*20 секунд*/
        });
        hidePreloader();
    }

    function closeNPModal(){
        $('#npttn-create-modal').remove();
        $('.modal-backdrop').remove();
        $( 'body' ).removeClass("modal-open");
    }
</script>
