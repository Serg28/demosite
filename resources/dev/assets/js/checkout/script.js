document.addEventListener('DOMContentLoaded', function () {

    //Применение промокода
    Livewire.on('checkout-promocode-apply-result', (event) => {
        console.log(event.response);
        let response = event.response.original || {};

        let modalClass = response.status === 'success' ? 'success' : 'info';
        //Livewire.dispatch('openModal', {
        //    component: 'ModalBlock',
        //    arguments: {title: 'Промокод', text: response.message || '', class: modalClass}
        //});
    });

    //Функционал быстрого выбора города - только визуальная перерисовка города в селекте города choices.js
    document.getElementById('fastCities').addEventListener('click', function(event) {
        // Проверяем, что клик был на ссылке с классом 'city-link'
        if (event.target.classList.contains('city')) {
            event.preventDefault(); // Отменяем переход по ссылке

            // Получаем значение data-city
            const cityId = event.target.getAttribute('data-city');

            if (window.choicesCities && cityId) {
                // Устанавливаем значение в Choices.js
                window.choicesCities.selectValue([cityId]);
            } else {
                //console.error('choicesCities не инициализирован или cityId не найден');
            }
        }
    });

    function scrollToElement(selector) {
        const element = document.querySelector(selector);
        if (element) {
            element.scrollIntoView({ behavior: 'smooth' });  // Плавная прокрутка
        } else {
            console.error(`Элемент с селектором "${selector}" не найден`);
        }
    }

    Livewire.on('checkout-validate-step', (event) => {
        setTimeout(function () {
            const stepId = `checkout-step-${event.id}`;
            const container = document.getElementById(stepId); // Контейнер шага
            let targetElement;

            // Добавляем класс form-check к контейнеру, если он найден
            if (container) {
                container.classList.add('form-check');
            }

            // Ищем первый элемент с ошибкой или require-empty внутри текущего контейнера
            const errorElements = container ? container.querySelectorAll('.error, .require-empty') : [];
            if (errorElements.length > 0) {
                targetElement = container; // Если ошибки есть, скроллим к контейнеру
            } else {
                // Если ошибок нет, ищем следующий шаг
                const nextStepId = `checkout-step-${event.id + 1}`;
                const nextContainer = document.getElementById(nextStepId);
                targetElement = nextContainer ? nextContainer : container; // Прокручиваем к следующему шагу, если он существует, иначе к текущему
                console.log(nextStepId);
            }

            // Если есть цель для прокрутки, прокручиваем к ней
            if (targetElement) {
                // Получаем высоту <header> (если он существует)
                const header = document.querySelector('header');
                const headerHeight = header ? header.offsetHeight : 0;

                // Добавляем отступ в 15px
                const yOffset = -(headerHeight + 15); // Отступ, равный высоте header + 15px
                const yPosition = targetElement.getBoundingClientRect().top + window.pageYOffset + yOffset;

                // Прокрутка с учетом высоты <header> и добавленного отступа
                window.scrollTo({ top: yPosition, behavior: 'smooth' });
            } else {
                console.warn('Элемент для прокрутки не найден');
            }

            // Если ошибок нет и аналитическая функция существует, вызываем её
            if (errorElements.length === 0) {
                if (typeof sendAnaliticCheckout === 'function') {
                    sendAnaliticCheckout(event.id);
                }
            }
        }, 100);
    });

});