/**
 * Phone Input Autofill
 *
 * Этот скрипт автоматически заполняет поле ввода телефона префиксом,
 * который берется из первых нескольких символов плейсхолдера при фокусе на поле,
 * если оно пустое. Также очищает поле при потере фокуса, если в нем остался только этот префикс.
 *
 * Примечание: Маска ввода телефона предполагается использоваться через Alpine.js с атрибутом `x-mask`.
 *
 * @function setupPhoneInputAutofill
 * @param {string} [inputSelector='input[type="tel"]'] - Селектор поля ввода телефона.
 * @param {string} [placeholderPrefix='+38(0'] - Префикс для заполнения поля при фокусе.
 *
 * @example
 * // Инициализация автозаполнения для поля ввода телефона
 * setupPhoneInputAutofill('input[type="tel"]', '+38(0');
 *
 * @example
 * // Автозаполнение с использованием стандартного селектора и префикса
 * setupPhoneInputAutofill();
 */

document.addEventListener('DOMContentLoaded', () => {
    /**
     * Инициализация автозаполнения для ввода телефона.
     * Заполняет поле при фокусе префиксом, взятым из первых символов плейсхолдера,
     * и очищает поле при потере фокуса, если в нем остался только этот префикс.
     *
     * @param {string} [inputSelector='input[type="tel"]'] - Селектор для поиска поля ввода телефона.
     * @param {string} [placeholderPrefix='+38(0'] - Префикс для заполнения при фокусе.
     */
    function setupPhoneInputAutofill(inputSelector = 'input[type="tel"]', placeholderPrefix = '+38(0') {
        const phoneInput = document.querySelector(inputSelector);

        // Проверяем существование элемента
        if (!phoneInput) {
            console.warn(`Элемент с селектором "${inputSelector}" не найден.`);
            return; // Прекращаем выполнение, если элемент не найден
        }

        // Обработчик фокуса
        phoneInput.addEventListener('focus', (event) => {
            const input = event.target;
            if (input.value === '') {
                input.value = placeholderPrefix;
                input.setSelectionRange(5, 5);
            }
        });

        // Обработчик потери фокуса
        phoneInput.addEventListener('blur', (event) => {
            const input = event.target;
            if (input.value === placeholderPrefix) {
                input.value = '';
            }
        });
    }

    // Инициализация скрипта для всех полей с type="tel"
    setupPhoneInputAutofill();
});