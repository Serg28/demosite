class CompareHandler {
    static config = {
        activeClass: 'active',
        buttonClass: 'compare-button',
        iconSelector: '.compare-icon',
        spinnerSelector: '.spinner',
        activeIcon: '/assets/images/compare-active.svg',
        inactiveIcon: '/assets/images/compare-gray.svg'
    };

    constructor(csrfToken) {
        this.csrfToken = csrfToken;
    }

    init() {
        document.querySelectorAll(`.${CompareHandler.config.buttonClass}`).forEach(btn => {
            btn.addEventListener('click', (event) => this.handleCompareClick(event));
        });
    }

    handleCompareClick(event) {
        const btn = event.currentTarget;
        const id = btn.dataset.id;
        const isActive = btn.classList.contains(CompareHandler.config.activeClass);
        const lang = window.lang;
        const url = `${lang}compare/${isActive ? 'delete' : 'add'}/${id}`;

        const icon = btn.querySelector(CompareHandler.config.iconSelector);
        const spinner = btn.querySelector(CompareHandler.config.spinnerSelector);

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
                // Меняем состояние кнопки
                //btn.classList.toggle(CompareHandler.config.activeClass);

                // Меняем иконку в зависимости от состояния
                if (btn.classList.contains(CompareHandler.config.activeClass)) {
                    icon.src = CompareHandler.config.activeIcon;
                } else {
                    icon.src = CompareHandler.config.inactiveIcon;
                }

                // Скрываем спиннер и показываем иконку после завершения запроса
                icon.style.display = 'inline-block';
                spinner.style.display = 'none';

                // Обновляем счетчик товаров в сравнении через Livewire
                Livewire.dispatch('updateCompareCount');

                // Отображение уведомления
                Livewire.dispatch('notify', { message: data.message, title: data.title, type: 'success' });
            })
            .catch(error => {
                // В случае ошибки скрываем спиннер и показываем иконку
                icon.style.display = 'inline-block';
                spinner.style.display = 'none';

                Livewire.dispatch('notify', { message: error.message, title: error.title, type: 'error' });
            });
    }
}

// Инициализация обработчика после загрузки страницы
document.addEventListener('DOMContentLoaded', function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    const compareHandler = new CompareHandler(csrfToken);
    compareHandler.init();
});
