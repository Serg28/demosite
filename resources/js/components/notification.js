/**
 * @file notification.js
 * Alpine.js toast-сповіщення. Реєструється через Alpine.data('toast').
 *
 * @version 3.0 — переписано з Web Component на Alpine.js (Tailwind CSS, нуль зовнішніх залежностей)
 * @author  Serg28 tsv.art.com@gmail.com for Linecore
 *
 * @usage Blade (один раз у shop.blade.php):
 *   <x-toast />
 *
 * @usage PHP (Livewire, з трейтом HasNotifications):
 *   $this->notify('Товар додано', 'Успіх', 'success')
 *   $this->notifyError('Щось пішло не так')
 *
 * @usage JS (з будь-якого місця):
 *   window.notify('success', 'Done!')
 *   Livewire.dispatch('notify', { type: 'success', message: 'Done!', title: '' })
 */

const TOAST_DURATION = 5000;

const ICONS = {
    success: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>',
    error:   '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>',
    info:    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
    warning: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>',
};

const COLORS = {
    success: 'bg-green-50 border-green-200 text-green-800',
    error:   'bg-red-50   border-red-200   text-red-800',
    info:    'bg-blue-50  border-blue-200  text-blue-800',
    warning: 'bg-yellow-50 border-yellow-200 text-yellow-800',
};

const ICON_COLORS = {
    success: 'text-green-500',
    error:   'text-red-500',
    info:    'text-blue-500',
    warning: 'text-yellow-500',
};

document.addEventListener('alpine:init', () => {
    Alpine.data('toast', () => ({
        items: [],

        add(type, message, title = '') {
            const id = Date.now();
            this.items.push({ id, type, message, title, visible: true });
            setTimeout(() => this.remove(id), TOAST_DURATION);
        },

        remove(id) {
            const item = this.items.find(n => n.id === id);
            if (item) { item.visible = false; }
            setTimeout(() => {
                this.items = this.items.filter(n => n.id !== id);
            }, 300);
        },

        iconSvg(type)    { return ICONS[type]      ?? ICONS.info; },
        colorClass(type) { return COLORS[type]     ?? COLORS.info; },
        iconColor(type)  { return ICON_COLORS[type] ?? ICON_COLORS.info; },
    }));
});

// Глобальний хелпер для виклику з чистого JS (без Livewire)
window.notify = (type, message, title = '') => {
    window.Livewire?.dispatch('notify', { type, message, title });
};
