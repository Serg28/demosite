
function initCaptcha(action, hiddenInput){
    grecaptcha.ready(function() {
        grecaptcha.execute(window.public_key, {action: action})
        .then(function(token) {
            const signUpCapthaInput = document.getElementById(hiddenInput);
            if(signUpCapthaInput){
                signUpCapthaInput.value = token;
                if (typeof Livewire !== 'undefined') {
                    Livewire.dispatch('recaptcha-changed', {value: token, hiddenInput: hiddenInput, formId: action});
                }
            }
        })
    });
}

function reinitCaptcha(action, hiddenInput){
    grecaptcha.execute(window.public_key, {action: action})
        .then(function(token) {
            const signUpCapthaInput = document.getElementById(hiddenInput);
            if(signUpCapthaInput){
                signUpCapthaInput.value = token;
                if (typeof Livewire !== 'undefined') {
                    Livewire.dispatch('recaptcha-changed', {value: token, hiddenInput: hiddenInput, formId: action});
                }
            }
        })
}
