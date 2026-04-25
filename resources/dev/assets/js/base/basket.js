//document.addEventListener('DOMContentLoaded', function () {
document.addEventListener('livewire:navigated', function() {  //При использовании livewire

    //Пришло событие перезагрузить текущую страницу
    Livewire.on('reload-current-page', () => {
        setTimeout(() => {
            window.location.href = window.location.href;
        }, 1000);
    });

    // Добавление в корзину
    /*Livewire.on('cart-product-added', (event) => {
        // Проверяем, существует ли event.response.original, иначе используем event
        let response = event.response?.original || event.event;
        if (response.status === 'success') {
            analytic(response);
        }

        Livewire.dispatch('openModal', {component: 'cart.content'});
    });

    // Изменение кол-ва товара в корзине
    Livewire.on('cart-product-updated', (event) => {
        // Проверяем, существует ли event.response.original, иначе используем event
        let response = event.response?.original || event.event;
        if (response.status === 'success') {
            analytic(response);
        }
    });

    // Удаление товара из корзины
    Livewire.on('cart-product-deleted', (event) => {
        // Проверяем, существует ли event.response.original, иначе используем event
        let response = event.response?.original || event.event;
        if (response.status === 'success') {
            analytic(response);
        }
    });*/

    //Изменение в корзине: добавление, удаление или изменение кол-ва
    Livewire.on('cart-changed', (event) => {
        // Проверяем, существует ли event.response.original, иначе используем event
        let response = event.response?.original || event.event;

        // Проверяем наличие response.status и выполняем действие
        if (response?.status === 'success') {
            analytic(response);
        }

        // Проверяем наличие response.action и выполняем действие
        if (response?.action === 'add') {
            Livewire.dispatch('openModal', {component: 'cart.content'});
        }
    });
});


function analytic(data) {
    console.log(data);
    if (data.products && typeof dataLayer !== 'undefined') {
        //GA4
        //data.product.item_list_name = $(self).closest('.ga4_item_list_name').data('item_list_name');
        dataLayer.push({ecommerce: null});
        dataLayer.push({
            event: data.action === "add" ? "add_to_cart" : "remove_from_cart",
            ecommerce: {
                items: data.products,
            },
        });
        //
    }
}

class CartHandler {
    static config = {
        activeClass: 'active',
        buttonClass: 'btn-addtocart', // Изменил класс кнопки для соответствия
        iconSelector: '.icon img',
        spinnerSelector: '.spinner',
    };

    constructor(csrfToken) {
        this.csrfToken = csrfToken;
    }

    init() {
        // Используем делегирование событий через общий контейнер (например, document или конкретный контейнер)
        document.addEventListener('click', (event) => {
            const btn = event.target.closest(`.${CartHandler.config.buttonClass}`);
            if (btn) {
                this.handleCartClick(event, btn);
            }
        });
    }

    handleCartClick(event, btn) {
        event.preventDefault();
        const id = btn.dataset.id; // Получаем ID товара
        const options = btn.dataset.options; // Получаем ID товара
        const price = btn.dataset.price; // Получаем цену товара
        const lang = window.lang;
        const url = `${lang}basket/add/${id}`; // URL для добавления в корзину

        const icon = btn.querySelector(CartHandler.config.iconSelector);
        const spinner = btn.querySelector(CartHandler.config.spinnerSelector);

        // Скрываем иконку и показываем спиннер
        //icon.style.display = 'none';
        if (spinner) {
            spinner.style.display = 'block';
        }

        // Отправляем запрос на добавление товара в корзину с количеством 1
        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': this.csrfToken,
                'Content-Type': 'application/json', // Указываем, что отправляем JSON
            },
            body: JSON.stringify({ options: options, p: price }) // Отправляем пустые опции, если не нужно передавать дополнительные данные
        })
            .then(response => response.json())
            .then(data => {
                // Скрываем спиннер и показываем иконку
                if (spinner) {
                    spinner.style.display = 'none';
                }
                //icon.style.display = 'block';
                // Проверяем ответ
                if (data.status === 'success' || data.status === true) {

                    //Livewire.dispatch('cart-product-added', {event: data});
                    // Обновляем счетчик товаров в корзине через Livewire
                    Livewire.dispatch('cart-changed', {event: data});
                } else {
                    // Обработка ошибки
                    Livewire.dispatch('notify', { message: data.message, title: '', type: 'error' });
                }
            })
            .catch(error => {
                // В случае ошибки скрываем спиннер и показываем иконку
                if (spinner) {
                    spinner.style.display = 'none';
                }
                //icon.style.display = 'block';

                Livewire.dispatch('notify', { message: error.message, title: 'Помилка', type: 'error' });
            });
    }
}

// Инициализация обработчика после загрузки страницы
document.addEventListener('DOMContentLoaded', function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    const cartHandler = new CartHandler(csrfToken);
    cartHandler.init();
});

