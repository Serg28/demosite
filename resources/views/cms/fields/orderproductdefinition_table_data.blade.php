<div class="loader_definition"><i class="fa fa-gear fa-4x fa-spin"></i></div>
<table class="table table-hover table-bordered">
	<thead>
	<tr>
		<td class="col_sort"></td>
		@foreach($fieldsDefinition as $field)
			<th>{{$field->getName()}}</th>
		@endforeach
		<th style="width: 10%"></th>
	</tr>
	</thead>
	<tbody>
	@forelse($list as $record)
		<tr data-id="{{$record->id}}" id="sort-{{$record->id}}">
			<td class="handle col_sort"><i class="fa fa-sort"></i></td>
			@foreach($record->fields as $field)
				<td>{!! $field->value !!}</td>
			@endforeach

			<td>
                @if($order->order_status_id!==7) {{-- Запрещаем для завершеного заказа --}}
				<div class="btn-group hidden-phone pull-right">
					<a class="btn dropdown-toggle btn-default"  data-toggle="dropdown"><i class="fa fa-cog"></i> <i class="fa fa-caret-down"></i></a>
					<ul class="dropdown-menu">
                        <li><a onclick="orderButton($(this),'{{request('id')}}','{{$record->product_id}}')"><i class="fa fa-shopping-basket"></i> {{__cms("Заказать")}}</a></li>
						<li><a onclick="ForeignDefinition.edit({{$record->id}}, '{{request('id')}}', '{{$attributes}}', 'actions')" class="edit"><i class="fa fa-pencil"></i> {{__cms('Редактировать')}}</a></li>
						{{-- <li><a onclick="ForeignDefinition.clone({{$record->id}}, '{{request('id')}}', '{{$attributes}}', 'actions')"><i class="fa fa-copy"></i> {{__cms('Клонировать')}}</a></li>--}}
						<li><a onclick="ForeignDefinition.delete({{$record->id}}, '{{request('id')}}', '{{$attributes}}', '/admin/actions/orders')"><i class="fa red fa-times"></i> {{__cms('Удалить')}}</a></li>
					</ul>
				</div>
                @endif
			</td>
		</tr>
	@empty
		<tr><td colspan="{{count ($fieldsDefinition) + 1 }}"> {{__cms('Пока пусто')}} </td></tr>
	@endforelse
	</tbody>
</table>

@if($order)

    <script>

        {{-- Сохранение всего заказа при клике на сохранение товаров заказа --}}
        $('.dropdown-menu a.edit, .button_add_product_button').unbind('click').click(function(){
            const saveOrderProductsSelector = '.modal_form_order_products .modal-footer .btn-success, .modal_form_order_products .modal-header .btn-success';
            $(document).off('click', saveOrderProductsSelector).on('click', saveOrderProductsSelector, function () {
                if (typeof onOrderProductSaveEvent == 'function') {
                    window.dirty = true;
                    onOrderProductSaveEvent();
                }
            });
        });
        {{-- /Сохранение всего заказа при клике на сохранение товаров заказа --}}

        //Запуск сохранения всего заказа
        function onOrderProductSaveEvent() {
            setTimeout(function () {
                if (typeof window.saveOrder === 'function') {
                    window.saveOrder({{$order->id}});
                } else {
                    $('#edit_form_orders').submit();
                    TableBuilder.getEditForm({{$order->id}}, $(this));
                }
            }, 1000);
        }

        //Открывает модальное окно с формой заказа товара у поставщика
        function orderButton(id,order,product) {
            ForeignDefinition.createDefinition(id, 'warehouse_order_product', '{"product":"'+product+'","name":"tovary","table":"warehouse_order_product","caption":"warehouse_order_product","definition":"warehouse_order_product","definition_parent":"orders","ident":"tovary","foreign_field":"order_id","path_definition":"App\\\\Cms\\\\Definitions\\\\QuickWarehouseOrderProduct","model_parent":"App\\\\Cms\\\\Definitions\\\\Orders","type_relation":"hasMany"}', order)
        }

        //При blur у полей с суммами пересчитываем все суммы
        $('[name=sale],[name=sale_promo],[name=sale_discount],[name=tax]').blur(function () {
            generateCostOrder();
        });

        //Логика пересчета итоговых сумм у заказа
        function generateCostOrder() {
            var costWithoutSale = '{{$order ? $order->getAllCost() : 0}}';
            var allCost = '{{$order ? $order->getAllCost() : 0}}';
            var cost = '{{$order ? $order->getTotalCost() : 0}}';
            var sale = $('[name=sale]').val() * 1;
            var sale_promo = $('[name=sale_promo]').val() * 1;
            var sale_discount = $('[name=sale_discount]').val() * 1;
            var tax = {{$order ? $order->tax : 0}};

            var priceSale = 0;
            var sale = 0;

            $('[name=cost_without_sale]').val(allCost);

            if (sale_promo || sale_discount) {
                sale = sale_promo+sale_discount;
            }

            $('[name=sale]').val(sale);
            $('[name=cost]').val(Math.round(cost) + tax);
        }

        generateCostOrder();
    </script>

@endif
