/**
 * CatalogRangeSlider — dual-handle range slider for SEO path-based filter URLs.
 *
 * HTML structure (rendered by Livewire / Blade):
 *
 *   <div class="js-range-slider"
 *        data-char-slug="price"
 *        data-base-path="/catalog/phones"
 *        data-min="0"    data-max="50000"
 *        data-current-min="0" data-current-max="50000">
 *     <div class="values">
 *       <input type="number" class="minValue">
 *       <input type="number" class="maxValue">
 *     </div>
 *     <div class="relative h-8 mx-1 my-1">         ← the track container
 *       <div class="range-track">
 *         <div class="range"></div>
 *       </div>
 *       <div class="minHandle"></div>
 *       <div class="maxHandle"></div>
 *       <input type="range" class="minSlider z-20 opacity-0">
 *       <input type="range" class="maxSlider z-20 opacity-0">
 *     </div>
 *   </div>
 *
 * Dual-thumb note: both range inputs are stacked (absolute inset-0). The one
 * rendered later in DOM wins pointer events by stacking order. We fix this with a
 * mousemove/touchstart listener on the track container that raises the z-index of
 * whichever thumb is closer to the pointer — BEFORE the click lands.
 *
 * On change: builds path URL via the same segment pattern, pushState + Livewire dispatch.
 * Safe to call redraw() after Livewire morph — re-queries children, rebinds events.
 */
class CatalogRangeSlider {
    /** @param {Element} container */
    constructor(container) {
        this.container = container;
        this._debounce  = null;
        this._trackEl   = null;

        // Bound handlers stored for clean unbind
        this._onSlideHandler          = () => this._onSlide();
        this._onCommitHandler         = () => this._commit();
        this._onInputHandler          = () => this._onInputChange();
        this._onMoveHandler           = (e) => this._routeZIndex(e.clientX);
        this._onTouchStartHandler     = (e) => { if (e.touches[0]) { this._routeZIndex(e.touches[0].clientX); } };

        this._setup();
    }

    // ─── Init ────────────────────────────────────────────────────────────────

    _setup() {
        this._findElements();
        if (!this.minSlider || !this.maxSlider) { return; }

        // minSlider starts on top: left thumb reachable at position 0%
        this.minSlider.style.zIndex = '25';
        this.maxSlider.style.zIndex = '20';

        this._updateTrack();
        this._bindEvents();
    }

    _findElements() {
        const c = this.container;
        this.minSlider = c.querySelector('.minSlider');
        this.maxSlider = c.querySelector('.maxSlider');
        this.minInput  = c.querySelector('.minValue');
        this.maxInput  = c.querySelector('.maxValue');
        this.range     = c.querySelector('.range');
        this.minHandle = c.querySelector('.minHandle');
        this.maxHandle = c.querySelector('.maxHandle');
    }

    _bindEvents() {
        if (!this.minSlider || !this.maxSlider) { return; }

        this.minSlider.addEventListener('input',  this._onSlideHandler);
        this.maxSlider.addEventListener('input',  this._onSlideHandler);
        this.minSlider.addEventListener('change', this._onCommitHandler);
        this.maxSlider.addEventListener('change', this._onCommitHandler);

        if (this.minInput) { this.minInput.addEventListener('change', this._onInputHandler); }
        if (this.maxInput) { this.maxInput.addEventListener('change', this._onInputHandler); }

        // Track-level listeners: route z-index before click lands on either slider
        this._trackEl = this.container.querySelector('.relative');
        if (this._trackEl) {
            this._trackEl.addEventListener('mousemove', this._onMoveHandler);
            this._trackEl.addEventListener('touchstart', this._onTouchStartHandler, { passive: true });
        }
    }

    _unbindEvents() {
        if (this.minSlider) {
            this.minSlider.removeEventListener('input',  this._onSlideHandler);
            this.minSlider.removeEventListener('change', this._onCommitHandler);
        }
        if (this.maxSlider) {
            this.maxSlider.removeEventListener('input',  this._onSlideHandler);
            this.maxSlider.removeEventListener('change', this._onCommitHandler);
        }
        if (this.minInput)  { this.minInput.removeEventListener('change', this._onInputHandler); }
        if (this.maxInput)  { this.maxInput.removeEventListener('change', this._onInputHandler); }

        if (this._trackEl) {
            this._trackEl.removeEventListener('mousemove', this._onMoveHandler);
            this._trackEl.removeEventListener('touchstart', this._onTouchStartHandler);
            this._trackEl = null;
        }
    }

