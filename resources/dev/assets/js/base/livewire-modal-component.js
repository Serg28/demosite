/**
 * @description Вызов модального компонента Livewire по клику на элемент с классом js-lw-modal
 *              По клику открывается модальное окно, сформированное пакетом
 *              https://github.com/wire-elements/modal
 *
 * @usage
 * 1. Добавьте класс 'js-lw-modal' к элементам (кнопкам, ссылкам), которые должны вызывать модальные окна.
 * 2. Установите атрибуты 'data-subject' и 'data-component' с соответствующими значениями для каждого элемента.
 * 3. Подключите этот скрипт к вашему HTML-документу.
 * 4. Убедитесь, что установлен пакет https://github.com/wire-elements/modal и существует компонент, который указан в data-component
 *
 * @example
 * <!-- Пример использования класса 'js-lw-modal' для кнопки -->
 * <button class="main-btn main-btn--red js-lw-modal"
 *         data-subject="Заказ звонка"
 *         data-component="recall-form"
 *         data-user-id="123"
 *         data-product-id="456">Тестовая кнопка</button>
 *
 * <!-- Пример использования класса 'js-lw-modal' для ссылки -->
 * <a href="#" class="custom-link js-lw-modal"
 *    data-subject="Тестовая форма Перезвонить"
 *    data-component="recall-form"
 *    data-user-id="123"
 *    data-product-id="456">Тестовая ссылка</a>
 *
 * @param {String} data-subject - Тема или название формы, передаваемая в модальное окно и далее в компонент как свойство subject.
 * @param {String} data-component - Компонент Livewire, который будет вызван при открытии модального окна.
 * @param {String} data-block - Текстовое название блока, в котором находится компонент, которое добавляется к свойству subject компонента при сохранении в компоненте.
 * @param {Object} data-* - Дополнительные аргументы, передаваемые в компонент.
 *
 */
var tmrLvModal = null;

function lvModal(){
    // Находим все элементы с классом 'js-lw-modal'
    const modalElements = document.querySelectorAll('.js-lw-modal');
    // Добавляем обработчик события клика на каждый элемент
    modalElements.forEach(function(element) {
        element.addEventListener('click', handleModalClick);
    });
}
// Обработчик события клика для элементов с классом 'js-lw-modal'
function handleModalClick(event) {
    event.preventDefault();

    const subject = this.getAttribute('data-subject');
    const component = this.getAttribute('data-component');
    const block = this.getAttribute('data-block');
    const args = {};

    // Перебираем все data-атрибуты элемента
    [].forEach.call(this.attributes, function(attr) {
        if (attr.name.startsWith('data-') && (attr.name !== 'data-component' && attr.name !== 'data-block')) {
            const key = attr.name.substring(5); // Убираем префикс 'data-'
            args[key] = attr.value;
        }
    });

    executeFunction(subject, component, block, args);
}

// Общая функция для выполнения действия
function executeFunction(subject, component, block, dataArgs) {
    const args = {
        subject: subject,
        block: block,
        ...dataArgs
    };

    if (window.Livewire && window.Livewire.dispatch) {
        window.Livewire.dispatch('openModal', { component: component, arguments: args });
    } else {
        console.error("Livewire or Livewire.dispatch is not defined.");
    }
}

document.addEventListener('livewire:navigated', () => {
    lvModal();
});

Livewire.hook('morph.updated',  ({ el, component, toEl, skip, childrenOnly }) => {
    //if (typeof initLazy === 'function') {
    clearTimeout(tmrLvModal);
    tmrLvModal = setTimeout(function (){ lvModal(); },100);
    //}
});