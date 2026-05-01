import './bootstrap';

// SEO: remove ?page=1 from URL
import './base/remove-first-page-from-url';

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

// Alpine.js & Livewire are included by Livewire
