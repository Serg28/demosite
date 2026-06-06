/**
 * @file like.js
 *
 * Обробник лайків товарів із підтримкою динамічного DOM та Livewire.
 * Портовано з linecore-demo/resources/dev/js/base/like.js
 *
 * @version 2.0
 * @author Serg28 tsv.art.com@gmail.com for Linecore
 * @license MIT
 */

(function () {
    'use strict';

    if (window.__likeHandlerInitialized) return;
    window.__likeHandlerInitialized = true;

    const state = {
        instance: null,
        statusTimeout: null,
        observer: null,
    };
    window.__LikeHandlerState = state;

    class LikeHandler {
        static config = {
            activeClass: 'active',
            loadingClass: 'loading',
            selector: '[data-js-like]',
        };

        constructor(csrfToken) {
            this.csrfToken = csrfToken;
            this.updateStatuses();
        }

        toggleLike(btn) {
            const { id } = btn.dataset;
            const isActive = btn.classList.contains(LikeHandler.config.activeClass);

            btn.classList.add(LikeHandler.config.loadingClass);

            fetch(`/like/toggle/${id}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': this.csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                },
            })
                .then((res) => {
                    if (res.status === 401) {
                        window.Livewire?.dispatch('openModal', {
                            component: 'auth.popup',
                            arguments: {},
                        });
                        return null;
                    }
                    return res.json();
                })
                .then((data) => {
                    if (!data) return;

                    btn.classList.toggle(LikeHandler.config.activeClass, data.isActive);
                    window.Livewire?.dispatch('updateFavoriteCount');
                    window.Livewire?.dispatch('notify', {
                        message: data.message,
                        type: data.status,
                    });

                    if (!isActive && data.product) this.sendAnalytics(data);
                })
                .catch((err) => {
                    window.Livewire?.dispatch('notify', {
                        message: err.message,
                        type: 'error',
                    });
                })
                .finally(() => {
                    btn.classList.remove(LikeHandler.config.loadingClass);
                });
        }

        updateStatuses() {
            const buttons = Array.from(
                document.querySelectorAll(LikeHandler.config.selector)
            );
            const ids = [...new Set(buttons.map((b) => b.dataset.id).filter(Boolean))];

            if (!ids.length) return;

            fetch('/like/status', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({ productIds: ids }),
            })
                .then((res) => {
                    if (res.status === 401) return null; // гість — тихо ігноруємо
                    return res.json();
                })
                .then((data) => {
                    if (!data) return;

                    buttons.forEach((btn) =>
                        btn.classList.remove(LikeHandler.config.activeClass)
                    );
                    (data.favorites || []).forEach((id) => {
                        document
                            .querySelectorAll(
                                `${LikeHandler.config.selector}[data-id="${id}"]`
                            )
                            .forEach((btn) =>
                                btn.classList.add(LikeHandler.config.activeClass)
                            );
                    });
                })
                .catch((err) => console.warn('Помилка оновлення лайків:', err));
        }

        sendAnalytics(data) {
            const product = data.product;
            if (!product) return;

            const value = product.price;
            const currency = 'UAH';

            if (typeof dataLayer !== 'undefined') {
                dataLayer.push({ ecommerce: null });
                dataLayer.push({
                    event: 'add_to_favorite',
                    ecommerce: { value, currency, items: product },
                });
            }

            if (typeof fbq === 'function') {
                fbq('track', 'AddToFavorite', {
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
        const btn = e.target.closest(LikeHandler.config.selector);
        if (btn && state.instance) {
            state.instance.toggleLike(btn);
        }
    });

    function initHandler() {
        const token = document.querySelector('meta[name="csrf-token"]')?.content;
        if (token && !state.instance) {
            state.instance = new LikeHandler(token);
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
        const selector = LikeHandler.config.selector;
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
            const selector = LikeHandler.config.selector;
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
