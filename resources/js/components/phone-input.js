/**
 * Alpine.js component: phoneInput
 *
 * Registered component — no JS logic in HTML attributes.
 * Usage: x-data="phoneInput('form.phone')"
 *
 * Formats Ukrainian phone numbers: +38 (0XX) XXX-XX-XX
 * Syncs back to Livewire via $wire.set(prop, value) on blur.
 *
 * Supports both flat ('phone') and nested ('form.phone') Livewire props.
 * Watches for server-side resets (e.g., after form submit) and clears the input.
 */
document.addEventListener('alpine:init', () => {
    Alpine.data('phoneInput', (prop) => ({
        init() {
            // Manual dot traversal — safe for nested props like 'form.phone'
            const initial = this._getPropValue();
            if (initial) {
                this.$refs.input.value = this.format(this.extractDigits(String(initial)));
            }

            // Sync DOM input when Livewire resets the property (morphdom won't do it automatically)
            this.$watch('$wire.' + prop, (val) => {
                if (!val) {
                    this.$refs.input.value = '';
                }
            });
        },

        _getPropValue() {
            return prop.split('.').reduce((node, key) => node?.[key], this.$wire) || '';
        },

        extractDigits(val) {
            let d = val.replace(/\D/g, '');
            if (d.startsWith('38')) { d = d.slice(2); }
            return d.slice(0, 10);
        },

        format(digits) {
            if (!digits) { return ''; }
            const d = digits.slice(0, 10);
            let r = '+38 (';
            r += d.slice(0, Math.min(3, d.length));
            if (d.length > 3) { r += ') ' + d.slice(3, 6); }
            if (d.length > 6) { r += '-' + d.slice(6, 8); }
            if (d.length > 8) { r += '-' + d.slice(8, 10); }
            return r;
        },

        onFocus() {
            if (!this.$refs.input.value) {
                this.$refs.input.value = '+38 (';
            }
        },

        onInput(e) {
            const digits = this.extractDigits(e.target.value);
            e.target.value = digits ? this.format(digits) : '';
        },

        onBlur() {
            const digits = this.extractDigits(this.$refs.input.value);
            if (digits.length === 10) {
                this.$wire.set(prop, '+38' + digits);
            } else {
                this.$wire.set(prop, '');
                if (!digits) { this.$refs.input.value = ''; }
            }
        },
    }));
});
