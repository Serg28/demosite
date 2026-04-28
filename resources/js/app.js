import './bootstrap';

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

// Alpine.js & Livewire are included by Livewire
