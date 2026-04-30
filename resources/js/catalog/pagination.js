/**
 * Generic SPA-pagination interceptor.
 *
 * Works with any wrapper that has [data-js-paginator] attribute.
 * On numbered page link click:
 *   1. Prevents full reload, pushes new URL to history
 *   2. Dispatches Livewire 'page-changed' event (globally)
 *      — any component with #[On('page-changed')] will respond
 *   3. Smooth-scrolls to [data-js-product-grid] or [id="js-product-grid"]
 *
 * For bots / no-JS: <a href> links still work as regular navigation.
 */
document.addEventListener('click', (e) => {
    const link = e.target.closest('[data-js-paginator] a[href]');
    if (!link) { return; }

    e.preventDefault();

    let url;
    try {
        url = new URL(link.href, window.location.origin);
    } catch {
        return;
    }

    const page = parseInt(url.searchParams.get('page') || '1', 10);

    history.pushState({}, '', url.pathname + (url.search || ''));

    if (typeof Livewire !== 'undefined') {
        Livewire.dispatch('page-changed', { page });
    }

    const grid = document.querySelector('[data-js-product-grid], #js-product-grid');
    if (grid) {
        grid.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
});
