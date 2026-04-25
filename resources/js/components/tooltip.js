class TooltipElement extends HTMLElement {
    constructor() {
        super();
        this.attachShadow({ mode: 'open' });
    }

    connectedCallback() {
        this.render();
        this.setupListeners();
    }

    render() {
        const text = this.getAttribute('text') || this.textContent;
        const position = this.getAttribute('position') || 'top';

        this.shadowRoot.innerHTML = `
            <style>
                :host {
                    --tooltip-bg: rgb(31, 41, 55);
                    --tooltip-text: white;
                    --tooltip-padding: 0.5rem 0.75rem;
                    --tooltip-border-radius: 0.375rem;
                    --tooltip-font-size: 0.875rem;
                    --tooltip-z-index: 1000;

                    position: relative;
                    display: inline-block;
                }

                .tooltip-trigger {
                    cursor: help;
                    border-bottom: 1px dotted currentColor;
                }

                .tooltip-content {
                    position: absolute;
                    background-color: var(--tooltip-bg);
                    color: var(--tooltip-text);
                    padding: var(--tooltip-padding);
                    border-radius: var(--tooltip-border-radius);
                    font-size: var(--tooltip-font-size);
                    white-space: nowrap;
                    z-index: var(--tooltip-z-index);
                    opacity: 0;
                    visibility: hidden;
                    pointer-events: none;
                    transition: opacity 0.2s, visibility 0.2s;
                    max-width: 250px;
                }

                :host([data-visible]) .tooltip-content {
                    opacity: 1;
                    visibility: visible;
                }

                /* Position variations */
                :host([data-position="top"]) .tooltip-content {
                    bottom: 100%;
                    left: 50%;
                    transform: translateX(-50%) translateY(-0.5rem);
                }

                :host([data-visible][data-position="top"]) .tooltip-content {
                    transform: translateX(-50%) translateY(-0.5rem);
                }

                :host([data-position="bottom"]) .tooltip-content {
                    top: 100%;
                    left: 50%;
                    transform: translateX(-50%) translateY(0.5rem);
                }

                :host([data-visible][data-position="bottom"]) .tooltip-content {
                    transform: translateX(-50%) translateY(0.5rem);
                }

                :host([data-position="left"]) .tooltip-content {
                    right: 100%;
                    top: 50%;
                    transform: translateY(-50%) translateX(-0.5rem);
                }

                :host([data-visible][data-position="left"]) .tooltip-content {
                    transform: translateY(-50%) translateX(-0.5rem);
                }

                :host([data-position="right"]) .tooltip-content {
                    left: 100%;
                    top: 50%;
                    transform: translateY(-50%) translateX(0.5rem);
                }

                :host([data-visible][data-position="right"]) .tooltip-content {
                    transform: translateY(-50%) translateX(0.5rem);
                }
            </style>

            <slot class="tooltip-trigger" name="trigger">Hover me</slot>
            <div class="tooltip-content">${text}</div>
        `;

        this.setAttribute('data-position', position);
    }

    setupListeners() {
        const slot = this.shadowRoot.querySelector('slot');
        const trigger = slot?.assignedElements()[0] || this;

        trigger.addEventListener('mouseenter', () => this.show());
        trigger.addEventListener('mouseleave', () => this.hide());
        trigger.addEventListener('focus', () => this.show());
        trigger.addEventListener('blur', () => this.hide());
    }

    show() {
        this.setAttribute('data-visible', '');
    }

    hide() {
        this.removeAttribute('data-visible');
    }
}

customElements.define('app-tooltip', TooltipElement);
