/**
 * CatalogFilter — SEO path-based filter manager.
 *
 * Responsibilities:
 *  - Intercept clicks on .js-filter-link (SEO anchor → reads paired input's data-url)
 *  - Intercept changes on .js-filter-input (direct checkbox interaction)
 *  - Both actions: history.pushState(newPath) + Livewire.dispatch('filter-changed')
 *  - Initialize .js-range-slider elements via CatalogRangeSlider
 *  - Handle popstate (browser back/forward) → re-dispatch filter-changed
 *  - Re-initialize on wire:navigate (livewire:navigated event)
 *
 * No logic in HTML attributes — configured entirely via data-* and CSS classes.
 */
class CatalogFilter {
    /** @param {string} rootSelector - CSS selector for the filter sidebar root */
    constructor(rootSelector = '.lw-catalog-facets') {
        this.root = rootSelector;
        this._sliders = [];
        this._offClick = null;
        this._offChange = null;
        this._offPopstate = null;
    }

    init() {
        if (!document.querySelector(this.root)) { return; }

        this._destroy();
        this._bindFilterLink();
        this._bindFilterInput();
        this._bindPopstate();
        this._initRangeSliders();
    }

    _destroy() {
        if (this._offClick)    { document.removeEventListener('click', this._offClick); }
        if (this._offChange)   { document.removeEventListener('change', this._offChange); }
        if (this._offPopstate) { window.removeEventListener('popstate', this._offPopstate); }
        this._sliders = [];
    }

    _bindFilterLink() {
        this._offClick = (e) => {
            const link = e.target.closest('.js-filter-link');
            if (!link) { return; }

            e.preventDefault();
            e.stopPropagation();

            const inputId = link.dataset.inputId;
            if (!inputId) {
                // Direct link: active filter tag, clear-all — navigate to href
                const href = link.getAttribute('href');
                if (href) { this._navigate(href); }
                return;
            }

            const input = document.getElementById(inputId);
            if (input && !input.disabled) {
                this._navigate(input.dataset.url || link.getAttribute('href'));
            }
        };

        document.addEventListener('click', this._offClick);
    }

    _bindFilterInput() {
        this._offChange = (e) => {
            const input = e.target.closest('.js-filter-input');
            if (!input || input.disabled) { return; }

            const url = input.dataset.url;
            if (url) { this._navigate(url); }
        };

        document.addEventListener('change', this._offChange);
    }

    _bindPopstate() {
        this._offPopstate = () => {
            if (typeof Livewire !== 'undefined') {
                Livewire.dispatch('filter-changed', { path: window.location.pathname });
            }
        };

        window.addEventListener('popstate', this._offPopstate);
    }

    _initRangeSliders() {
        document.querySelectorAll('.js-range-slider').forEach((el) => {
            const slider = new CatalogRangeSlider(el);
            this._sliders.push(slider);
        });

        // Redraw range slider values after Livewire morph
        if (typeof Livewire !== 'undefined') {
            Livewire.hook('morph.updated', ({ el }) => {
                if (el.classList && el.classList.contains('js-range-slider')) {
                    const match = this._sliders.find((s) => s.container === el);
                    if (match) { match.redraw(); }
                }
            });
        }
    }

    /** Push new URL and dispatch Livewire filter-changed event. */
    _navigate(url) {
        if (!url || url === '#') { return; }

        try {
            const parsed = new URL(url, window.location.origin);
            const finalUrl = parsed.pathname + (parsed.search || '');
            history.pushState({}, '', finalUrl);

            if (typeof Livewire !== 'undefined') {
                Livewire.dispatch('filter-changed', { path: parsed.pathname });
            }
        } catch {
            history.pushState({}, '', url);
            if (typeof Livewire !== 'undefined') {
                Livewire.dispatch('filter-changed', { path: url.split('?')[0] });
            }
        }
    }
}

// ─── Bootstrap ───────────────────────────────────────────────────────────────

const catalogFilter = new CatalogFilter('.lw-catalog-facets');

document.addEventListener('livewire:init', () => {
    catalogFilter.init();
});

// Re-init after wire:navigate page transitions
document.addEventListener('livewire:navigated', () => {
    catalogFilter.init();
});
