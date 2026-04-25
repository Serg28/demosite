/**
 * Инициализация выпадающего списка на основе Choices-js с AJAX-поиском.
 * @param {string} url - URL для AJAX-запроса.
 * @param {string} elementClass - Класс элемента, к которому применить скрипт.
 * @param {Object} options - Дополнительные настройки для Choices.js.
 * @param {Object} customTexts - Пользовательские тексты перевода.
 * @param {Object} customRequestBody - Дополнительные параметры для тела запроса.
 */
// Документация к Choices-js: https://github.com/Choices-js/Choices
/*
Формат данных, которые должны возвращаться по запросу AJAX:
[
    {id: 8662, title: "Кіндратівка", value: 8662, label: "Кіндратівка"}
    {id: 8662, title: "Кіндратівка", value: 8662, label: "Кіндратівка"}
]
Формат JSON, метод запроса - POST
По-умолчанию для формирования списка для значения используется поле id, для текста - title
Но можно переопределит это в коде ниже в функции loadChoices, код: choices.setChoices(data, 'id', 'title', true);
* */
// Пример использования:
/*
const customTexts = {
    searchPlaceholder: 'Custom Search Placeholder',
    placeholder: 'Custom Placeholder',
    loadingText: 'Custom Loading Text',
    noResultsText: 'Custom No Results Text',
    noChoicesText: 'Custom No Choices Text'
};

const customRequestBody = {
    city: $wire.cityId,  // Замените на фактический способ получения cityId
    customParam: 'customValue'  // Пример дополнительного параметра
};

SelectSearchable('/checkout/search/cities', 'select2-cities', {}, customTexts, customRequestBody);
*/
function SelectSearchable(url, elementClass, options = {}, customTexts = {}, customRequestBody = {}) {
    if (typeof Choices !== 'function') {
        console.error('Choices.js library is required for this script. Please include it in your HTML.');
        return;
    }

    const els = document.querySelector(`.${elementClass}`);

    if (!els) {
        console.error(`Element with class '${elementClass}' not found.`);
        return;
    }

    const defaultOptions = {
        searchPlaceholderValue: customTexts.searchPlaceholder || objTranslationForSite['Почніть вводити текст'],
        placeholderValue: customTexts.placeholder || objTranslationForSite['Оберіть місто'],
        loadingText: customTexts.loadingText || objTranslationForSite['Йде пошук...'],
        noResultsText: customTexts.noResultsText || objTranslationForSite['Нічого не знайдено'],
        noChoicesText: customTexts.noChoicesText || objTranslationForSite['Почніть вводити текст'],
        silent: true,
        allowHTML: false,
        itemSelectText: '',
        placeholder: true,
        searchResultLimit: 100,
        maxItemCount: 1,
        searchFloor: 1,
        removeItemButton: false,
        classNames: {
            focusState: '_is-focused',
        }
    };

    //const mergedOptions = { ...defaultOptions, ...options };
    let {preloadOnDropOpen, ...mergedOptions} = {...defaultOptions, ...options};

    preloadOnDropOpen = preloadOnDropOpen !== undefined ? preloadOnDropOpen : false;
    customRequestBody.limit = mergedOptions.searchResultLimit;

    const choices = new Choices(els, mergedOptions);
    let timer = null;
if (url) {
    els.addEventListener('showDropdown', function (event) {
        const searchTermsInput = els.parentNode.parentNode.querySelector('[name="search_terms"]');

        if (searchTermsInput) {
            searchTermsInput.addEventListener('input', function (event) {
                if (!event.target.value) {
                    setTimeout(() => {
                        //choices.clearChoices(); // очищаем список, если нет значений
                    }, 300);
                }
            });

            //Надо загрузить весь список значений при открытии списка
            if (preloadOnDropOpen/* && !event.target.value*/) {
                setTimeout(() => {
                    loadChoices(' ');
                }, 350);
            }

        }
    }, false);

    els.addEventListener('hideDropdown', function () {
        const searchTermsInput = els.parentNode.parentNode.querySelector('[name="search_terms"]');
        if (searchTermsInput) {
            searchTermsInput.removeEventListener('input', function () {
            });
        }
    });

    els.addEventListener('choice', e => {
        // Выбор значения
        //console.log(e.detail.choice.value);
    });

    els.addEventListener('search', e => {
        const inputValue = e.detail.value;

        if (timer !== null) {
            clearTimeout(timer);
        }

        timer = setTimeout(() => {
            loadChoices(inputValue);
        }, 300);
    });

    function loadChoices(query) {
        fetch(url, {
            method: 'POST',
            cache: 'no-cache',
            body: JSON.stringify({
                q: query,
                ...customRequestBody  // Добавление дополнительных параметров из customRequestBody
            }),
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
            .then(response => response.json())
            .then(data => {
                choices.setChoices(data, 'id', 'title', true);

                // Если список не пустой, устанавливаем первое значение
                if (data.length > 0) {
                    choices.setChoiceByValue(data[0].id); // Устанавливаем по id первого элемента
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
    }

}
    function selectValue(values) {
        return choices.setChoiceByValue(values);
    }

    return {
        choicesInstance: choices,
        selectValue,
        loadChoices
    };
}