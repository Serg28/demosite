@if($cityId !== null)
    @php $warehouseUrl = route('api.v1.checkout.warehouses') . '?delivery_slug=' . $deliverySlug . '&city_id=' . $cityId; @endphp
    <x-checkout.autocomplete
        :label="__t($this->warehouseLabel)"
        :placeholder="__t('Введіть номер або адресу...')"
        :search-url="$warehouseUrl"
        :selected-id="$selectedWarehouseId"
        :selected-title="$selectedWarehouseTitle"
        select-method="selectWarehouse"
        clear-method="clearWarehouse"
        :min-chars="1"
    />
@endif
