<button class="btn btn-success btn-sm" onclick="createOrder(); return false;">{{__cms('Создать заказ')}}</button>

<script>
	function createOrder() {
	    window.location = '/admin/create-order-from-unfinished-basket/{{$field->getId()}}';
    }
</script>