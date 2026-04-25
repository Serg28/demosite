<div>
    <div x-data="{ defaultValue: '{{$defaultValue}}', value: @entangle('value') }" class="select-searchable">
        <div wire:ignore :class="value ? '' : 'error'">
            <select class="select select2-cities" wire:model.live="value">
            </select>
        </div>

        @script
        <script>
            $(document).ready(function () {

                $(".select2-cities").select2({
                    placeholder: '{{$placeholder}}',
                    minimumInputLength: 1,
                    allowClear: true,
                    width: "100%",
                    ajax: {
                        url: window.lang + "checkout/search/cities",
                        dataType: "json",
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                                "content"
                            ),
                        },
                        quietMillis: 250,
                        method: "POST",
                        data: function (term) {
                            // page is the one-based page number tracked by Select2
                            return {
                                q: term.term, //search term
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
                            return objTranslationForSite["Введите город"];
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

                $('.select2-cities').on('change', function (e) {
                @this.select($(this).val(), $(this).val());
                @this.value = $(this).val();
                    //Livewire.dispatch('checkout-city-changed', {city_id: $(this).val()})
                    //Livewire.dispatch('checkout-set-property', {property: 'city_id', value : $(this).val()})
                });

                /*$(".select2-cities").on('select2:open',
                    function (e) {

                    }
                );
                $(".select2-cities").on('select2:closing',
                    function (e) {

                    }
                );*/

                //Сброс поля города
                $(".select2-cities").on(
                    "select2:unselecting",
                    function (e) {
                        Livewire.dispatch('checkout-set-property', {property: 'city_id', value : null})
                    }
                );
            });

        </script>
        @endscript

    </div>
</div>