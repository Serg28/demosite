/**
 * Alpine.js component: emailInput
 *
 * Registered component — no JS logic in HTML attributes.
 * Usage: x-data="emailInput('form.email', 'Невірний формат email')"
 *
 * Validates email format client-side with 500ms debounce.
 * Syncs back to Livewire via $wire.set(prop, value) on blur.
 *
 * Supports both flat ('email') and nested ('form.email') Livewire props.
 * Watches for server-side resets (e.g., after form submit) and clears the input.
 */
document.addEventListener('alpine:init', () => {
    Alpine.data('emailInput', (prop, errorMsg) => ({
        value: '',
        clientError: '',
        _timer: null,
        emailRegex: /^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/,

        init() {
            const initial = this._getPropValue();
            if (initial) {
                this.value = String(initial);
            }

            // Sync DOM value when Livewire resets the property (morphdom won't do it automatically)
            this.$watch('$wire.' + prop, (val) => {
                if (!val) {
                    this.value = '';
                    this.clientError = '';
                }
            });
        },

        _getPropValue() {
            return prop.split('.').reduce((node, key) => node?.[key], this.$wire) || '';
        },

        onInput(e) {
            // Strip non-ASCII / non-email characters immediately (Cyrillic, emoji, etc.)
            const filtered = e.target.value.replace(/[^\x20-\x7E]/g, '');
            if (e.target.value !== filtered) {
                e.target.value = filtered;
                this.value     = filtered;
            }
            // Debounced validation
            clearTimeout(this._timer);
            this._timer = setTimeout(() => this.validate(), 500);
        },

        validate() {
            if (!this.value) { this.clientError = ''; return; }
            this.clientError = this.emailRegex.test(this.value) ? '' : errorMsg;
        },

        onBlur() {
            this.validate();
            this.$wire.set(prop, this.value || '');
        },
    }));
});
