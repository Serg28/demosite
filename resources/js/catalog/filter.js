/**
 * CatalogFilter — SEO path-based filter manager.
 *
 * Responsibilities:
 *  - Intercept clicks on .js-filter-link (SEO anchor → reads paired input's data-char-slug/data-opt-slug)
 *  - Intercept changes on .js-filter-input (direct checkbox interaction)
 *  - Both actions: history.pushState(newPath) + Livewire.dispatch('filter-changed')
 *  - Initialize .js-range-slider elements via CatalogRangeSlider
 *  - Handle popstate (browser back/forward) → re-dispatch filter-changed
 *  - Re-initialize on wire:navigate (livewire:navigated event)
 *
 * URL building happens in JS from data-char-slug + data-opt-slug attributes,
 * using basePath from [data-base-path] on the root element.
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
                if (href && href !== '#') { this._navigate(href); }
                return;
            }

            const input = document.getElementById(inputId);
            if (input && !input.disabled) {
                const url = this._resolveInputUrl(input);
                if (url) { this._navigate(url); }
            }
        };

        document.addEventListener('click', this._offClick);
    }

    _bindFilterInput() {
        this._offChange = (e) => {
            const input = e.target.closest('.js-filter-input');
            if (!input || input.disabled) { return; }

            const url = this._resolveInputUrl(input);
            if (url) { this._navigate(url); }
        };

        document.addEventListener('change', this._offChange);
    }

    _bindPopstate() {
        this._offPopstate = () => {
            if (typeof Livewire !== 'undefined') {
                const url = new URL(location.href);
                const page = parseInt(url.searchParams.get('page') || '1', 10);
                const sort = url.searchParams.get('sort') || '';
                Livewire.dispatch('filter-changed', { path: url.pathname, page });
                Livewire.dispatch('sort-changed', { sortKey: sort });
            }
        };

        window.addEventListener('popstate', this._offPopstate);
    }

    _initRangeSliders() {
        document.querySelectorAll('.js-range-slider').forEach((el) => {
            const slider = new CatalogRangeSlider(el);
            this._sliders.push(slider);
        });
    }

    /**
     * Resolve the toggle URL for a checkbox input.
     * Prefers data-url (legacy), then builds from data-char-slug + data-opt-slug.
     */
    _resolveInputUrl(input) {
        if (input.dataset.url) { return input.dataset.url; }

        const charSlug = input.dataset.charSlug;
        const optSlug  = input.dataset.optSlug;
        if (!charSlug || !optSlug) { return null; }

        return this._buildOptionUrl(charSlug, optSlug);
    }

    /**
     * Build a toggle URL for a checkbox option.
     * Reads basePath from [data-base-path] on the root element.
     * Computes filtersPath from window.location.pathname by stripping basePath.
     */
    _buildOptionUrl(charSlug, optSlug) {
        const root = document.querySelector(this.root);
        const basePath = root ? (root.dataset.basePath || '') : '';
        const prefix   = basePath.replace(/\/$/, '') + '/';
        const pathname = window.location.pathname;

        const filtersPath = pathname.startsWith(prefix)
            ? pathname.slice(prefix.length)
            : '';

        const segments = {};
        for (const seg of filtersPath.replace(/^\/|\/$/g, '').split('/').filter(Boolean)) {
            const eqIdx = seg.indexOf('=');
            if (eqIdx > 0) {
                segments[seg.slice(0, eqIdx)] = seg.slice(eqIdx + 1);
            }
        }

        if (segments[charSlug] !== undefined) {
            const opts = segments[charSlug].split(',').filter(Boolean);
            const idx  = opts.indexOf(optSlug);
            if (idx >= 0) {
                opts.splice(idx, 1);
            } else {
                opts.push(optSlug);
            }
            if (opts.length === 0) {
                delete segments[charSlug];
            } else {
                segments[charSlug] = opts.join(',');
            }
        } else {
            segments[charSlug] = optSlug;
        }

        const parts = Object.entries(segments).map(([k, v]) => `${k}=${v}`);
        return parts.length > 0
            ? basePath.replace(/\/$/, '') + '/' + parts.join('/') + '/'
            : basePath.replace(/\/$/, '') + '/';
    }

    /** Push new URL and dispatch Livewire filter-changed event. */
    _navigate(url) {
        if (!url || url === '#') { return; }

        try {
            const parsed = new URL(url, window.location.origin);

            // Preserve ?sort= from current URL (filter changes must not reset sort)
            const currentSort = new URL(window.location.href).searchParams.get('sort');
            if (currentSort && !parsed.searchParams.has('sort')) {
                parsed.searchParams.set('sort', currentSort);
            }
            // Filter change always resets pagination
            parsed.searchParams.delete('page');

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

    // After each Livewire commit:
    // 1. Sync checkbox .checked property with HTML attribute
    //    (Alpine morph preserves DOM property, causing visual desync)
    // 2. Redraw range sliders
    //    (server updates data-current-min/max; sliders must re-read them)
    Livewire.hook('commit', ({ succeed }) => {
        succeed(() => {
            queueMicrotask(() => {
                document.querySelectorAll('.lw-catalog-facets input[type="checkbox"]').forEach((input) => {
                    input.checked = input.hasAttribute('checked');
                });
                catalogFilter._sliders.forEach((s) => s.redraw());
            });
        });
    });
});

// Re-init after wire:navigate page transitions
document.addEventListener('livewire:navigated', () => {
    catalogFilter.init();
});
