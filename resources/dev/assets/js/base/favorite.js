/**
 * Класс для управления добавлением товаров в избранное и проверки их статуса.
 */
class FavoriteHandler {
    static config = {
        activeClass: 'active',               // Класс, обозначающий добавление в избранное
        buttonClass: 'favorite-button',      // Класс кнопки избранного
        iconSelector: 'img',                 // Селектор иконки избранного
        spinnerSelector: '.spinner',         // Селектор индикатора загрузки
        activeIcon: '/assets/images/heart-active.svg',   // Путь к активной иконке
        inactiveIcon: '/assets/images/heart-gray.svg'    // Путь к неактивной иконке
    };

    /**
     * @param {string} csrfToken - CSRF-токен для защиты запросов.
     */
    constructor(csrfToken) {
        this.csrfToken = csrfToken;
        this.init();  // Инициализация обработчиков при создании экземпляра
        this.checkFavoriteStatus();  // Проверка статуса избранного
    }

    /**
     * Инициализация обработчиков клика на кнопках избранного.
     * Устанавливает делегирование событий для обработки кликов.
     */
    init() {
        document.addEventListener('click', (event) => {
            const btn = event.target.closest(`.${FavoriteHandler.config.buttonClass}`);
            if (btn) {
                this.handleFavoriteClick(event, btn);
            }
        });
    }

    /**
     * Проверка статуса избранного для товаров на странице.
     * Отправляет запрос на сервер для получения статуса и обновляет иконки избранного.
     */
    /**
     * Проверка статуса избранного для товаров на странице.
     * Отправляет запрос на сервер для получения статуса и обновляет иконки избранного.
     */
    checkFavoriteStatus() {
        const productIds = Array.from(document.querySelectorAll(`.${FavoriteHandler.config.buttonClass}`))
            .map(button => button.dataset.id);

        if (!productIds.length) return;

        fetch(window.lang + 'like/status', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': this.csrfToken
            },
            body: JSON.stringify({ productIds })
        })
            .then(response => response.json())
            .then(data => {
                if (data.favorites) { // Проверяем, если есть ключ favorites
                    // Сначала убираем класс active у всех кнопок
                    const allButtons = document.querySelectorAll(`.${FavoriteHandler.config.buttonClass}`);
                    allButtons.forEach(btn => {
                        btn.classList.remove(FavoriteHandler.config.activeClass);
                        const icon = btn.querySelector(FavoriteHandler.config.iconSelector);
                        icon.src = FavoriteHandler.config.inactiveIcon; // Устанавливаем иконку неактивной
                    });

                    // Теперь добавляем класс active только к избранным
                    data.favorites.forEach(productId => {
                        const buttons = document.querySelectorAll(`.${FavoriteHandler.config.buttonClass}[data-id="${productId}"]`);

                        buttons.forEach(btn => {
                            // Проставляем класс active для избранных товаров
                            btn.classList.add(FavoriteHandler.config.activeClass);

                            const icon = btn.querySelector(FavoriteHandler.config.iconSelector);
                            icon.src = FavoriteHandler.config.activeIcon; // Обновляем иконку на активную
                        });
                    });
                }
            })
            .catch(error => {
                //console.error('Error checking favorite status:', error);
            });
    }

    /**
     * Обработка клика на кнопку избранного.
     * Отправляет запрос на добавление или удаление товара из избранного и обновляет иконку.
     *
     * @param {Event} event - Событие клика.
     * @param {HTMLElement} btn - Кнопка, на которую кликнули.
     */
    handleFavoriteClick(event, btn) {
        event.preventDefault();
        const id = btn.dataset.id;  // Получаем ID товара
        const lang = window.lang;
        const url = `${lang}like/toggle/${id}`;  // URL для добавления/удаления избранного

        const icon = btn.querySelector(FavoriteHandler.config.iconSelector);
        const spinner = btn.querySelector(FavoriteHandler.config.spinnerSelector);

        // Скрываем иконку и показываем спиннер
        icon.style.display = 'none';
        spinner.style.display = 'inline-block';

        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': this.csrfToken
            }
        })
            .then(response => response.json())
            .then(data => {
                // Получаем все кнопки с одинаковым ID
                const buttons = document.querySelectorAll(`.${FavoriteHandler.config.buttonClass}[data-id="${id}"]`);

                buttons.forEach(button => {
                    // Обновляем статус кнопки
                    button.classList.toggle(FavoriteHandler.config.activeClass, data.isActive);

                    // Обновляем иконку
                    const buttonIcon = button.querySelector(FavoriteHandler.config.iconSelector);
                    buttonIcon.src = data.isActive
                        ? FavoriteHandler.config.activeIcon
                        : FavoriteHandler.config.inactiveIcon;
                });

                // Скрываем спиннер и показываем иконку после завершения запроса
                icon.style.display = 'inline-block';
                spinner.style.display = 'none';

                // Обновляем счетчик избранного через Livewire
                Livewire.dispatch('updateFavoriteCount');

                // Отображение уведомления
                Livewire.dispatch('notify', { message: data.message, title: data.title, type: data.status });
            })
            .catch(error => {
                // В случае ошибки скрываем спиннер и показываем иконку
                icon.style.display = 'inline-block';
                spinner.style.display = 'none';

                Livewire.dispatch('notify', { message: error.message, title: 'Ошибка', type: 'error' });
            });
    }
}

// Инициализация обработчика после загрузки страницы
document.addEventListener('DOMContentLoaded', function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    new FavoriteHandler(csrfToken);
});
