/**
 * Catalog sort interceptor.
 *
 * Handles both:
 * - [data-js-sort] link clicks (legacy button approach)
 * - [data-js-sort-select] select change events (current design)
 *
 * Reads current window.location.pathname (which includes SEO filter segments
 * like /color=red/price=100-500/), updates only the ?sort= query param,
 * and dispatches Livewire sort-changed event.
 */

function applySortKey(sortKey) {
    const current = new URL(window.location.href);

    if (sortKey) {
        current.searchParams.set('sort', sortKey);
    } else {
        current.searchParams.delete('sort');
    }
    // Sort change always resets to page 1
    current.searchParams.delete('page');

    history.pushState({}, '', current.pathname + (current.search || ''));

    if (typeof Livewire !== 'undefined') {
        Livewire.dispatch('sort-changed', { sortKey });
    }
}

// Select-based sort (current design)
document.addEventListener('change', (e) => {
    const select = e.target.closest('[data-js-sort-select]');
    if (!select) { return; }
    applySortKey(select.value);
});

// Link-based sort (legacy, keep for backward compatibility)
document.addEventListener('click', (e) => {
    const link = e.target.closest('[data-js-sort]');
    if (!link) { return; }

    e.preventDefault();
    applySortKey(link.dataset.jsSort);
});
