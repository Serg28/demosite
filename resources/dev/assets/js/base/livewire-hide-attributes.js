/**
 * Этот JavaScript модуль предоставляет функционал для удаления указанных ниже атрибутов у элементов компонентов Livewire.
 * Он определяет массив атрибутов для удаления (lwAttrs) и функцию (lwHideAttr) для удаления этих атрибутов
 * из элементов с классами 'lwh'.
 */

// Массив атрибутов для удаления
const lwAttrs = [
    'snapshot',
    'effects',
    // 'id'
];

const lwHideAttr = () => {
    document.querySelectorAll('div, span').forEach(element => {
        lwAttrs.forEach(attr => {
            if (element.getAttribute(`wire:${attr}`) !== null) {
                element.removeAttribute(`wire:${attr}`);
            }
        });
    });
};

// Вызов функции snap() после загрузки документа
//document.addEventListener('livewire:navigated', function() {
window.addEventListener('load',(ev) =>{
    lwHideAttr();
});