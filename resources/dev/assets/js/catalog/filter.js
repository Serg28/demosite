//Инициалзация фильтра в компоненте app/Livewire/Category/FilterProducts.php
//Подключение в шаблоне resources/views/category/auto/partials/livewire-filter-products.blade.php
//или подобные в папке resources/views/category/.../livewire-filter-products.blade.php
//const sliders = [];

function initFilter(component) {
    const selector = `${component} `;

    if (!document.querySelector(component)) {
        return false;
    }

    const handleClick = (e, attribute) => {
        const target = e.target.closest(`${selector}${attribute}`);
        if (target) {
            e.preventDefault();
            const value = target.getAttribute(e.target.tagName === 'A' ? 'href' : 'data-url');
            if (value && value !== '#') {
                history.pushState({}, '', value);
                Livewire.dispatch('filter-changed');
                window.scrollTo({top: 0, behavior: "smooth"});
            }
        }
    };

    document.addEventListener("click", (e) => handleClick(e, '.filters_category a'));
    document.addEventListener("change", (e) => handleClick(e, '.filters_category input'));

    //Ленивая загрузка фото - переинициализация
    Livewire.hook('morph.updating', ({el, component, toEl, skip, childrenOnly}) => {
        if (typeof initLazy === 'function') {
            initLazy();
        }
    });

    //Слайдер цен
    const slider = new RangeSlider('#sliderPrices'/*, (container, min, max) => {
        //Здесь колл-бек. По-умолчанию задействуется defaultEventCallback
    }*/);
}


/**
 * Класс RangeSlider для создания слайдера диапазонов
 *
 * // Пример использования RangeSlider для разных коллбэков для каждого контейнера
 * document.addEventListener('DOMContentLoaded', () => {
 *         // Экземпляр 1 с пользовательским коллбэком
 *         new RangeSlider('#sliderContainer1', (container, min, max) => {
 *             const baseUrl = container.dataset.url;
 *             const param = container.dataset.param ? container.dataset.param : 'price';
 *             const url = `${baseUrl}/${param}=${min}-${max}`;
 *             history.pushState({}, '', url);
 *             console.log(`Пользовательский коллбэк для контейнера 1: Мин - ${min}, Макс - ${max}`);
 *         });
 *
 *         // Экземпляр 2 с другим пользовательским коллбэком
 *         new RangeSlider('#sliderContainer2', (container, min, max) => {
 *             const baseUrl = container.dataset.url;
 *             const param = container.dataset.param ? container.dataset.param : 'price';
 *             const url = `${baseUrl}/${param}=${min}-${max}`;
 *             history.pushState({}, '', url);
 *             console.log(`Пользовательский коллбэк для контейнера 2: Мин - ${min}, Макс - ${max}`);
 *         });
 *
 *         // Экземпляр 3 дефолтный коллбек
 *         new RangeSlider('#sliderContainer3');
 *     });
 *
 *     <div class="slider-wrapper">
 *     <div id="sliderContainer1" class="slider-container" data-url="http://site.com/catalog" data-param="price">
 *         <div class="values">
 *             <label>Min:</label>
 *             <input type="number" class="minValue" value="100">
 *             <label>Max:</label>
 *             <input type="number" class="maxValue" value="900">
 *         </div>
 *         <div class="range-track">
 *             <div class="range"></div>
 *         </div>
 *         <input type="range" class="minSlider" min="0" max="1000" value="100">
 *         <input type="range" class="maxSlider" min="0" max="1000" value="900">
 *     </div>
 * </div>
 */
