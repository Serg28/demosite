class RatingStars extends HTMLElement {
    constructor() {
        super();
        this.attachShadow({ mode: 'open' });
    }

    connectedCallback() {
        const rating = parseInt(this.textContent.trim(), 10) || 0;

        // Получаем пользовательский класс и атрибут размера
        const customClass = this.getAttribute('class') || '';
        const size = this.getAttribute('size') === 'big' ? 24 : 16;

        // Создаем шаблон со стилями и HTML, включая пользовательский класс и размер
        this.shadowRoot.innerHTML = `
            <style>
                .stars {
                    display: flex;
                    align-items: center;
                }
                .stars img {
                    width: ${size}px;
                    height: ${size}px;
                }
            </style>
            <span class="stars ${customClass}">
                ${Array.from({ length: 5 }, (_, i) =>
            `<img src="${i < rating ? '/assets/images/star-full.svg' : '/assets/images/star-empty.svg'}" alt="">`
        ).join('')}
            </span>
        `;

        // Показываем компонент после его инициализации
        this.style.visibility = 'visible';
    }
}

if (!customElements.get('rating-stars')) {
    customElements.define('rating-stars', RatingStars);
}
