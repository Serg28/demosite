/**
 * GA4 Analytics — select_item, view_item_list, add_to_cart, remove_from_cart.
 * Портовано з linecore-demo/resources/dev/js/base/analytic.js
 */

function ga4Push(data) {
    window.dataLayer = window.dataLayer || [];
    dataLayer.push(data);
}

/** view_item_list: товари що потрапили у viewport */
function checkGA4Elements() {
    const scrollTop    = window.scrollY;
    const windowHeight = window.innerHeight;
    const cards        = document.querySelectorAll('[data-ga-push="0"]');
    const items        = [];

    cards.forEach((el) => {
        const rect = el.getBoundingClientRect();
        const inView = rect.top >= 0 && rect.bottom <= windowHeight + scrollTop;
        if (!inView) { return; }

        el.setAttribute('data-ga-push', '1');

        const listName = el.closest('[data-item_list_name]')?.getAttribute('data-item_list_name') ?? '';
        const nameEl   = el.querySelector('[data-ga4-click]');

        items.push({
            item_id:        el.getAttribute('data-code') ?? el.dataset.id,
            item_name:      nameEl?.textContent?.trim() ?? '',
            price:          el.getAttribute('data-sum') ?? '',
            item_list_name: listName,
        });
    });

    if (items.length) {
        ga4Push({ ecommerce: null });
        ga4Push({ event: 'view_item_list', ecommerce: { items } });
    }
}

/** select_item: клік по назві/посиланню товару */
function bindSelectItem() {
    document.addEventListener('click', (e) => {
        const link = e.target.closest('[data-ga4-click]');
        if (!link) { return; }

        const card     = link.closest('[data-ga-push]');
        const listName = card?.closest('[data-item_list_name]')?.getAttribute('data-item_list_name') ?? '';

        if (typeof dataLayer === 'undefined') { return; }

        ga4Push({ ecommerce: null });
        ga4Push({
            event: 'select_item',
            ecommerce: {
                items: [{
                    item_id:        card?.getAttribute('data-code') ?? '',
                    item_name:      link.textContent?.trim() ?? '',
                    price:          card?.getAttribute('data-sum') ?? '',
                    item_list_name: listName,
                }],
            },
        });

        /** Зберігаємо list_name у cookie для product-page */
        const productId = card?.dataset.id;
        if (productId && listName) {
            try {
                const raw = decodeURIComponent(
                    document.cookie.split('; ').find((c) => c.startsWith('analitic_list_name='))?.split('=')[1] ?? '{}'
                );
                const obj = JSON.parse(raw);
                obj[productId] = listName;
                const exp = new Date(Date.now() + 86400_000).toUTCString();
                document.cookie = `analitic_list_name=${encodeURIComponent(JSON.stringify(obj))}; expires=${exp}; path=/; SameSite=Lax`;
            } catch {}
        }
    });
}

/** Ініціалізація після DOMContentLoaded та після Livewire-навігації */
function initAnalytics() {
    if (typeof dataLayer === 'undefined') { return; }
    checkGA4Elements();
}

document.addEventListener('DOMContentLoaded', initAnalytics);
window.addEventListener('scroll', () => {
    if (typeof dataLayer !== 'undefined') { checkGA4Elements(); }
}, { passive: true });

document.addEventListener('livewire:navigated', initAnalytics);

document.addEventListener('livewire:init', () => {
    Livewire.on('form-submitted', (event) => {
        if (typeof dataLayer === 'undefined') { return; }
        ga4Push({
            event:      'sent_form',
            eventModel: { form_id: event.component },
        });
    });
});

bindSelectItem();
