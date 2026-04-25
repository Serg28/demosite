if (window.Echo) {
    Echo.private("orders-channel")
        .listen(".OrderCreated", (data) => {
            //Добавлен новый заказ - уведомляем
            if (typeof doAjaxLoadContent === 'function') {
                newOrderNotification(data);
            }
        })
        .listen(".OrderUpdated", (data) => {
            //Заказ обновлен - манипуляция данными и уведомления
            if (typeof updatedOrderNotification === 'function' && typeof orderId !== "undefined") {
                updatedOrderNotification(data, orderId)
            }
        });
}


document.addEventListener('DOMContentLoaded', function() {
    document.querySelector('[data-action="minifyMenu"]').addEventListener('click', function(e) {
        // Проверяем, содержит ли класс "minified" в body
        let isMinified = document.body.classList.contains('minified');

        // Инвертируем значение minified
        isMinified = !isMinified;

        // Обновляем значение minified в bodyClass
        const bodyClass = isMinified ? 'max' : 'minified';

        // Отправляем GET-запрос с использованием fetch
        fetch("/admin/left-sidebar/" + bodyClass, {
            method: 'GET'
        });

        e.preventDefault();
    });
});