(function () {
    if (typeof RangeSlider === 'undefined') {
        class RangeSlider {
            /**
             * Создает экземпляр класса RangeSlider.
             * @param {string} containerClass - Класс контейнера слайдера.
             * @param {function} [eventCallback] - Необязательная функция обратного вызова события.
             */
            constructor(containerClass, eventCallback = null) {
                this.sliderContainers = document.querySelectorAll(containerClass);

                if (this.sliderContainers.length === 0) {
                    throw new Error(`Контейнеры с классом ${containerClass} не найдены.`);
                }

                this.eventCallback = eventCallback;
                this.initializeContainers();

                // Добавление слушателя событий для перерисовки слайдера
                Livewire.hook('morph.updated', () => {
                    this.redrawSliders();
                });
            }

            /**
             * Инициализирует контейнеры слайдера.
             */
            initializeContainers() {
                this.sliderContainers.forEach(container => {
                    const {minSlider, maxSlider, minValue, maxValue, range} = this.getElements(container);
                    if (!minSlider || !maxSlider || !minValue || !maxValue || !range) {
                        console.error('Отсутствуют необходимые элементы в контейнере:', container);
                        return;
                    }

                    this.setupEventListeners(container);
                    this.updateValues(container);
                });
            }

            /**
             * Возвращает элементы слайдера.
             * @param {HTMLElement} container - Контейнер слайдера.
             * @returns {Object} Элементы слайдера.
             */
            getElements(container) {
                return {
                    minSlider: container.querySelector('.minSlider'),
                    maxSlider: container.querySelector('.maxSlider'),
                    minValue: container.querySelector('.minValue'),
                    maxValue: container.querySelector('.maxValue'),
                    range: container.querySelector('.range'),
                };
            }

            /**
             * Устанавливает слушатели событий для элементов слайдера.
             * @param {HTMLElement} container - Контейнер слайдера.
             */
            setupEventListeners(container) {
                const {minSlider, maxSlider, minValue, maxValue} = this.getElements(container);

                minSlider.addEventListener('input', () => this.updateValues(container));
                maxSlider.addEventListener('input', () => this.updateValues(container));
                minSlider.addEventListener('change', () => this.handleEvent(container));
                maxSlider.addEventListener('change', () => this.handleEvent(container));
                minValue.addEventListener('change', () => this.updateFromInput(container));
                maxValue.addEventListener('change', () => this.updateFromInput(container));
            }

            /**
             * Обновляет значения слайдера и визуализацию диапазона.
             * @param {HTMLElement} container - Контейнер слайдера.
             */
            updateValues(container) {
                const {minSlider, maxSlider, minValue, maxValue, range} = this.getElements(container);
                const min = parseInt(minSlider.value);
                const max = parseInt(maxSlider.value);

                if (min <= max) {
                    minValue.value = min;
                    maxValue.value = max;
                    this.updateRange(container);
                }
            }

            /**
             * Обновляет визуализацию диапазона.
             * @param {HTMLElement} container - Контейнер слайдера.
             */
            updateRange(container) {
                const {minSlider, range} = this.getElements(container);
                const min = parseInt(minSlider.value);
                const max = parseInt(container.querySelector('.maxSlider').value);
                const minPercent = ((min - minSlider.min) / (minSlider.max - minSlider.min)) * 100;
                const maxPercent = ((max - minSlider.min) / (minSlider.max - minSlider.min)) * 100;
                range.style.left = `calc(${minPercent}% + 10px)`;
                range.style.width = `calc(${maxPercent - minPercent}% - 10px)`;
            }

            /**
             * Обрабатывает событие изменения значения слайдера.
             * @param {HTMLElement} container - Контейнер слайдера.
             */
            handleEvent(container) {
                const {minValue, maxValue} = this.getElements(container);
                const minPrice = minValue.value;
                const maxPrice = maxValue.value;
                const callback = this.eventCallback || this.defaultEventCallback;
                callback(container, minPrice, maxPrice);
            }

            /**
             * Обновляет значения слайдера из полей ввода.
             * @param {HTMLElement} container - Контейнер слайдера.
             */
            updateFromInput(container) {
                const {minValue, maxValue, minSlider, maxSlider} = this.getElements(container);
                const minVal = parseInt(minValue.value);
                const maxVal = parseInt(maxValue.value);

                if (minVal <= maxVal) {
                    minSlider.value = minVal;
                    maxSlider.value = maxVal;
                    this.updateRange(container);
                    this.handleEvent(container);
                } else {
                    minValue.value = minSlider.value;
                    maxValue.value = maxSlider.value;
                }
            }

            /**
             * Обратный вызов по умолчанию для обработки изменения значений слайдера.
             * @param {HTMLElement} container - Контейнер слайдера.
             * @param {number} min - Минимальное значение.
             * @param {number} max - Максимальное значение.
             */
            defaultEventCallback(container, min, max) {
                const baseUrl = container.dataset.url;
                const param = container.dataset.param ? container.dataset.param : 'price';
                const url = `${baseUrl}/${param}=${min}-${max}`;
                history.pushState({}, '', url);
                Livewire.dispatch('filter-changed');
            }

            /**
             * Перерисовывает слайдеры, используя данные из атрибутов контейнера.
             */
            redrawSliders() {
                this.sliderContainers.forEach(container => {
                    const { minSlider, maxSlider, minValue, maxValue } = this.getElements(container);
                    const min = container.dataset.min;
                    const max = container.dataset.max;
                    const minValueData = container.dataset.minValue;
                    const maxValueData = container.dataset.maxValue;

                    if (minSlider && maxSlider && minValue && maxValue) {
                        minSlider.min = min;
                        maxSlider.min = min;
                        minSlider.max = max;
                        maxSlider.max = max;
                        minSlider.value = minValueData;
                        maxSlider.value = maxValueData;
                        minValue.value = minValueData;
                        maxValue.value = maxValueData;

                        this.updateRange(container);
                    }
                });
            }
        }

        window.RangeSlider = RangeSlider;
    }
})();


