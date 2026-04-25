//Маска номера телефона livewire-компонентов формы.
//Сейчас настроена на поддержку украинских, польских и американских номеров. Можно добавить другие
//Для активации нужно в поле телефона добавить x-data="phoneInput"

document.addEventListener('alpine:init', () => {
    Alpine.data('phoneInput', () => ({
        phone: null,
        init() {
            this.phone = this.$el;

            const maskOptions = [
                { mask: '+38(000)000-00-00' },
                { mask: '+1(000)000-0000' },
                { mask: '+48(000)000-000' },
            ];

            const phoneMask = IMask(this.phone, {
                mask: maskOptions,
                dispatch: function (appended, dynamicMasked) {
                    const number = (dynamicMasked.value + appended).replace(/\D/g, '');

                    if (number.startsWith('38')) {
                        return dynamicMasked.compiledMasks[0];
                    } else if (number.startsWith('1')) {
                        return dynamicMasked.compiledMasks[1];
                    } else if (number.startsWith('48')) {
                        return dynamicMasked.compiledMasks[2];
                    }
                    return dynamicMasked.compiledMasks[0]; // default to first mask
                }
            });

            this.phone.addEventListener('input', function () {
                phoneMask.updateValue();
            });
        }
    }));
});