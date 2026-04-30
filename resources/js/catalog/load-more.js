/**
 * "Show more" — fetches next page as server-rendered HTML fragments and:
 *   - appends product cards to the grid
 *   - replaces the pagination block (#js-pagination) with fresh server HTML
 * Updates ?page=N in URL via history.replaceState (no reload).
 * Works via event delegation on document.
 */
document.addEventListener('click', async (e) => {
    const btn = e.target.closest('.js-load-more');
    if (!btn) return;

    e.preventDefault();

    const grid = document.getElementById('js-product-grid');
    if (!grid) return;

    const nextPage = parseInt(btn.dataset.nextPage, 10);
    const apiUrl   = grid.dataset.apiUrl;

    const params = new URLSearchParams(window.location.search);
    params.set('page', nextPage);
    params.set('page_base', window.location.pathname);

    const filterPath = _extractFilterPath(grid.dataset.catalogUrl);
    if (filterPath) {
        params.set('filter_path', filterPath);
    }

    btn.disabled = true;
    btn.querySelector('.js-load-more-text').textContent = '...';

    try {
        const html = await fetch(`${apiUrl}?${params.toString()}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
        }).then((r) => {
            if (!r.ok) throw new Error(`HTTP ${r.status}`);
            return r.text();
        });

        const doc = new DOMParser().parseFromString(html, 'text/html');

        const productsFragment  = doc.querySelector('[data-fragment="products"]');
        const paginationFragment = doc.querySelector('[data-fragment="pagination"]');

        if (productsFragment) {
            grid.insertAdjacentHTML('beforeend', productsFragment.innerHTML);
        }

        if (paginationFragment) {
            const paginationEl = document.querySelector('[data-js-paginator]');
            if (paginationEl) {
                paginationEl.outerHTML = paginationFragment.innerHTML;
            }
        }

        // Update ?page in URL (skip page=1 — remove-first-page-from-url handles it)
        const urlParams = new URLSearchParams(window.location.search);
        if (nextPage > 1) {
            urlParams.set('page', nextPage);
        } else {
            urlParams.delete('page');
        }
        const newSearch = urlParams.toString();
        history.replaceState(null, '', window.location.pathname + (newSearch ? '?' + newSearch : ''));

    } catch {
        // Restore button on error — get fresh reference since pagination may have been replaced
        const freshBtn = document.querySelector('.js-load-more');
        if (freshBtn) {
            freshBtn.disabled = false;
        }
    }
});

function _extractFilterPath(catalogUrl) {
    if (!catalogUrl) return '';

    try {
        const catalogPath = new URL(catalogUrl).pathname.replace(/\/$/, '');
        const path        = window.location.pathname;

        if (!path.startsWith(catalogPath)) return '';

        const rest = path.slice(catalogPath.length);
        return rest.startsWith('/') ? rest.slice(1) : rest;
    } catch {
        return '';
    }
}
