@props([
    'wireModel' => '',
    'label'     => '',
    'required'  => false,
])

@once
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('phoneInput', (prop) => ({
        init() {
            const val = this.$wire[prop] || '';
            if (val) {
                this.$refs.input.value = this.format(this.extractDigits(String(val)));
            }
        },

        extractDigits(val) {
            let d = val.replace(/\D/g, '');
            if (d.startsWith('38')) d = d.slice(2);
            return d.slice(0, 10);
        },

        format(digits) {
            if (!digits) return '';
            let d = digits.slice(0, 10);
            let r = '+38 (';
            r += d.slice(0, Math.min(3, d.length));
            if (d.length > 3) r += ') ' + d.slice(3, 6);
            if (d.length > 6) r += '-' + d.slice(6, 8);
            if (d.length > 8) r += '-' + d.slice(8, 10);
            return r;
        },

        onFocus() {
            if (!this.$refs.input.value) {
                this.$refs.input.value = '+38 (';
            }
        },

        onInput(e) {
            const digits = this.extractDigits(e.target.value);
            // Directly update DOM value — :value binding doesn't update after user interaction
            e.target.value = digits ? this.format(digits) : '';
        },

        onBlur() {
            const digits = this.extractDigits(this.$refs.input.value);
            if (digits.length === 10) {
                this.$wire.set(prop, '+38' + digits);
            } else {
                this.$wire.set(prop, '');
                if (!digits) this.$refs.input.value = '';
            }
        },
    }));
});
</script>
@endonce

<div x-data="phoneInput('{{ $wireModel }}')">
    @if($label)
        <label class="text-sm font-medium mb-1.5 block">
            {!! $label !!}
        </label>
    @endif
    <input type="tel"
           x-ref="input"
           @focus="onFocus()"
           @input="onInput($event)"
           @blur="onBlur()"
           class="field @error($wireModel) border-red-400 @enderror"
           placeholder="+38 (0__) ___-__-__"
           autocomplete="tel">
    @error($wireModel)
        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>
