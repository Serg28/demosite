/**
 * Catalog filter URL manager.
 * Listens for Livewire's updateFilterUrl event and pushes SEO-friendly slug-based
 * query params to browser history without page reload.
 * On popstate (Back/Forward), reloads so server re-resolves filter state from URL.
 */
document.addEventListener('livewire:init', () => {
    Livewire.on('updateFilterUrl', ({ params }) => {
        const url = new URL(window.location);

        // Strip all existing filter params (everything except page/system params)
        const systemParams = new Set(['page']);
        [...url.searchParams.keys()].forEach((key) => {
            if (!systemParams.has(key)) {
                url.searchParams.delete(key);
            }
        });

        // Reset pagination on filter change
        url.searchParams.delete('page');

        // Apply new filter params
        Object.entries(params).forEach(([key, value]) => {
            url.searchParams.set(key, String(value));
        });

        history.pushState({ filterParams: params }, '', url.toString());
    });

    // Full reload on Back/Forward so server re-resolves filter state from URL
    window.addEventListener('popstate', () => {
        window.location.reload();
    });
});
