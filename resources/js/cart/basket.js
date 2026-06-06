const CART_CONFIG = {
    mode:           'sidebar',
    addUrl:         '/cart/add/',
    addAllUrl:      '/cart/add-all',
    sidebarEvent:   'open-cart-drawer',
    selectors: {
        addToCart:    '[data-js-add-to-cart]',
        addAllToCart: '[data-js-add-all-to-cart]',
        spinner:      '[data-js-spinner]',
    },
};

class CartHandler {
    constructor() {
        this._off = null;
    }

    init() {
        this._destroy();
        this._off = this._delegate(document, 'click', CART_CONFIG.selectors.addToCart, (e, el) => {
            e.preventDefault();
            this._add(el);
        });
        this._offAll = this._delegate(document, 'click', CART_CONFIG.selectors.addAllToCart, (e, el) => {
            e.preventDefault();
            this._addAll(el);
        });
    }

    _destroy() {
        if (this._off) { this._off(); this._off = null; }
        if (this._offAll) { this._offAll(); this._offAll = null; }
    }

    async _add(el) {
        const productId = el.dataset.id;
        if (!productId) { return; }

        const qty     = parseInt(el.dataset.count || '1', 10);
        const options = el.dataset.options ? JSON.parse(el.dataset.options) : {};
        const list    = el.dataset.list || '';
        const spinner = el.querySelector(CART_CONFIG.selectors.spinner);

        el.disabled = true;
        if (spinner) { spinner.classList.remove('hidden'); }

        try {
            const url = `${CART_CONFIG.addUrl}${productId}${list ? '?list=' + encodeURIComponent(list) : ''}`;
            const res = await fetch(url, {
                method:  'POST',
                headers: {
                    'Content-Type':     'application/json',
                    'X-CSRF-TOKEN':     document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({ count: qty, options }),
            });

            const data = await res.json();

            if (data.status) {
                CartAnalytics.track('add_to_cart', data.products);
                Livewire.dispatch('cart-changed', { count: data.count, action: data.action, products: data.products });

                if (CART_CONFIG.mode === 'sidebar') {
                    Livewire.dispatch(CART_CONFIG.sidebarEvent);
                }
            }
        } catch (err) {
            console.error('[CartHandler] add failed', err);
        } finally {
            el.disabled = false;
            if (spinner) { spinner.classList.add('hidden'); }
        }
    }

    /**
     * Масове додавання товарів до кошика.
     * Елемент: <button data-js-add-all-to-cart data-items='[{"id":1,"count":1}]'>
     */
    async _addAll(el) {
        const items = el.dataset.items ? JSON.parse(el.dataset.items) : [];
        if (!items.length) { return; }

        el.disabled = true;

        try {
            const res = await fetch(CART_CONFIG.addAllUrl, {
                method:  'POST',
                headers: {
                    'Content-Type':     'application/json',
                    'X-CSRF-TOKEN':     document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({ items }),
            });

            const data = await res.json();

            if (data.status) {
                CartAnalytics.track('add_to_cart', data.products);
                Livewire.dispatch('cart-changed', { count: data.count, action: 'add', products: data.products });

                if (CART_CONFIG.mode === 'sidebar') {
                    Livewire.dispatch(CART_CONFIG.sidebarEvent);
                }
            }
        } catch (err) {
            console.error('[CartHandler] addAll failed', err);
        } finally {
            el.disabled = false;
        }
    }

    _delegate(root, event, selector, handler) {
        const fn = (e) => {
            const el = e.target.closest(selector);
            if (el && root.contains(el)) { handler(e, el); }
        };
        root.addEventListener(event, fn);
        return () => root.removeEventListener(event, fn);
    }
}

class CartAnalytics {
    static track(event, items) {
        if (typeof dataLayer === 'undefined' || !Array.isArray(items) || !items.length) { return; }
        dataLayer.push({ ecommerce: null });
        dataLayer.push({ event, ecommerce: { items } });
    }
}

const cartHandler = new CartHandler();

document.addEventListener('livewire:init', () => {
    cartHandler.init();

    // remove_from_cart — приходить з PHP Sidebar::remove()
    Livewire.on('cart-changed', ({ action, products }) => {
        if (action === 'remove') {
            CartAnalytics.track('remove_from_cart', products);
        }
    });

    // view_cart — приходить з PHP Sidebar::open()
    Livewire.on('view-cart', ({ items }) => {
        CartAnalytics.track('view_cart', items);
    });
});

document.addEventListener('livewire:navigated', () => { cartHandler.init(); });

// Alpine stepper для кількості товару (sidebar + сторінка товару)
document.addEventListener('alpine:init', () => {
    Alpine.data('cartStepper', ({ rowId, unitPrice = 0, updateUrl }) => ({
        rowId,
        qty: 0,
        unitPrice,
        updateUrl: updateUrl ?? `/cart/update/${rowId}`,
        _timer:    null,
        _syncing:  false,
        _observer: null,

        get totalFormatted() {
            return Math.round(this.qty * this.unitPrice).toLocaleString('uk-UA');
        },

        init() {
            const v = parseInt(this.$el.dataset.qty, 10);
            if (!isNaN(v)) { this.qty = v; }

            this._observer = new MutationObserver(() => {
                if (this._syncing) { return; }
                const dv = parseInt(this.$el.dataset.qty, 10);
                if (!isNaN(dv) && dv !== this.qty) { this.qty = dv; }
            });
            this._observer.observe(this.$el, { attributes: true, attributeFilter: ['data-qty'] });
        },

        destroy() {
            this._observer?.disconnect();
        },

        inc() {
            this.qty++;
            this._save();
        },

        dec() {
            if (this.qty > 1) { this.qty--; this._save(); }
        },

        _save() {
            clearTimeout(this._timer);
            this._syncing = true;
            this._timer = setTimeout(() => this._send(), 400);
        },

        async _send() {
            try {
                const res = await fetch(this.updateUrl, {
                    method:  'PATCH',
                    headers: {
                        'Content-Type':     'application/json',
                        'X-CSRF-TOKEN':     document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({ qty: this.qty }),
                });

                const data = await res.json();

                if (data.status) {
                    const ga4Event = data.action === 'remove' ? 'remove_from_cart' : 'add_to_cart';
                    CartAnalytics.track(ga4Event, data.products);
                    Livewire.dispatch('cart-changed', { count: data.count, action: data.action, products: data.products });
                }
            } catch (err) {
                console.error('[cartStepper] update failed', err);
            } finally {
                this._syncing = false;
            }
        },
    }));
});
