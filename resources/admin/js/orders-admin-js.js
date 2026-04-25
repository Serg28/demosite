/**
 * Обновляет список заказов, показывает всплывающее уведомление и воспроизводит звук при новом заказе в админке.
 *
 * @function
 * @name newOrderNotification
 * @param {object} createdDataOrder - Объект с данными о новом заказе.
 * @returns {void}
 * @remarks
 * Функция использует звуковой файл 'bigbox.mp3' для уведомлений и требует поддержки HTML5 Audio.
 * Всплывающее уведомление отображает ID нового заказа.
 *
 * @example
 * // Пример использования функции newOrderNotification()
 * const createdDataOrder = { model: { id: 123, is_online_payed: 1 } };
 * newOrderNotification(createdDataOrder);
 */
function newOrderNotification(createdDataOrder) {
    if (typeof doAjaxLoadContent === "function") {

        //Перезагружаем список заказов
        if(location.pathname==='/admin/orders') {
            doAjaxLoadContent(location.href);
        }

        //Всплывающее уведомление
        TableBuilder.showSuccessNotification(
            phrase["Новый заказ"] + " " + createdDataOrder.model.id
        );

        //Звуковое уведомление
        var PlaySound = 1;
        if (isIE8orlower() == 0) {
            var audioElement = document.createElement("audio");
            audioElement.setAttribute("src", $.sound_path + "bigbox.mp3");
            $.get();
            audioElement.addEventListener(
                "load",
                function () {
                    audioElement.play();
                },
                true
            );

            audioElement.pause();
            audioElement.play();
        }
    }
}

/**
 * Уведомляет об изменениях в заказе, обновляет статус оплаты и предлагает перезагрузку страницы заказа при необходимости.
 *
 * @function
 * @name updatedOrderNotification
 * @param {object} updatedDataOrder - Объект с данными об обновленном заказе.
 * @param {string} currentOrderId - Номер текущего редактируемого заказа.
 * @returns {void}
 * @remarks
 * Функция проверяет изменения в статусе оплаты и, если необходимо, предлагает перезагрузку страницы заказа.
 *
 * @example
 * // Пример использования функции updatedOrderNotification
 * const updatedDataOrder = { model: { is_online_payed: 1 } };
 * const orderId = '12345';
 * updatedOrderNotification(updatedDataOrder, currentOrderId);
 */
function updatedOrderNotification(updatedDataOrder, currentOrderId) {
    if (typeof currentOrderId !== "undefined" && currentOrderId && updatedDataOrder.model.id === currentOrderId) {
        //Если в странице конкретного заказа
        const is_online_payed = updatedDataOrder.model.is_online_payed || 0;
        const inputPayStatus = $('[name="is_online_payed"]');
        const currentPayStatus = inputPayStatus.val();

        if (parseInt(currentPayStatus) !== parseInt(is_online_payed)) {
            inputPayStatus.val(is_online_payed).change();

            if (!$(".divMessageBox").length) {
                jQuery.SmartMessageBox(
                    {
                        title: phrase["Внимание"],
                        content:
                            phrase["У заказа изменился статус оплаты. Что делаем?"],
                        buttons:
                            "[" +
                            phrase["Закрыть"] +
                            "][" +
                            phrase["Перезагрузить заказ"] +
                            "]",
                    },
                    function (ButtonPressed) {
                        if (ButtonPressed === phrase["Перезагрузить заказ"]) {
                            if (typeof reloadOrder === "function" && currentOrderId) {
                                reloadOrder(currentOrderId);
                            }
                        }
                    }
                );
            }
        }
    }
}