/**
 * Инициализация компонента фильтрации элементов.
 *
 * Этот компонент добавляет функциональность поиска и фильтрации элементов на странице.
 * Используйте атрибут `x-data="searchItems"` для активации компонента.
 *
 * Пример использования:
 * <div x-data="searchItems">
 *   <input type="text" x-model="search" placeholder="Поиск...">
 *   <ul>
 *     <li x-show="show($el)">Элемент 1</li>
 *     <li x-show="show($el)">Элемент 2</li>
 *     <li x-show="show($el)">Элемент 3</li>
 *   </ul>
 * </div>
 */
document.addEventListener('alpine:init', () => {
    Alpine.data('searchItems_orig', () => ({
        search: '',
        /**
         * Функция для показа или скрытия элементов в зависимости от введенного текста поиска.
         * Регистронезависимый поиск.
         *
         * @param {HTMLElement} el - Элемент, который необходимо проверить.
         * @return {boolean} - Возвращает true, если элемент должен быть показан, иначе false.
         */
        show(el) {
            const searchTerm = this.search.toLowerCase();
            const textContent = el.textContent.toLowerCase();
            return searchTerm === '' || textContent.includes(searchTerm);
        }
    }));
});







/**
 * Инициализация компонента фильтрации элементов.
 *
 * Этот компонент добавляет функциональность блоку фильтрации: фильтрация элементов, кол-во видимых элементов по дефолту.
 * Активация ссылок Показать еще и Скрыть с их запоминанием до перезагрузки страницы
 * Используйте атрибут `x-data="searchItems"` для активации компонента.
 *
 * Пример использования:
 * <div class="filters_category" x-data="searchItems(custom_id)" x-init="init" data-id="custom_id">
 *   <input type="text" x-model="search" @input="updateVisibility" placeholder="Поиск...">
 *   <div x-ref="itemsContainer">
     *   <label data-item>
 *          <input type="checkbox" data-url="url_фильтра_с_текущим_значением" value="ID_опции">
 *       </label>
 *       <label data-item>
 *          <input type="checkbox" data-url="url_фильтра_с_текущим_значением" value="ID_опции">
 *       </label>
 *       <label data-item>
 *          <input type="checkbox" data-url="url_фильтра_с_текущим_значением" value="ID_опции">
 *       </label>
 *   </div>
 *   <p x-cloak x-show="!items.filter(item => show(item)).length">Нічого не знайдено.</p>
 *         <div x-cloak class="show-more-wrap start" x-show="hiddenCount > 0">
 *             <button class="show-more" @click="toggleShowMore()">
 *                 <span x-cloak x-show="!showMoreClicked">Показати ще {{--<span x-text="hiddenCount"></span>--}}</span>
 *                 <span x-cloak x-show="showMoreClicked">Приховати</span>
 *             </button>
 *         </div>
 * </div>
 */
