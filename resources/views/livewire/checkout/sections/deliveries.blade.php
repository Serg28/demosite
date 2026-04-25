{{-- Доставка --}}
<div class="form-section form-section-2 br--br-4 bg--white p-24 mt-16 @if(!$this->isStepSuccess(1)) disabled opacity-50 @endif">
    <p class="form-heading fsz-18 fw-600">{{__t('Доставка')}}</p>
    {{--@if($this->isStepSuccess(1)) --}}
    <div class="flex fd--column radio-wrapper mt-24" style="display: none" x-cloak x-transition x-show="formStep === 2 || formStep === 3">
        @include('livewire.checkout.forms.deliveries')
        <div class="checkbox-row" x-data="{receive_open: false}">
            <label for="receiver_type" class="checkbox flex v--center">
                <input id="receiver_type" type="checkbox" wire:model="receiver" value="other" class="other-cust" @click="receive_open = $event.target.checked ? true : false">
                <span>{{__t('Інший отримувач')}}</span>
            </label>
            <div class="droppdown-other" x-bind:style="{display: receive_open ? 'block' : 'none'}" >
                <div class="wrapper">
                    <div class="input small">
                        <input @error('receiver_last_name') class="error" @enderror wire:model="receiver_last_name"  placeholder=" ">
                        <span>{{__t('Прізвище')}}</span>
                    </div>
                    <div class="input small">
                        <input @error('receiver_first_name') class="error" @enderror wire:model="receiver_first_name" placeholder=" ">
                        <span>{{__t('Ім’я')}}</span>
                    </div>
                    <div class="input small">
                        <input @error('receiver_patronymic_name') class="error" @enderror wire:model="receiver_patronymic_name" placeholder=" ">
                        <span>{{__t('По-батькові')}}</span>
                    </div>
                    <div class="input small">
                        <input type="tel" class="vc @error('receiver_phone') error @enderror" wire:model="receiver_phone"  x-mask="+99(999) 999-99-99" placeholder="+38(0__) ___-__-__">
                        <!-- <span>Мобільний телефон</span> -->
                    </div>
                </div>
            </div>
        </div>
        <div class="main-btn blue-small" @click="formStep = 3; sendAnaliticCheckout(2)" wire:click="activatePayments">{{__t('Перейти до оплати')}} </div>
    </div>
    {{--@endif --}}
</div>
{{-- /Доставка --}}