    // ─── Z-index routing ─────────────────────────────────────────────────────

    /**
     * Called on mousemove / touchstart — raises the z-index of whichever thumb is
     * closer to the pointer position. Runs BEFORE the click event, so the correct
     * slider is already on top when pointerdown fires.
     */
    _routeZIndex(clientX) {
        if (!this._trackEl) { return; }

        const rect  = this._trackEl.getBoundingClientRect();
        const pct   = Math.max(0, Math.min(1, (clientX - rect.left) / rect.width));
        const rMin  = parseFloat(this.minSlider.min);
        const rMax  = parseFloat(this.minSlider.max);
        const total = rMax - rMin;
        if (total <= 0) { return; }

        const hoverVal = rMin + pct * total;
        const minVal   = parseFloat(this.minSlider.value);
        const maxVal   = parseFloat(this.maxSlider.value);

        if (Math.abs(hoverVal - minVal) <= Math.abs(hoverVal - maxVal)) {
            this.minSlider.style.zIndex = '25';
            this.maxSlider.style.zIndex = '20';
        } else {
            this.minSlider.style.zIndex = '20';
            this.maxSlider.style.zIndex = '25';
        }
    }

    // ─── Slide interaction ───────────────────────────────────────────────────

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

        clearTimeout(this._debounce);
        this._debounce = setTimeout(() => {
            if (typeof Livewire !== 'undefined') {
                Livewire.dispatch('filter-changed', { path: window.location.pathname });
            }
        }, 600);
    }

    // ─── Visual track update ─────────────────────────────────────────────────

    _updateTrack() {
        if (!this.range) { return; }

        const sliderMin = parseFloat(this.minSlider.min);
        const sliderMax = parseFloat(this.minSlider.max);
        const min       = parseFloat(this.minSlider.value);
        const max       = parseFloat(this.maxSlider.value);
        const total     = sliderMax - sliderMin;
        if (total <= 0) { return; }

        const leftPct  = ((min - sliderMin) / total) * 100;
        const rightPct = ((sliderMax - max) / total) * 100;

        this.range.style.left  = `${leftPct}%`;
        this.range.style.right = `${rightPct}%`;

        if (this.minHandle) { this.minHandle.style.left = `${leftPct}%`; }
        if (this.maxHandle) { this.maxHandle.style.left = `${100 - rightPct}%`; }
    }

    // ─── Redraw after Livewire morph ─────────────────────────────────────────

    /**
     * Re-queries DOM children from container, updates values from data-* attributes,
     * rebinds events. Safe to call after Livewire morphing.
     */
    redraw() {
        this._unbindEvents();
        this._findElements();
        if (!this.minSlider || !this.maxSlider) { return; }

        const c = this.container;
        this.minSlider.min   = c.dataset.min;
        this.maxSlider.min   = c.dataset.min;
        this.minSlider.max   = c.dataset.max;
        this.maxSlider.max   = c.dataset.max;
        this.minSlider.value = c.dataset.currentMin;
        this.maxSlider.value = c.dataset.currentMax;
        if (this.minInput) { this.minInput.value = c.dataset.currentMin; }
        if (this.maxInput) { this.maxInput.value = c.dataset.currentMax; }

        // Restore initial z-order (min on top — left end accessible after redraw)
        this.minSlider.style.zIndex = '25';
        this.maxSlider.style.zIndex = '20';

        this._updateTrack();
        this._bindEvents();
    }

    // ─── URL building ────────────────────────────────────────────────────────

    /**
     * Build the new path URL by updating or adding the char-slug=min-max segment.
     */
    _buildUrl(min, max) {
        const basePath = this.container.dataset.basePath || '';
        const charSlug = this.container.dataset.charSlug || 'price';

        const pathname    = window.location.pathname;
        const prefix      = basePath.replace(/\/$/, '') + '/';
        const filtersPath = pathname.startsWith(prefix) ? pathname.slice(prefix.length) : '';

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
}

window.CatalogRangeSlider = CatalogRangeSlider;
