import './bootstrap';

// SEO: remove ?page=1 from URL
import './base/remove-first-page-from-url';

// Web Components for UI
import './components/notification';
import './components/tooltip';
import './components/lazy-image';

// Catalog: filter group Alpine component (registered before Alpine init)
import './catalog/filter-group';

// Catalog: range slider class (must load before filter.js)
import './catalog/filter-range';

// Catalog: main filter manager (CatalogFilter class + bootstrap)
import './catalog/filter';

// Catalog: "Show more" — fetch HTML partial + replaceState
import './catalog/load-more';

// Alpine.js & Livewire are included by Livewire
