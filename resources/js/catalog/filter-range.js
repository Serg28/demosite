/**
 * CatalogRangeSlider — dual-handle range slider for SEO path-based filter URLs.
 *
 * HTML structure (rendered by Livewire / Blade):
 *
 *   <div class="js-range-slider"
 *        data-char-slug="price"
 *        data-base-path="/catalog/phones"
 *        data-filters-path="color=red/"
 *        data-min="0"    data-max="50000"
 *        data-current-min="0" data-current-max="50000">
 *     <div class="values">
 *       <input type="number" class="minValue">
 *       <input type="number" class="maxValue">
 *     </div>
 *     <div class="range-track"><div class="range"></div></div>
 *     <input type="range" class="minSlider">
 *     <input type="range" class="maxSlider">
 *   </div>
 *
 * On change: builds path URL via the same segment pattern, pushState + Livewire dispatch.
 * Works with wire:navigate (re-init on livewire:navigated).
 */
class CatalogRangeSlider {
    /** @param {Element} container */
    constructor(container) {
        this.container = container;
        this._setup();
    }

    _setup() {
        const c = this.container;
        this.minSlider = c.querySelector('.minSlider');
        this.maxSlider = c.querySelector('.maxSlider');
        this.minInput  = c.querySelector('.minValue');
        this.maxInput  = c.querySelector('.maxValue');
        this.range     = c.querySelector('.range');

        if (!this.minSlider || !this.maxSlider) { return; }

        this._updateTrack();
        this.minSlider.addEventListener('input', () => this._onSlide());
        this.maxSlider.addEventListener('input', () => this._onSlide());
        this.minSlider.addEventListener('change', () => this._commit());
        this.maxSlider.addEventListener('change', () => this._commit());

        if (this.minInput) {
            this.minInput.addEventListener('change', () => this._onInputChange());
        }
        if (this.maxInput) {
            this.maxInput.addEventListener('change', () => this._onInputChange());
        }
    }

    _onSlide() {
        const min = parseInt(this.minSlider.value, 10);
        const max = parseInt(this.maxSlider.value, 10);
        if (min > max) { return; }
        if (this.minInput) { this.minInput.value = min; }
        if (this.maxInput) { this.maxInput.value = max; }
        this._updateTrack();
    }

    _onInputChange() {
        const min = parseInt(this.minInput.value, 10);
        const max = parseInt(this.maxInput.value, 10);
        if (isNaN(min) || isNaN(max) || min > max) { return; }
        this.minSlider.value = min;
        this.maxSlider.value = max;
        this._updateTrack();
        this._commit();
    }

    _commit() {
        const min = parseInt(this.minSlider.value, 10);
        const max = parseInt(this.maxSlider.value, 10);
        const url = this._buildUrl(min, max);
        history.pushState({}, '', url);
        if (typeof Livewire !== 'undefined') {
            Livewire.dispatch('filter-changed', { path: window.location.pathname });
        }
    }

    _updateTrack() {
        if (!this.range) { return; }
        const sliderMin = parseFloat(this.minSlider.min);
        const sliderMax = parseFloat(this.minSlider.max);
        const min = parseFloat(this.minSlider.value);
        const max = parseFloat(this.maxSlider.value);
        const range = sliderMax - sliderMin;
        const leftPct  = ((min - sliderMin) / range) * 100;
        const rightPct = ((sliderMax - max) / range) * 100;
        this.range.style.left  = `${leftPct}%`;
        this.range.style.right = `${rightPct}%`;
    }

    /**
     * Build the new path URL by updating or adding the char-slug=min-max segment.
     */
    _buildUrl(min, max) {
        const basePath     = this.container.dataset.basePath || '';
        const filtersPath  = this.container.dataset.filtersPath || '';
        const charSlug     = this.container.dataset.charSlug || 'price';

        const segments = {};
        for (const seg of filtersPath.replace(/^\/|\/$/g, '').split('/').filter(Boolean)) {
            const eqIdx = seg.indexOf('=');
            if (eqIdx > 0) {
                segments[seg.slice(0, eqIdx)] = seg.slice(eqIdx + 1);
            }
        }

        segments[charSlug] = `${min}-${max}`;

        const parts = Object.entries(segments).map(([k, v]) => `${k}=${v}`);
        return basePath.replace(/\/$/, '') + '/' + parts.join('/') + '/';
    }

    /**
     * Redraw slider values from current data-* attributes (called after Livewire morph).
     */
    redraw() {
        const c = this.container;
        this.minSlider.min = c.dataset.min;
        this.maxSlider.min = c.dataset.min;
        this.minSlider.max = c.dataset.max;
        this.maxSlider.max = c.dataset.max;
        this.minSlider.value = c.dataset.currentMin;
        this.maxSlider.value = c.dataset.currentMax;
        if (this.minInput) { this.minInput.value = c.dataset.currentMin; }
        if (this.maxInput) { this.maxInput.value = c.dataset.currentMax; }
        this._updateTrack();
    }
}

window.CatalogRangeSlider = CatalogRangeSlider;
