/**
 * @file livewire-modal.js
 *
 * Виклик Livewire-модального компонента через data-атрибути.
 * Замінює wire-elements/modal — використовує власний ModalManager (Livewire 4 native).
 *
 * @version 3.0 — адаптовано для demo.loc (data-js-modal замість .js-lw-modal)
 * @author  Serg28 tsv.art.com@gmail.com for Linecore
 * @license MIT
 *
 * == Як замінює wire-elements/modal ==
 * wire-elements/modal: <livewire:modal /> у layout + dispatch('openModal', ...)
 * Наш варіант:         <livewire:components.modal-manager /> у layout + той самий dispatch
 * JS-інтерфейс повністю ідентичний — migration = тільки замінити livewire:modal на
 * livewire:components.modal-manager у shop.blade.php.
 *
 * == Використання в Blade ==
 *   <button data-js-modal
 *           data-component="forms.recall"
 *           data-subject="Зворотній дзвінок">
 *     Зателефонуйте мені
 *   </button>
 *
 * == Програмний виклик ==
 *   Livewire.dispatch('openModal', { component: 'forms.recall', arguments: { subject: 'Дзвінок' } })
 *   Livewire.dispatch('closeModal')
 *
 * == Параметри data-атрибутів ==
 * @param {string} data-component  — ім'я Livewire-компонента (обов'язково)
 * @param {*}      data-*          — всі інші data-* передаються як arguments у компонент
 */

(function () {
    'use strict';

    let isInitialized = false;

    function handleClick(event) {
        const trigger = event.target.closest('[data-js-modal]');
        if (!trigger) return;

        event.preventDefault();

        const component = trigger.getAttribute('data-component');
        if (!component) {
            console.warn('[livewire-modal] Не вказано data-component');
            return;
        }

        const args = collectArgs(trigger);
        openModal(component, args);
    }

    function collectArgs(element) {
        const args = {};
        for (const attr of element.attributes) {
            if (attr.name.startsWith('data-') && attr.name !== 'data-component' && attr.name !== 'data-js-modal') {
                args[attr.name.slice(5)] = attr.value;
            }
        }
        return args;
    }

    function openModal(component, args) {
        if (window.Livewire?.dispatch) {
            window.Livewire.dispatch('openModal', { component, arguments: args });
        } else {
            console.error('[livewire-modal] Livewire не знайдено');
        }
    }

    function init() {
        if (isInitialized) return;
        document.addEventListener('click', handleClick);
        isInitialized = true;
    }

    document.addEventListener('livewire:navigated', init);
    init();
})();
