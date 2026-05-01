/**
 * Catalog sort interceptor.
 *
 * Intercepts clicks on [data-js-sort] elements, reads current window.location.pathname
 * (which includes SEO filter segments like /color=red/price=100-500/),
 * updates only the ?sort= query param, and dispatches Livewire sort-changed event.
 *
 * This avoids the staleness problem of server-generated sort URLs
 * which are computed at mount time and become outdated after JS filter navigation.
 */
document.addEventListener('click', (e) => {
    const link = e.target.closest('[data-js-sort]');
    if (!link) { return; }

    e.preventDefault();

    const sortKey = link.dataset.jsSort; // '' = default sort, 'priceup' etc = named sort
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
});
