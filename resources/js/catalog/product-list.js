/**
 * Alpine.js data component for catalog product list.
 * Config is read from element's data-config attribute (HTML-encoded JSON)
 * to avoid breaking x-data when filters contain double quotes.
 */
window.catalogProductList = function (el) {
    const readConfig = () => JSON.parse(el.dataset.config || '{}');
    const cfg = readConfig();

    return {
        extraProducts: [],
        hasMore: cfg.hasMore ?? false,
        nextPage: cfg.nextPage ?? 2,
        loading: false,

        init() {
            // product-list-reset fires AFTER Livewire updates DOM,
            // so dataset.config already has new values at this point.
            this.$el.addEventListener('product-list-reset', () => {
                const newCfg = readConfig();
                this.extraProducts = [];
                this.hasMore = newCfg.hasMore ?? false;
                this.nextPage = newCfg.nextPage ?? 2;
            });
        },

        async loadMore() {
            if (this.loading || !this.hasMore) {
                return;
            }

            this.loading = true;
            const liveCfg = readConfig();

            try {
                const params = new URLSearchParams();
                params.set('page', this.nextPage);
                params.set('per_page', liveCfg.perPage || 24);
                params.set('sort_by', liveCfg.sortBy || 'priority');
                params.set('sort_dir', liveCfg.sortDir || 'desc');
                params.set('filters[category_id]', liveCfg.categoryId);

                const filters = liveCfg.filters || {};

                if (filters.min_price) {
                    params.set('filters[min_price]', filters.min_price);
                }
                if (filters.max_price) {
                    params.set('filters[max_price]', filters.max_price);
                }
                if (filters.characteristics) {
                    Object.entries(filters.characteristics).forEach(([charId, optIds]) => {
                        (optIds || []).forEach((optId) => {
                            params.append(`filters[characteristics][${charId}][]`, optId);
                        });
                    });
                }

                const response = await fetch(`/api/v1/catalog/products?${params.toString()}`);

                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }

                const data = await response.json();

                this.extraProducts.push(...(data.products || []));
                this.nextPage++;
                this.hasMore = data.has_more ?? false;
            } catch (error) {
                console.error('Catalog load more failed:', error);
            } finally {
                this.loading = false;
            }
        },

        getTitle(product) {
            if (product.title && typeof product.title === 'object') {
                return product.title.ua || product.title.ru || '';
            }
            return product.title || '';
        },

        formatPrice(price) {
            return new Intl.NumberFormat('uk-UA', { maximumFractionDigits: 0 }).format(price) + ' грн';
        },
    };
};
