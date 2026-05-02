import './bootstrap';

// SEO: remove ?page=1 from URL
import './base/remove-first-page-from-url';

// GA4: select_item, view_item_list
import './base/analytic';

// Toast notifications (Alpine.js, замінює Web Component)
import './components/notification';

// Tooltip Web Component
import './components/tooltip';

// Lazy image loading (IntersectionObserver + MutationObserver)
import './base/lazy-loading-img';

// Modal: виклик Livewire-модалок через [data-js-modal]
import './base/livewire-modal';

// Catalog: filter group Alpine component (registered before Alpine init)
import './catalog/filter-group';

// Catalog: range slider class (must load before filter.js)
import './catalog/filter-range';

// Catalog: main filter manager (CatalogFilter class + bootstrap)
import './catalog/filter';

// Catalog: "Show more" — fetch HTML partial + replaceState
import './catalog/load-more';

// Catalog: numbered pagination — SPA intercept, no full reload
import './catalog/pagination';

// Catalog: sort links — SPA intercept, preserves filter path
import './catalog/sort';

// Cart: add-to-cart handler + GA4 analytics
import './cart/basket';

// Cart: open-cart-sidebar via [data-js-open-cart] click
document.addEventListener('click', (e) => {
    if (e.target.closest('[data-js-open-cart]')) {
        e.preventDefault();
        window.Livewire?.dispatch('open-cart-sidebar');
    }
});

// Alpine.js & Livewire are included by Livewire
