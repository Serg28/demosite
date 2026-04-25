class NotificationsPopup extends HTMLElement {
    constructor() {
        super();
        this.messages = [];
        this.attachShadow({ mode: 'open' });
    }

    connectedCallback() {
        this.render();
        Livewire.on('notify', this.addNotification.bind(this));
    }

    addNotification(event) {
        const { type, title = '', message } = event;
        const newMessage = { type, title, message, key: Date.now() };
        this.messages.push(newMessage);
        this.render();

        setTimeout(() => {
            this.removeNotification(newMessage.key);
        }, 5000);
    }

    removeNotification(key) {
        this.messages = this.messages.filter(msg => msg.key !== key);
        this.render();
    }

    render() {
        this.shadowRoot.innerHTML = `
            <style>
                @import "${this.getAttribute('styles-path')}"
            </style>
            <div class="notification">
                ${this.messages.map(msg => `
                    <div class="popup bg-${msg.type}"
                         style="transition: opacity 0.3s; opacity: 1;">
                        <div class="icon">
                            <svg class="${msg.type}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                ${this.getIconSVG(msg.type)}
                            </svg>
                        </div>
                        <div class="content">
                            <p class="notification-title">${msg.title}</p>
                            <p class="notification-message">${msg.message}</p>
                        </div>
                        <div class="closer">
                            <button onclick="this.getRootNode().host.removeNotification(${msg.key})">
                                <svg class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                `).join('')}
            </div>
        `;
    }

    getIconSVG(type) {
        switch (type) {
            case 'success':
                return `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />`;
            case 'error':
                return `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />`;
            case 'info':
                return `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />`;
            case 'warning':
                return `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.618 5.984A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016zM12 9v2m0 4h.01" />`;
            default:
                return '';
        }
    }
}

if (!customElements.get('notifications-popup')) {
    customElements.define('notifications-popup', NotificationsPopup);
}
