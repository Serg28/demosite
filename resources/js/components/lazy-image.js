class LazyImageElement extends HTMLElement {
    constructor() {
        super();
        this.observer = null;
    }

    connectedCallback() {
        const src = this.getAttribute('src');
        const alt = this.getAttribute('alt') || '';
        const placeholder = this.getAttribute('placeholder') || 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 300"%3E%3Crect fill="%23e5e7eb" width="400" height="300"/%3E%3C/svg%3E';

        if (!src) {
            console.error('LazyImage: src attribute is required');
            return;
        }

        // Create img element
        const img = document.createElement('img');
        img.src = placeholder;
        img.alt = alt;
        img.className = 'lazy-img';
        img.style.transition = 'opacity 0.3s ease-in-out';

        // Store actual src
        img.dataset.src = src;

        this.appendChild(img);

        // Setup Intersection Observer for lazy loading
        if ('IntersectionObserver' in window) {
            this.observer = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        this.loadImage(img);
                        this.observer.unobserve(entry.target);
                    }
                });
            }, { rootMargin: '50px' });

            this.observer.observe(this);
        } else {
            // Fallback: load immediately if IntersectionObserver not supported
            this.loadImage(img);
        }
    }

    loadImage(img) {
        const src = img.dataset.src;
        const newImg = new Image();

        newImg.onload = () => {
            img.src = src;
            img.classList.add('loaded');
            img.style.opacity = '1';
        };

        newImg.onerror = () => {
            img.classList.add('error');
            console.error(`Failed to load image: ${src}`);
        };

        newImg.src = src;
    }

    disconnectedCallback() {
        if (this.observer) {
            this.observer.disconnect();
        }
    }
}

customElements.define('app-lazy-image', LazyImageElement);
