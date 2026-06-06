/**
 * @file compare.js
 *
 * Обробник порівняння товарів із підтримкою динамічного DOM та Livewire.
 * Портовано з yandmi/www/resources/js/base/compare.js
 *
 * @version 2.0
 * @author Serg28 tsv.art.com@gmail.com for Linecore
 * @license MIT
 */

(function () {
    'use strict';

    if (window.__compareHandlerInitialized) return;
    window.__compareHandlerInitialized = true;

    const state = {
        instance: null,
        statusTimeout: null,
        observer: null,
    };
    window.__CompareHandlerState = state;

    class CompareHandler {
        static config = {
            selector: '[data-js-compare]',
            activeClass: 'active',
            loadingClass: 'loading',
        };

        constructor(csrfToken) {
            this.csrfToken = csrfToken;
            this.updateStatuses();
        }

        toggleCompare(btn) {
            const { id } = btn.dataset;
            const isActive = btn.classList.contains(CompareHandler.config.activeClass);
            const url = isActive ? `/compare/delete/${id}` : `/compare/add/${id}`;

            btn.classList.add(CompareHandler.config.loadingClass);

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({}),
            })
                .then((res) => res.json())
                .then((data) => {
                    if (data.status === 'success') {
                        btn.classList.toggle(CompareHandler.config.activeClass, data.inCompare);
                        window.Livewire?.dispatch('updateCompareCount', { count: data.count });
                        window.Livewire?.dispatch('notify', {
                            message: data.message,
                            type: 'success',
                        });

                        if (data.product) {
                            this.sendAnalytics(data, isActive ? 'remove' : 'add');
                        }
                    } else {
                        window.Livewire?.dispatch('notify', {
                            message: data.message || 'Помилка операції з порівнянням',
                            type: 'error',
                        });
                    }
                })
                .catch((err) => {
                    console.error('Помилка порівняння:', err);
                    window.Livewire?.dispatch('notify', {
                        message: 'Помилка операції з порівнянням',
                        type: 'error',
                    });
                })
                .finally(() => {
                    btn.classList.remove(CompareHandler.config.loadingClass);
                });
        }

        updateStatuses() {
            const buttons = Array.from(
                document.querySelectorAll(CompareHandler.config.selector)
            );
            const ids = [...new Set(buttons.map((b) => b.dataset.id).filter(Boolean))];

            if (!ids.length) return;

            fetch('/compare/status', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({ productIds: ids }),
            })
                .then((res) => res.json())
                .then((data) => {
                    buttons.forEach((btn) =>
                        btn.classList.remove(CompareHandler.config.activeClass)
                    );
                    (data.inCompare || []).forEach((id) => {
                        document
                            .querySelectorAll(
                                `${CompareHandler.config.selector}[data-id="${id}"]`
                            )
                            .forEach((btn) =>
                                btn.classList.add(CompareHandler.config.activeClass)
                            );
                    });
                })
                .catch((err) => console.warn('Помилка оновлення статусів порівняння:', err));
        }

        sendAnalytics(data, action) {
            const product = data.product;
            if (!product) return;

            const value = product.price;
            const currency = 'UAH';

            if (typeof dataLayer !== 'undefined') {
                dataLayer.push({ ecommerce: null });
                dataLayer.push({
                    event: action === 'add' ? 'add_to_compare' : 'remove_from_compare',
                    ecommerce: { value, currency, items: product },
                });
            }

            if (typeof fbq === 'function') {
                fbq('track', action === 'add' ? 'AddToCompare' : 'RemoveFromCompare', {
                    value,
                    item_name: product.item_name,
                    currency,
                    content_ids: [product.item_id],
                    content_type: 'product',
                    content_category: product.item_category,
                });
            }
        }
    }

    // Єдиний статичний делегований обробник — не дублюється при перестворенні instance.
    document.addEventListener('click', (e) => {
        const btn = e.target.closest(CompareHandler.config.selector);
        if (btn && state.instance) {
            e.preventDefault();
            state.instance.toggleCompare(btn);
        }
    });

    function initHandler() {
        const token = document.querySelector('meta[name="csrf-token"]')?.content;
        if (token && !state.instance) {
            state.instance = new CompareHandler(token);
        }
    }

    function scheduleStatusUpdate() {
        if (!state.instance) return;
        if (state.statusTimeout) clearTimeout(state.statusTimeout);
        state.statusTimeout = setTimeout(() => {
            state.instance.updateStatuses();
            state.statusTimeout = null;
        }, 100);
    }

    function observeDOM() {
        const selector = CompareHandler.config.selector;
        if (state.observer) state.observer.disconnect();
        state.observer = new MutationObserver((mutations) => {
            for (const { addedNodes } of mutations) {
                for (const node of addedNodes) {
                    if (
                        node.nodeType === 1 &&
                        (node.matches?.(selector) || node.querySelector?.(selector))
                    ) {
                        scheduleStatusUpdate();
                        return;
                    }
                }
            }
        });
        state.observer.observe(document.body, { childList: true, subtree: true });
    }

    document.addEventListener('livewire:navigated', () => {
        if (state.observer) state.observer.disconnect();
        state.instance = null;
        initHandler();
        observeDOM();
    });

    if (window.Livewire?.hook) {
        let morphTimeout = null;
        Livewire.hook('morphed', ({ el, toEl }) => {
            const selector = CompareHandler.config.selector;
            const hasBtn = [el, toEl].some(
                (node) =>
                    node?.matches?.(selector) || node?.querySelector?.(selector)
            );
            if (hasBtn && state.instance) {
                if (morphTimeout) clearTimeout(morphTimeout);
                morphTimeout = setTimeout(() => {
                    scheduleStatusUpdate();
                    morphTimeout = null;
                }, 50);
            }
        });
    }

    initHandler();
    observeDOM();
})();
