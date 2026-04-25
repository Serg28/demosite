{{--
 Поскольку в системе не предусмотрено создание отдельных страниц для заказа и управление разметкой страницы,
 мы загружаем весь контент, а затем в нужном порядке расставляем элементы страницы с помощью jquery.

 Ниже подготовлен "костяк" страницы заказа. Комментариям помечены места, в которые будут перемещены указанные элементы.

 --}}

<section id="widget-grid" class="">
    <div class="row" style="padding-right: 13px; padding-left: 13px;">
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12 sortable-grid ui-sortable" style="padding-right: 0px; padding-left: 0px;">

            <div class="jarviswidget jarviswidget-color-blue" id="wid-id-1" data-widget-editbutton="false"
                 data-widget-colorbutton="false" data-widget-deletebutton="false" data-widget-sortable="false"
                 role="widget">

                <header>
                    <h2>
                        <a href='/admin'>{{__cms('Главная')}}</a> / <a href="{{$parent_url}}">{{$parent_title}}</a>
                        / {{$data->title}} № {{$orderNum}} (ID: {{$orderId}})
                    </h2>
                    <button class="btn btn-success btn-sm" style="float: right;margin-top:7px;margin-right:7px;" type="button" onclick="saveOrder({{$orderId}});">
                        <span class="glyphicon glyphicon-floppy-disk"></span> {{__cms('Сохранить')}}
                    </button>
                    <span class="jarviswidget-loader"><i class="fa fa-refresh fa-spin"></i></span>
                </header>

            </div>
        </article>
    </div>

    <div id="modal_form_edit">

        <form id="edit_form_orders" class="smart-form" method="post" action="/admin/actions/orders"
              novalidate="novalidate" style="display: none">
            <div class="row header_form">
                <fieldset>
                    <div class="col-lg-2 col-md-4 order-info"><!-- Номер заказа --></div>
                    <div class="col-lg-6 col-md-8 order-data-status">
                        <div class="col-md-4 order-data"><!-- Дата создания, Дата резерва --></div>
                        <div class="col-md-4 order-status-1-2"><!-- Статус заказа, Статус комплектации --></div>
                        <div class="col-md-4 order-status-3"><!-- Статус оплаты, 1С --></div>
                        <div class="col-md-12 order-cancel-reason"><!-- Причина расформирования --></div>
                    </div>
                    <div class="col-lg-4 col-md-12">
                        <!-- Кнопка печати заказа -->
                        <section class="without_label">
                            <a class="btn btn-sm btn-warning" href="/admin/printing/{{$orderId}}" role="button"
                               target="_blank">
                                <i class="glyphicon glyphicon-print"></i> {{__cms("Печать заказа")}}
                            </a>
                        </section>
                        <!-- /Кнопка печати заказа -->
                    </div>
                    <div class="col-lg-12 col-md-12">
                        <section class="user_is_null"><!-- Сообщение, если пользователь не определен --></section>
                    </div>

                </fieldset>
            </div>
            <div class="row body_form">
                <div class="col-md-6 order-left-col">
                    <fieldset>
                        <div style="display: flex;">
                            <div class="col-md-6 user-id"><!-- Клиент --></div>
                            <div class="col-md-6 user-orders-button">
                                <!-- Кнопка Все заказы клиента -->
                                @if($order->user_id)
                                <section>
                                    <label class="order-type virtual"></label>
                                    <button class="btn btn-info" type="button" onclick="orderuserButton({{$order->user_id}})">
                                        <span class="glyphicon glyphicon-shopping-cart"></span> {{__cms("Все заказы клиента")}}
                                    </button>
                                </section>
                                @endif
                                <!-- /Кнопка Все заказы клиента -->
                            </div>
                        </div>

                        <div class="col-lg-6 col-md-12 user-first-name"><!-- Имя клиента --></div>
                        <div class="col-lg-6 col-md-12 user-last-name"><!-- Фамилия клиента --></div>
                        <div class="col-lg-6 col-md-12 user-patronimyc"><!-- Отчество клиента --></div>
                        <div class="col-lg-6 col-md-12 user-email"><!-- Email клиента --></div>
                        <div class="col-lg-6 col-md-12 user-phone"><!-- Телефон клиента --></div>


                        <div class="col-lg-6 col-md-12 user-call-me without_label"><!-- Чекбокс Перезвонить мне --></div>
                        <div class="col-lg-12 col-md-12 receiver_form_orders">
                            <div class="col-lg-12 col-md-12 receiver"><!-- Селект Кто получатель --></div>
                            <div class="col-lg-6 col-md-12 receiver-first-name"><!-- Имя получателя --></div>
                            <div class="col-lg-6 col-md-12 receiver-last-name"><!-- Фамилия получателя --></div>
                            <div class="col-lg-6 col-md-12 receiver-patronymic-name"><!-- Отчество получателя --></div>
                            <div class="col-lg-6 col-md-12 receiver-phone"><!-- Телефон получателя --></div>
                            <div class="col-md-12 user-comment"><!-- Комментарий клиента --></div>
                        </div>
                    </fieldset>
                </div>
                <div class="col-md-6 order-right-col">Загрузка...</div>
            </div>
            <div class="row order_base">
                <fieldset>
                    <div class="order-products"><!-- Таблица товаров заказа --></div>
                    <div>
                        <section class="order-products-button"><!-- Кнопка Добавить товар --></section>
                    </div>
                    <div>
                        <div class="col-lg-10 col-md-8 col-xs-12 order-total-fields"><!-- Итоговые цифры заказа --></div>
                        <div class="col-lg-2 col-md-4 col-xs-12 order-total-cost-field"><!-- Итоговая сумма заказа --></div>
                    </div>
                </fieldset>
                <fieldset>
                    <div class="order-other-fields"><!-- Другие поля: Примечания менеджера, Комментарий из 1С --></div>
                </fieldset>
            </div>
            <div class="content-form"><!-- Остальные поля и табы --></div>
        </form>
    </div>

    {{-- Панель кнопок внизу --}}
    <div class="row" style="padding-right: 13px; padding-left: 13px;">
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12 sortable-grid ui-sortable" style="padding-right: 0px; padding-left: 0px;">

            <div class="jarviswidget jarviswidget-color-blue" id="wid-id-1" data-widget-editbutton="false"
                 data-widget-colorbutton="false" data-widget-deletebutton="false" data-widget-sortable="false"
                 role="widget">

                <header>
                    <button class="btn btn-success btn-sm" style="float: right;margin-top:7px;margin-right:7px;" type="button" onclick="saveOrder({{$orderId}});">
                        <span class="glyphicon glyphicon-floppy-disk"></span> {{__cms('Сохранить')}}
                    </button>
                    <span class="jarviswidget-loader"><i class="fa fa-refresh fa-spin"></i></span>
                </header>

            </div>
        </article>
    </div>
    {{--/Панель кнопок внизу--}}


