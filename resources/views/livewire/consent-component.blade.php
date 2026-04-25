<div>
@if (!$consentGiven)
    {{-- Knowing others is intelligence; knowing yourself is true wisdom. --}}
    <div class="main-popup-wrap cookies-popup">
        <div class="popup-wrap">
            <div class="popup-close"></div>
            <div class="popup">
                <div class="popup-closer"><img src="/img/close.svg" alt=""></div>
                <h2 class="popup-heading">{{__t('Cookies уведомление')}}</h2>
                <p class="popup-text">{{__t('Мы используем данные cookie, чтобы анализировать поведение посетителей нашего сайта и улучшать его. Используя наш сайт, вы соглашаетесь с данными cookie в соответствии с нашим')}} <a href="{{geturl('privacy-policy')}}">Cookie Policy.</a></p>
                <div class="button-row">
                    <button type="button" class="main-btn main-btn--red get-close-popup" wire:click="giveConsent">{{__t('Я согласен')}}</button>
                </div>
            </div>
        </div>
    </div>
@endif
</div>