document.addEventListener('alpine:init', () => {
    Alpine.data('searchItems', (id) => ({
        search: '',
        items: [],
        shownItems: 10,
        showMoreClicked: Alpine.$persist(false).as(`showMoreClicked_${id}`),
        heightSet: false,
        initialized: false, // Track initialization state
        listeners: [],
        init() {
            if (!this.initialized) {
                this.items = Array.from(this.$refs.itemsContainer.querySelectorAll('[data-item]')).map(el => ({
                    el,
                    text: el.textContent.toLowerCase()
                }));
                this.updateVisibility();
                this.applyHeight();

                // Register new listener
                const listener = Livewire.on('filter-product-updated', () => {
                    document.querySelectorAll('[x-data^="searchItems"]').forEach(el => {
                        const id = el.getAttribute('data-id');
                        const showMoreClicked = localStorage.getItem(`showMoreClicked_${id}`);

                        const itemsContainer = el.querySelector('[x-ref="itemsContainer"]');

                        if (showMoreClicked === "true") {
                            itemsContainer.style.height = 'auto';
                        } else {
                            const items = Array.from(itemsContainer.querySelectorAll('[data-item]'));
                            let totalHeight = items.slice(0, this.shownItems).reduce((acc, item) => acc + item.offsetHeight, 0);
                            totalHeight = totalHeight>0 ? totalHeight+'px' : 'auto';
                            requestAnimationFrame(() => {
                                itemsContainer.style.height = `${totalHeight}`;
                            });
                        }
                    });
                });
                this.listeners.push(listener);

                this.initialized = true;
            } else {
                //Если нужно сделать запоминание состояния открыт-закрыт, закомментировать ниже
                // Restore state on page reload
                this.showMoreClicked = false; // Start collapsed
                this.applyHeight(); // Apply initial height
            }
        },

        destroy() {
            this.listeners.forEach((listener) => listener());
        },

        show(item) {
            const searchTerm = this.search.toLowerCase();
            return searchTerm === '' || item.text.includes(searchTerm);
        },

        updateVisibility() {
            let visibleCount = 0;

            this.items.forEach((item) => {
                if (this.show(item) && (visibleCount < this.shownItems || this.showMoreClicked)) {
                //if (this.show(item)) {
                    item.el.style.display = '';
                    visibleCount++;
                } else {
                    item.el.style.display = 'none';
                }
            });

            this.applyHeight();
        },

        toggleShowMore() {
            this.showMoreClicked = !this.showMoreClicked;
            this.updateVisibility();
        },

        applyHeight() {
            if (this.showMoreClicked) {
                this.$refs.itemsContainer.style.height = 'auto';
                this.heightSet = false;
            } else {
                let totalHeight = this.items.slice(0, this.shownItems).reduce((acc, item) => acc + item.el.offsetHeight, 0);
                totalHeight = totalHeight>0 ? totalHeight+'px' : 'auto';
                //this.$refs.itemsContainer.style.height = `${totalHeight}px`;
                this.$refs.itemsContainer.style.height = `${totalHeight}`;
                this.heightSet = true;
            }
        },

        get hiddenCount() {
            return Math.max(0, this.items.filter(item => this.show(item)).length - this.shownItems);
        }
    }));
});


/**
 * Сортирует элементы фильтра по активности и значениям.
 * Активные элементы перемещаются вверх списка, а неактивные - вниз.
 * Элементы сортируются сначала по числовым значениям, если они присутствуют в начале строки,
 * затем по алфавиту.
 *
 * @return void
 */
function sortActiveCharacteristicsTop() {
    const filterContainers = document.querySelectorAll(".filters_category");

    filterContainers.forEach(filterContainer => {
        // Находим контейнер элементов с помощью x-ref
        const listContainer = filterContainer.querySelector("[x-ref='itemsContainer']");

        if (!listContainer) {
            //console.error("Контейнер элементов не найден для .filters_category", filterContainer);
            return; // Пропускаем текущий контейнер, если контейнер элементов не найден
        }

        // Собираем все элементы списка
        const listItems = Array.from(listContainer.querySelectorAll("label"));

        // Функция для проверки, начинается ли строка с числа
        function startsWithNumber(value) {
            return /^\d/.test(value.trim());
        }

        // Сортируем только активные элементы
        const activeItems = listItems.filter(item => {
            return !item.querySelector('[type="checkbox"]').disabled;
        });

        // Сортировка активных элементов
        activeItems.sort((a, b) => {
            const textA = a.textContent.trim();
            const textB = b.textContent.trim();

            const typeA = startsWithNumber(textA) ? "numeric" : "string";
            const typeB = startsWithNumber(textB) ? "numeric" : "string";

            if (typeA === "numeric" && typeB === "numeric") {
                return parseInt(textA) - parseInt(textB);
            } else {
                return textA.localeCompare(textB);
            }
        });

        // Очищаем контейнер перед добавлением отсортированных элементов
        listContainer.innerHTML = "";

        // Вставляем отсортированные активные элементы в начало списка
        activeItems.forEach(item => {
            listContainer.appendChild(item);
        });

        // Сортируем неактивные элементы
        const disabledItems = listItems.filter(item => !activeItems.includes(item));

        // Сортировка неактивных элементов
        disabledItems.sort((a, b) => {
            const textA = a.textContent.trim();
            const textB = b.textContent.trim();

            const typeA = startsWithNumber(textA) ? "numeric" : "string";
            const typeB = startsWithNumber(textB) ? "numeric" : "string";

            if (typeA === "numeric" && typeB === "numeric") {
                return parseInt(textA) - parseInt(textB);
            } else {
                return textA.localeCompare(textB);
            }
        });

        // Вставляем отсортированные неактивные элементы в конец списка под активными
        disabledItems.forEach(item => {
            listContainer.appendChild(item);
        });
    });
}


//Инициализация слайдера
initFilter('.lw-catalog-filter');
