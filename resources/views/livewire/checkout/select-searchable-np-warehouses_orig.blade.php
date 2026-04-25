<div>
    @if($cityId)
        <div x-data="{ city_id: @entangle('cityId'), value: @entangle('value'), error: @entangle('show')}" class="select-searchable">
            <div wire:ignore :class="value ? '' : 'error'">
                <select class="select select2-np-warehouses" wire:model.live="value">
                </select>
            </div>
            @script
            <script>
                $(document).ready(function () {
                    let cityId = $wire.cityId;
                    let select2_np_warehouses = $('.select2-np-warehouses').select2({
                        placeholder: '{{$placeholder}}',
                        ajax: {
                            url: window.lang + "checkout/delivery/pointers-np",
                            dataType: "json",
                            headers: {
                                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                                    "content"
                                ),
                            },
                            quietMillis: 250,
                            method: "POST",
                            data: function (term) {
                                return {
                                    q: term.term, //search term
                                    city: cityId
                                };
                            },
                            processResults: function (data) {
                                return {
                                    results: $.map(data, function (item) {
                                        return {
                                            text: item.title,
                                            id: item.id,
                                        };
                                    }),
                                };
                            },
                        },
                        language: {
                            inputTooShort: function () {
                                return objTranslationForSite["Введите отделение"];
                            },
                            errorLoading: function () {
                                return objTranslationForSite[
                                    "По запросу ничего не найдено"
                                    ];
                            },
                            noResults: function () {
                                return objTranslationForSite[
                                    "По запросу ничего не найдено"
                                    ];
                            },
                            searching: function () {
                                return objTranslationForSite["Идет поиск..."];
                            },
                        },
                    });

                    Livewire.on('checkout-city-changed', (event) => {
                        cityId = event.city_id;
                        select2_np_warehouses.html('').data('select2').$results.children().remove();
                    });

                    select2_np_warehouses.on('select2:select', function (e) {
                        @this.select($(this).val(), $(this).val());
                        @this.value = $(this).val();
                    });

                });

            </script>
            @endscript


        </div>
    @endif
</div>
