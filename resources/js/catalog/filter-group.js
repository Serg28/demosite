/**
 * Alpine.js component: filterGroup
 *
 * Registered component — no JS logic in HTML attributes.
 * Configure via data attributes on the root element:
 *   data-limit="8"  — how many options to show initially
 *
 * Works with wire:navigate (Alpine re-inits on navigation automatically).
 * Alpine state (search, showAll) is preserved across Livewire morphs via wire:key.
 */
document.addEventListener('alpine:init', () => {
    Alpine.data('filterGroup', function () {
        return {
            search: '',
            showAll: false,
            limit: 8,
            total: 0,

            init() {
                this.limit = parseInt(this.$el.dataset.limit ?? 8, 10);
                this.total = parseInt(this.$el.dataset.total ?? 0, 10);
            },

            /**
             * Determines visibility of an option by index and label.
             * Without JS (no Alpine): all options are visible (graceful degradation).
             *
             * @param {number} index - 0-based position in the sorted options list
             * @param {string} label - display text of the option
             */
            isVisible(index, label) {
                const matchesSearch = !this.search ||
                    label.toLowerCase().includes(this.search.toLowerCase());

                // When searching: show all matching (ignore limit/showAll)
                if (this.search) {
                    return matchesSearch;
                }

                return (this.showAll || index < this.limit) && matchesSearch;
            },

            get hasMore() {
                return !this.showAll && this.total > this.limit && !this.search;
            },
        };
    });
});
