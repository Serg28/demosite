/**
 * @file lazy-loading-img.js
 *
 * Класс для ленивой загрузки изображений в Livewire приложениях
 *
 * @version 2.0
 * @author — Serg28 tsv.art.com@gmail.com for Linecore
 * @license MIT
 *
 * @description Автономный класс для ленивой загрузки изображений в Livewire приложениях
 *              Автоматически обрабатывает изображения при навигации, пагинации и фильтрации
 *              Полностью защищен от утечек памяти и дублирования обработчиков
 *
 * @usage
 * 1. Подключите этот скрипт к HTML-документу
 * 2. Добавьте атрибуты loading="lazy" и data-src к изображениям
 * 3. Класс автоматически инициализируется и работает со всеми Livewire событиями
 *
 * @example
 * <!-- Пример ленивого изображения -->
 * <img loading="lazy" 
 *      data-src="/path/to/image.jpg" 
 *      src="/path/to/placeholder.jpg" 
 *      alt="Описание">
 *
 * <!-- Пример для товара в каталоге -->
 * <div class="product-card">
 *     <img loading="lazy" 
 *          data-src="{{ $product->image_url }}" 
 *          src="/images/placeholder.svg" 
 *          alt="{{ $product->name }}">
 * </div>
 *
 * @features
 * - Автоматическая работа с Livewire навигацией, пагинацией и фильтрацией
 * - Оптимизированная производительность с IntersectionObserver
 * - Защита от утечек памяти через WeakSet и правильную очистку
 * - Обработка некорректных изображений и srcset атрибутов
 * - Фоллбэк для старых браузеров без IntersectionObserver
 */
