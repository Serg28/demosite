/**
 * Alpine.js component: filterGroup
 *
 * Manages show-more limit and text search for a facet group.
 * Configure via data attributes on the root element:
 *   data-limit="8"  — how many options to show initially
 *
 * Each option label uses:
 *   x-show="search ? $el.querySelector('a > span')?.textContent?.toLowerCase().includes(search.toLowerCase()) : (showAll || INDEX < limit)"
 *
 * Works with wire:navigate (Alpine re-inits on navigation automatically).
 */
document.addEventListener('alpine:init', () => {
    Alpine.data('filterGroup', function () {
        return {
            search: '',
            showAll: false,
            limit: 8,
            total: 0,
            labelShow: '',
            labelCollapse: '',

            init() {
                this.limit         = parseInt(this.$el.dataset.limit ?? 8, 10);
                this.total         = parseInt(this.$el.dataset.total ?? 0, 10);
                this.labelShow     = this.$el.dataset.labelShow ?? 'Показати всі';
                this.labelCollapse = this.$el.dataset.labelCollapse ?? 'Згорнути';
            },

            get toggleLabel() {
                return this.showAll
                    ? this.labelCollapse
                    : `${this.labelShow} (${this.total})`;
            },
        };
    });
});
