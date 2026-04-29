/**
 * Removes ?page=1 from the URL on load and after each Livewire morph,
 * preventing duplicate canonical pages in search engines.
 */
(function () {
    'use strict';

    if (window.__pageParamCleaned) return;
    window.__pageParamCleaned = true;

    const clean = () => {
        try {
            const url = new URL(location.href);
            if (url.searchParams.get('page') === '1') {
                url.searchParams.delete('page');
                history.replaceState(null, '', url);
            }
        } catch (e) {
            console.error('Error cleaning page param:', e);
        }
    };

    clean();

    document.addEventListener('livewire:init', () => {
        Livewire.hook('morphed', clean);
    });
})();