</section>

<script>
    var dirty = false;
    const orderNum = '{{$orderNum}}';
    const orderOld = '{{$orderOld?? ''}}';

    $(document).ready(function () {
        checkClose();
        var id = {{$orderId}};
        reloadOrder(id);
        TableBuilder.action_url = '/admin/actions/orders';
    });
    //Загрузка/перезагрузка заказа
    function reloadOrder(id, callback = null) {
        $("title").html("{{__cms("Заказ")}} № " + orderNum + " (ID: " + id + ") - {{__cms("Административная часть сайта")}}");
        showPreloader();
        //var tabid = $('#edit_form_orders .nav-tabs .active a').attr('href');
        jQuery('#wid-id-1').find('tr[data-editing="true"]').removeAttr('data-editing');

        const data = [
            {name: "query_type", value: "show_edit_form"},
            {name: "id", value: id},
            {name: "__node", value: TableBuilder.getUrlParameter('node')}
        ];
        TableBuilder.action_url = '';
        jQuery.ajax({
            type: "POST",
            url: '/admin/actions/orders',
            data: data,
            dataType: 'json',
            success: function (response) {
                if (response.status) {
                    $('#modal_form_edit form .content-form').html($(response.html).find('.modal-body form').html());
                    $('#edit_form_orders').show();
                    rebuildForm(id);
                    TableBuilder.initFroalaEditor();
                    TableBuilder.handleActionSelect();
                    $(window).unbind("beforeunload");
                    checkClose();

                    if (callback !== null && typeof callback === 'function') {
                        callback(); // Вызывается только, если callback определён
                    }

                } else {
                    TableBuilder.showErrorNotification("Что-то пошло не так, попробуйте позже");
                }
                setAsDisabled();
                hidePreloader();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                var errorResult = jQuery.parseJSON(xhr.responseText);
                TableBuilder.showErrorNotification(errorResult.message);
                hidePreloader();
            }
        });
    }

    //Запрещаем изменения заказа, если он помечен как старый (поле id_old не пусто
    function setAsDisabled() {
        if(orderOld) {
            $('input,button,textarea,select').attr('disabled', true);
        }
    }

    function onOrderProductSaveEvent() {
        setTimeout(function () {
            saveOrder({{$orderId}});
        }, 2000);
    }

    function reloadPage(id) {
        reloadOrder(id)
    }

    function saveOrder(order) {
        $(window).unbind("beforeunload");
        dirty = false;
        showPreloader();

        TableBuilder.onDoEdit = function () {
            hidePreloader();
            reloadOrder(order);
        };

        TableBuilder.doEdit(
            order,
            "orders",
            '{{request('foreign_field_id')}}',
            '{!! request('foreign_attributes')!!}'
        );
    }

    window.saveOrder = saveOrder;

    function doSaveOrder(order) {
        TableBuilder.doEdit(
            order,
            "orders",
            '{{request('foreign_field_id')}}',
            '{!! request('foreign_attributes')!!}'
        );
    }

    function showPreloader() {
        $('#table-preloader').remove();
        $('#modal_form_edit').prepend('<div id="table-preloader" class="smoke_lol" style="display: none;"><i class="fa fa-cog fa-4x fa-spin"></i></div>');
        TableBuilder.showPreloader();
    }

    function hidePreloader() {
        TableBuilder.hidePreloader();
        $('#table-preloader').remove();
    }

    function deliveryActions() {
        var selectClass = 'delivery_id_foreign';
        var formClass = 'section_delivery_payment';
        selectAction(selectClass, formClass);
    }

    function receiverActions() {
        var selectClass = 'orders_receiver';
        var formClass = 'receiver_form_orders';
        selectAction(selectClass, formClass);
    }

    function selectAction(selectClass, formClass) {
        $('.' + formClass + ' select.' + selectClass).change(function () {
            $("." + formClass + " section.section_field").hide();
            $("." + formClass + " section.section_field." + $(this).val()).show();
        });

        $("." + formClass + " section.section_field").hide();

        if ($('.' + formClass + ' select.' + selectClass).val()) {
            $("." + formClass + " section.section_field." + $('select.' + selectClass).val()).show();
        }
    }

    function checkClose() {
        //var dirty = false;
        $('input,select,textarea').change(function () {
            dirty = true;
            $(window).on('beforeunload', function () {
                return 'Are you sure you want to leave?';
            });
        });
        $('nav a').click(function (e) {
            var event = e;
            if (dirty == true) {

                var proceed = confirm("{{__cms("Возможно, внесенные изменения не сохранятся.")}}");
                if (proceed) {
                    dirty = false;
                } else {
                    event.preventDefault();
                    event.stopImmediatePropagation();
                }
            }
            ;
        });
        $('input,select,textarea').keyup(function () {
            dirty = true;
            $(window).on('beforeunload', function () {
                return 'Are you sure you want to leave?';
            });
        });
    }

    function cancelreasonAction() {
        var field = 'select[name="order_status_id"]';
        var field2 = 'select[name="cancel_reason_id"]'; //
        var field3 = '[name="cancel_reason"]'; //
        $(field).change(function () {
            $(".section_cancel_reason").hide();
            $(".section_cancel_reason_id").hide(); //
            //$(".section_cancel_reason." + $(field).val()).show();
            $(".section_cancel_reason_id." + $(field).val()).show();
        });

        $(field2).change(function () {
            $(".section_cancel_reason").hide(); //
            $(".section_cancel_reason." + $(field).val() + $(field2).val()).show();
        });

        $(".section_cancel_reason").hide();
        $(".section_cancel_reason_id").hide(); //

        if ($(field).val()) {
            $(".section_cancel_reason_id." + $(field).val()).show();
        }
        //--
        if ($(field2).val()) {
            $(".section_cancel_reason." + $(field).val() + $(field2).val()).show();
        }
        //--
    }

    function orderuserButton(user_id) {
        if (user_id) {
            TableBuilder.action_url = '/admin/actions/users';
            TableBuilder.getEditForm(user_id);
            setTimeout(function () {
                $('[href="#tabformusers-1"]').click();
            }, 1000);
        } else {
            jQuery.SmartMessageBox({
                title: '{{__cms("Внимание")}}',
                content: '{{__cms("Клиент не выбран. Пожалуйста, выберите клиента из списка, сохраните заказ и повторите запрос еще раз")}}',
                buttons: '[{{__cms("Закрыть")}}]'
            });
        }
    }

    function rebuildForm(id) {
        var form = '#edit_form_orders';
        var header_form = form + ' .header_form';
        var body_form = form + ' .body_form';
        var order_base = form + ' .order_base';

        // ----- HEADER ------
        //Order ID
        var order_info = $(header_form + ' .order-info');
        order_info.html('<section><label class="label">{{__cms("Заказ")}} @if($is_prom) (Prom #{{$order->prom_id}}) @elseif($is_quick) (Быстрый) @else c сайта @endif</label><div id="order-id"># {{$orderNum}} <span class="badge-orderid">(ID: {{$orderId}})</span> </div></section>');
        //Manager
        $(form + ' [name="manager_id"]').closest('section').addClass('section_manager_id');
        $('.section_manager_id').appendTo(order_info);

        //Data created
        var order_data = $(header_form + ' .order-data');
        order_data.html('');
        $(form + ' #created_atorders').closest('section').addClass('section_created_atorders');
        $('.section_created_atorders').appendTo(order_data);
        //Data reserved
        $(form + ' #reserved_toorders').closest('section').addClass('section_reserved_toorders');
        $('.section_reserved_toorders').appendTo(order_data);

        //Status order
        var order_status_1_2 = $(header_form + ' .order-status-1-2');
        order_status_1_2.html('');
        $(form + ' [name="order_status_id"]').closest('section').addClass('section_order_status_id');
        $('.section_order_status_id').appendTo(order_status_1_2);
        //Status complect
        $(form + ' [name="complect_status_id"]').closest('section').addClass('section_complect_status_id');
        $('.section_complect_status_id').appendTo(order_status_1_2);

        //order-status-3
        var order_status_3 = $(header_form + ' .order-status-3');
        order_status_3.html('');
        $(form + ' [name="is_online_payed"]').closest('section').addClass('section_order-status-3');
        $('.section_order-status-3').appendTo(order_status_3);

        //1C
        $('<section><label class="label">1с</label><div class="order-process-1c">{!! $order->processed_1c ? '<i class="glyphicon glyphicon-ok"></i>&nbsp;&nbsp;'.__cms("Проведен в 1с") : '<i class="glyphicon glyphicon-minus"></i>&nbsp;&nbsp;'.__cms("Не проведен в 1с") !!} </div></section>').appendTo(order_status_3);

        //cancel_reason
        var order_cancel_reason = $(header_form + ' .order-cancel-reason');
        order_cancel_reason.html('');
        $(form + ' [name="cancel_reason_id"]').closest('section').addClass('section_cancel_reason_id');
        $('.section_cancel_reason_id').appendTo(order_cancel_reason);
        $(form + ' [name="cancel_reason"]').closest('section').addClass('section_cancel_reason');
        $('.section_cancel_reason').appendTo(order_cancel_reason);

        // ---- /HEADER ----


        // ---- BODY -----
        // Left col
        var order_left_col = body_form + ' .order-left-col';

        //User_Id
        var order_leftcol_user_id = $(order_left_col + ' .user-id');
        order_leftcol_user_id.html('');
        $(form + ' [name="user_id"]').closest('section').addClass('section_user_id');
        $('.section_user_id').appendTo(order_leftcol_user_id);

        if (!$('input[name="user_id"]').val()) {
            $('.user_is_null').html('<div class="alert alert-warning">{{__cms("Внимание! В заказе не выбран клиент. Для успешной выгрузки в 1С выберите клиента из списка или полностю заполните ФИО, Email, и телефон.")}}');
        } else {
            $('.user_is_null').html('');
        }

        //User_orders_button
        //Later...

        //User name
        // first_name
        var order_leftcol_user_first_name = $(order_left_col + ' .user-first-name');
        order_leftcol_user_first_name.html('');
        $(form + ' [name="first_name"]').closest('section').addClass('section_first_name');
        $('.section_first_name').appendTo(order_leftcol_user_first_name);

        // last_name
        var order_leftcol_user_last_name = $(order_left_col + ' .user-last-name');
        order_leftcol_user_last_name.html('');
        $(form + ' [name="last_name"]').closest('section').addClass('section_last_name');
        $('.section_last_name').appendTo(order_leftcol_user_last_name);

        //patronymic
        var order_leftcol_user_patronimyc = $(order_left_col + ' .user-patronimyc');
        order_leftcol_user_patronimyc.html('');
        $(form + ' [name="patronymic"]').closest('section').addClass('section_patronymic');
        $('.section_patronymic').appendTo(order_leftcol_user_patronimyc);

        //phone
        var order_leftcol_user_phone = $(order_left_col + ' .user-phone');
        order_leftcol_user_phone.html('');
        $(form + ' [name="phone"]').closest('section').addClass('section_phone');
        $('.section_phone').appendTo(order_leftcol_user_phone);

        //email
        var order_leftcol_user_email = $(order_left_col + ' .user-email');
        order_leftcol_user_email.html('');
        $(form + ' [name="email"]').closest('section').addClass('section_email');
        $('.section_email').appendTo(order_leftcol_user_email);

        //call-me
        var order_leftcol_user_call_me = $(order_left_col + ' .user-call-me');
        order_leftcol_user_call_me.html('');
        $(form + ' [name="call_me"]').closest('section').addClass('section_call_me');
        $('.section_call_me').appendTo(order_leftcol_user_call_me);

        //receiver
        var order_leftcol_receiver = $(order_left_col + ' .receiver');
        order_leftcol_receiver.html('');
        $(form + ' [name="receiver"]').closest('section').addClass('section_receiver col-lg-6 col-md-12');
        $('.section_receiver').appendTo(order_leftcol_receiver);

        //receiver_first_name
        var order_leftcol_receiver_first_name = $(order_left_col + ' .receiver-first-name');
        order_leftcol_receiver_first_name.html('');
        $(form + ' [name="receiver_first_name"]').closest('section').addClass('section_receiver_first_name');
        $('.section_receiver_first_name').appendTo(order_leftcol_receiver_first_name);

        //receiver_last_name
        var order_leftcol_receiver_last_name = $(order_left_col + ' .receiver-last-name');
        order_leftcol_receiver_last_name.html('');
        $(form + ' [name="receiver_last_name"]').closest('section').addClass('section_receiver_last_name');
        $('.section_receiver_last_name').appendTo(order_leftcol_receiver_last_name);

        //receiver_patronymic_name
        var order_leftcol_receiver_patronymic_name = $(order_left_col + ' .receiver-patronymic-name');
        order_leftcol_receiver_patronymic_name.html('');
        $(form + ' [name="receiver_patronymic_name"]').closest('section').addClass('section_receiver_patronymic_name');
        $('.section_receiver_patronymic_name').appendTo(order_leftcol_receiver_patronymic_name);

        //receiver_phone
        var order_leftcol_receiver_phone = $(order_left_col + ' .receiver-phone');
        order_leftcol_receiver_phone.html('');
        $(form + ' [name="receiver_phone"]').closest('section').addClass('section_receiver_phone');
        $('.section_receiver_phone').appendTo(order_leftcol_receiver_phone);

        //User comment
        //comment
        var order_leftcol_user_comment = $(order_left_col + ' .user-comment');
        order_leftcol_user_comment.html('');
        $(form + ' [name="comment"]').closest('section').addClass('section_user_comment');
        $('.section_user_comment').appendTo(order_leftcol_user_comment);

        //-----Right col------
        var order_right_col = $(body_form + ' .order-right-col');
        //Delivery & Payment
        order_right_col.html('');
        $(form + ' [name="tracking_num"]').closest('fieldset').addClass('section_delivery_payment');
        $('.section_delivery_payment').appendTo(order_right_col);

        //Rightcol fieldset
        var order_right_col_fieldset = $(body_form + ' .order-right-col fieldset');
        //liqpayonline-block
        $('.liqpayonline-block').appendTo(order_right_col_fieldset);
        //monopayparts-block
        $('.monopayparts-block').appendTo(order_right_col_fieldset);
        //Delivery price
        $(form + ' [name="price_delivery"]').closest('section').addClass('section_price_delivery');
        $('.section_price_delivery').appendTo(order_right_col_fieldset);

        //is_delivery_paid_separately
        $(form + ' [name="is_delivery_paid_separately"]').closest('section').addClass('section_is_delivery_paid_separately');
        $('.section_is_delivery_paid_separately').appendTo(order_right_col_fieldset);
        //-------------------


        //Products
        var order_products = $(order_base + ' .order-products');
        order_products.html('');
        $(form + ' .definition_blocks.definition_tovary').closest('section').addClass('section_definition_tovary');
        $('.section_definition_tovary').appendTo(order_products);
        $('.section_definition_tovary label').remove();
        //Products button
        var order_products_button = $(order_base + ' .order-products-button');
        order_products_button.html('');
        $(form + ' .section_definition_tovary button').addClass('button_add_product_button');
        $('.button_add_product_button').appendTo(order_products_button);
        //Total fields
        var order_total_fields = $(order_base + ' .order-total-fields');
        order_total_fields.html('');
        $(form + ' [name="cost_without_sale"]').closest('section').addClass('col-lg-3 col-md-6 section_cost_without_sale');
        $('.section_cost_without_sale').appendTo(order_total_fields);
        $(form + ' [name="promo_code"]').closest('section').addClass('col-lg-3 col-md-6 section_promo_code');
        $('.section_promo_code').appendTo(order_total_fields);
        $(form + ' [name="sale_promo"]').closest('section').addClass('col-lg-3 col-md-6 section_sale_promo');
        $('.section_sale_promo').appendTo(order_total_fields);
        $(form + ' [name="sale_discount"]').closest('section').addClass('col-lg-3 col-md-6 section_sale_discount');
        $('.section_sale_discount').appendTo(order_total_fields);
        $(form + ' [name="tax"]').closest('section').addClass('col-lg-3 col-md-6 section_tax');
        $('.section_tax').appendTo(order_total_fields);
        //Total cost
        var order_total_cost_fields = $(order_base + ' .order-total-cost-field');
        order_total_cost_fields.html('');
        $(form + ' [name="cost"]').closest('section').addClass('col-lg-12 col-md-12 section_cost');
        $('.section_cost').appendTo(order_total_cost_fields);


        //hidden
        $(form + ' [name="sale"]').appendTo(order_total_fields);
        $(form + ' [name="id"]').appendTo(order_total_fields);
        $(form + ' [name="prom_id"]').appendTo(order_total_fields);

        //order-other-fields
        var order_other_fields = $(order_base + ' .order-other-fields');
        order_other_fields.html('');
        $(form + ' #note_for_manager').closest('section').addClass('section_other_fields');
        $('.section_other_fields').appendTo(order_other_fields);
        $(form + ' #system_notes').closest('section').addClass('section_system_notes');
        $('.section_system_notes').appendTo(order_other_fields);

        //Delete tabs
        //Delete user tabs
        //#tabformorders-0
        $('a[href="#tabformorders-0"]').parent().hide();
        $("#tabformorders-0").hide();
        //#tabformorders-1
        $('a[href="#tabformorders-1"]').parent().hide();
        $("#tabformorders-1").hide();
        //Receiver tab remove
        //#tabformorders-2
        //$('a[href="#tabformorders-2"]').parent().hide();
        //$("#tabformorders-2").hide();
        //Statuses tab remove
        //#tabformorders-3
        $('a[href="#tabformorders-3"]').parent().hide();
        $("#tabformorders-3").hide();
        //Delivery & Payment  tab remove
        //#tabformorders-4
        $('a[href="#tabformorders-4"]').parent().hide();
        $("#tabformorders-4").hide();
        //Receiver tab remove
        $('a[href="#tabformorders-5"]').parent().hide();
        $("#tabformorders-5").hide();

        $('a[href="#tabformorders-8"]').click();

        deliveryActions();
        receiverActions();
        cancelreasonAction();
    }