(function() {
    'use strict';

    class LazyImageLoader {
        constructor() {
            // IntersectionObserver для отслеживания видимости изображений
            this.observer = null;
            // MutationObserver для отслеживания изменений DOM
            this.mutationObserver = null;
            // Флаг инициализации для предотвращения повторной инициализации
            this.isInitialized = false;
            // WeakSet для отслеживания обработанных изображений (автоочистка при удалении из DOM)
            this.processedImages = new WeakSet();
            
            this.init();
        }

        /**
         * Инициализация всех компонентов класса
         */
        init() {
            if (this.isInitialized) return;

            this.createObserver();
            this.processImages();
            this.setupImageObserver();
            this.isInitialized = true;
        }

        /**
         * Создает IntersectionObserver для ленивой загрузки
         */
        createObserver() {
            // Проверяем поддержку IntersectionObserver
            if (!('IntersectionObserver' in window)) {
                this.observer = null;
                return;
            }

            this.observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        this.loadImage(entry.target);
                    }
                });
            }, {
                // Начинаем загрузку за 50px до появления изображения в области видимости
                rootMargin: '50px 0px',
                // Минимальный процент видимости для срабатывания
                threshold: 0.01
            });
        }

        /**
         * Загружает изображение и удаляет его из наблюдения
         * @param {HTMLImageElement} img - Элемент изображения
         */
        loadImage(img) {
            // Пропускаем если нет data-src или изображение уже обработано
            if (!img.dataset.src || this.processedImages.has(img)) {
                return;
            }

            // Пропускаем некорректные data-src (ошибки сервера)
            if (img.dataset.src.includes('Image source not readable') || 
                img.dataset.src.includes('not found') || 
                img.dataset.src.length < 4) {
                this.processedImages.add(img);
                if (this.observer) {
                    this.observer.unobserve(img);
                }
                return;
            }

            // Сохраняем оригинальный src для фоллбэка при ошибке загрузки
            const originalSrc = img.src;

            // Очищаем некорректные srcset атрибуты перед установкой нового src
            this.cleanupImageAttributes(img);
            
            // Устанавливаем реальный URL изображения
            img.src = img.dataset.src;
            
            // Обработчик ошибки загрузки с автоматической очисткой
            const handleError = () => {
                if (originalSrc && originalSrc !== img.dataset.src) {
                    img.src = originalSrc; // Возвращаем placeholder при ошибке
                }
            };

            // Добавляем обработчик ошибки с автоматическим удалением после первого срабатывания
            img.addEventListener('error', handleError, { once: true });
            
            // Помечаем изображение как обработанное
            this.processedImages.add(img);
            
            // Удаляем из наблюдения IntersectionObserver
            if (this.observer) {
                this.observer.unobserve(img);
            }
        }

        /**
         * Очищает некорректные атрибуты изображения
         * @param {HTMLImageElement} img - Элемент изображения
         */
        cleanupImageAttributes(img) {
            if (img.srcset) {
                const srcsetValue = img.srcset.trim();
                
                // Проверяем корректность формата srcset
                const isValidSrcset = /^[^\s]+(\s+\d+[wx])?(\s*,\s*[^\s]+(\s+\d+[wx])?)*$/i.test(srcsetValue);
                
                // Удаляем некорректные srcset
                if (!isValidSrcset || srcsetValue === 'Image' || srcsetValue.length < 5) {
                    img.removeAttribute('srcset');
                }
            }

            // Удаляем sizes если srcset был удален
            if (!img.srcset && img.sizes) {
                img.removeAttribute('sizes');
            }
        }

        /**
         * Обрабатывает все ленивые изображения на странице
         */
        processImages() {
            const lazyImages = document.querySelectorAll("img[loading='lazy'][data-src]");
            
            lazyImages.forEach(img => {
                // Обрабатываем только новые изображения
                if (!this.processedImages.has(img)) {
                    if (this.observer) {
                        // Добавляем в наблюдение IntersectionObserver
                        this.observer.observe(img);
                    } else {
                        // Фоллбэк для старых браузеров - загружаем сразу
                        this.loadImage(img);
                    }
                }
            });
        }

        /**
         * Настраивает MutationObserver для отслеживания новых изображений в DOM
         */
        setupImageObserver() {
            // Отключаем предыдущий observer если есть
            if (this.mutationObserver) {
                this.mutationObserver.disconnect();
            }

            this.mutationObserver = new MutationObserver((mutations) => {
                let shouldProcess = false;

                // Проверяем все изменения DOM
                for (const mutation of mutations) {
                    // Ищем добавленные элементы
                    for (const node of mutation.addedNodes) {
                        if (node.nodeType === Node.ELEMENT_NODE) {
                            // Проверяем наличие ленивых изображений
                            if ((node.tagName === 'IMG' && node.loading === 'lazy' && node.dataset.src) ||
                                node.querySelector?.("img[loading='lazy'][data-src]")) {
                                shouldProcess = true;
                                break;
                            }
                        }
                    }
                    if (shouldProcess) break;
                }

                // Если найдены новые ленивые изображения
                if (shouldProcess) {
                    setTimeout(() => {
                        // Всегда очищаем список обработанных для гарантированной работы
                        this.refresh(true);
                    }, 10);
                }
            });

            // Начинаем наблюдение за изменениями DOM
            this.mutationObserver.observe(document.body, {
                childList: true,
                subtree: true
            });
        }

        /**
         * Принудительная обработка изображений
         * @param {boolean} clearProcessed - Очистить список обработанных изображений
         */
        refresh(clearProcessed = false) {
            if (clearProcessed) {
                // Создаем новый WeakSet для сброса списка обработанных изображений
                this.processedImages = new WeakSet();
            }
            this.processImages();
        }

        /**
         * Полная очистка ресурсов (для предотвращения утечек памяти)
         */
        destroy() {
            // Отключаем и очищаем IntersectionObserver
            if (this.observer) {
                this.observer.disconnect();
                this.observer = null;
            }
            
            // Отключаем и очищаем MutationObserver
            if (this.mutationObserver) {
                this.mutationObserver.disconnect();
                this.mutationObserver = null;
            }
            
            // Очищаем WeakSet
            this.processedImages = new WeakSet();
            this.isInitialized = false;
        }
    }

    // Глобальные переменные с защитой от дублирования
    let lazyLoader = null;
    let isNavigationListenerAdded = false;

    /**
     * Инициализация ленивой загрузки
     */
    function initializeLazyLoading() {
        if (!lazyLoader) {
            // Создаем новый экземпляр
            lazyLoader = new LazyImageLoader();
        } else {
            // Обновляем существующий
            lazyLoader.refresh();
        }
    }

    /**
     * Добавляет слушатель навигации Livewire (только один раз)
     */
    function addNavigationListener() {
        if (isNavigationListenerAdded) return;
        
        document.addEventListener('livewire:navigated', initializeLazyLoading);
        isNavigationListenerAdded = true;
    }

    // === ИНИЦИАЛИЗАЦИЯ ===
    initializeLazyLoading();
    addNavigationListener();

    // === ДОПОЛНИТЕЛЬНАЯ ПОДДЕРЖКА LIVEWIRE МОРФИНГА ===
    if (window.Livewire && window.Livewire.hook) {
        let morphRefreshTimeout = null;

        window.Livewire.hook('morphed', ({el, component, toEl, skip, childrenOnly}) => {
            // Проверяем наличие ленивых изображений в измененном контенте
            const hasLazyImages = (el && el.querySelector && el.querySelector("img[loading='lazy'][data-src]")) ||
                                 (toEl && toEl.querySelector && toEl.querySelector("img[loading='lazy'][data-src]")) ||
                                 (el && el.tagName === 'IMG' && el.loading === 'lazy' && el.dataset.src) ||
                                 (toEl && toEl.tagName === 'IMG' && toEl.loading === 'lazy' && toEl.dataset.src);

            if (hasLazyImages && lazyLoader) {
                // Отменяем предыдущий таймаут для предотвращения дублирования
                if (morphRefreshTimeout) {
                    clearTimeout(morphRefreshTimeout);
                }

                // Группируем вызовы с задержкой для оптимизации
                morphRefreshTimeout = setTimeout(() => {
                    lazyLoader.refresh(true);
                    morphRefreshTimeout = null;
                }, 50);
            }
        });
    }

    // === ЭКСПОРТ ДЛЯ ВНЕШНЕГО ИСПОЛЬЗОВАНИЯ ===
    
    // Функция для ручного обновления (обратная совместимость)
    window.initLazy = function() {
        if (lazyLoader) {
            lazyLoader.refresh();
        } else {
            initializeLazyLoading();
        }
    };

    // Доступ к экземпляру класса для продвинутого использования
    if (!Object.getOwnPropertyDescriptor(window, 'lazyLoader')) {
        Object.defineProperty(window, 'lazyLoader', {
            get: function() {
                return lazyLoader;
            }
        });
    }

    // Экспорт класса для создания дополнительных экземпляров
    window.LazyImageLoader = LazyImageLoader;

})();