</script>

<style>
    fieldset {
        max-width: 1000px;
    }

    .jarviswidget > header > h2 {
        line-height: 44px;
    }

    .jarviswidget > header a {
        color: #fff;
    }

    .jarviswidget > header {
        height: 44px;
        line-height: 44px;
        margin-bottom: 1px;
    }

    .jarviswidget > header > .widget-icon {
        line-height: 44px;
    }

    .smart-form fieldset {
        margin: 0;
        max-width: 100%;
        padding: 13px 11px !important;
    }

    .smart-form input.input-sm {
        height: 32px;
    }

    .smart-form label.virtual {
        height: 44px;
    }

    .smart-form .without_label {
        padding-top: 25px;
    }

    .smart-form .btn {
        line-height: 22px;
        font-size: 13px;
        padding: 4px 10px;
    }

    .select2-container {
        height: 32px;
        border: 0;
    }

    .select2-container .select2-choice {
        border: 1px solid #bdbdbd;
        height: 30px;
        line-height: 30px;
    }

    .header_form, .body_form, .order_base {
        /*padding: 10px;*/
        margin: 0 0 15px 0 !important;
        /*padding: 13px 7px;*/
        border: 1px solid #ddd;
    }

    .header_form fieldset, .body_form fieldset, .order_base fieldset {
        padding-bottom: 5px !important;
    }

    .header_form [class*="col-"], .body_form [class*="col-"] {
        box-sizing: border-box;
    }

    .header_form #order-id {
        padding: 5px 5px;
        border: 1px solid #dddddd;
        line-height: 20px;
        color: #000;
        background-color: #eee;
        background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#f2f2f2), to(#fafafa));
        background-image: -webkit-linear-gradient(top, #f2f2f2 0, #fafafa 100%);
        background-image: -moz-linear-gradient(top, #f2f2f2 0, #fafafa 100%);
        background-image: -ms-linear-gradient(top, #f2f2f2 0, #fafafa 100%);
        background-image: -o-linear-gradient(top, #f2f2f2 0, #fafafa 100%);
        background-image: -linear-gradient(top, #f2f2f2 0, #fafafa 100%);
    }

    .header_form .order-process-1c {
        padding: 5px 5px;
        border: 1px solid #dddddd;
        line-height: 20px;
        color: #000;
        background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#f2f2f2), to(#fafafa));
        background-image: -webkit-linear-gradient(top, #f2f2f2 0, #fafafa 100%);
        background-image: -moz-linear-gradient(top, #f2f2f2 0, #fafafa 100%);
        background-image: -ms-linear-gradient(top, #f2f2f2 0, #fafafa 100%);
        background-image: -o-linear-gradient(top, #f2f2f2 0, #fafafa 100%);
        background-image: -linear-gradient(top, #f2f2f2 0, #fafafa 100%);
    }

    .header_form section, .body_form section, .order_base section {
        margin-bottom: 15px;
        padding-left: 7px;
        padding-right: 7px;
    }

    .order_base section.section_cost {
        float: right;
    }

    .order_base section.section_cost input {
        background-color: #ebfff0;
        border-color: #83b593;
    }

    .header_form section:last-child, .body_form section:last-child {
        margin-bottom: 15px;
    }

    .order_base .definition_blocks.definition_tovary {
        padding-top: 5px;
    }

    .order-total-fields section, .order-total-cost-field section {
        max-width: /*179*/205px;
    }

    .badge-orderid {
        font-size: 11px;
        color: #4c4f53;
    }

    div.smoke_lol i {
        position: absolute;
        top: 43vh;
        left: 50%;
        margin-top: -23px;
        margin-left: -23px;
        color: #2e6e0b;
    }

    @media (max-width: 1200px) {
        .smart-form .without_label {
            padding-top: 0;
        }
    }

    @media (min-width: 768px) and (max-width: 979px) {
        .order_base section.section_cost {
            float: none;
        }

        .order-total-fields section, .order-total-cost-field section {
            max-width: 100%;
        }
    }
</style>

