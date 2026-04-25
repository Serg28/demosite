{{-- Оплата --}}
<div class="form-section form-section-3 br--br-4 bg--white mt-16 p-24 @if(!$city_id || !$delivery_id) disabled opacity-50 @endif">
    <p class="form-heading fsz-18 fw-600">{{__t('Оплата')}}</p>

    <div style="display: none" x-cloak x-transition x-show="formStep === 3">
    @if($city_id && $delivery_id)

        @error('pay_method_id') <div class="error">{{__t('Выберіть спосіб оплати')}}</div> @enderror

        <div class="mt-24" autocomplete="off">
            {{-- TODO: кешбек --}}
            {{--<div class="cb-row flex v--center h--between">
                <div class="cb flex v--center"><img src="assets/images/cb.svg" alt=""><p>Баланс кешбеку <span class="fw-600 color--orange">815 ₴</span></p></div>
                <div class="btn fsz-14 color--blue">Використати</div>
            </div> --}}
            <div class="radios mt-24 flex fd--column">
                @if($payments && $delivery_id)
                    @include('livewire.checkout.forms.payments')
                @endif
            </div>
            <div x-data="{ open: false }">
                <div class="get-comment-area mt-24 color--blue" @click.prevent="open = ! open">{{__t('Додати коментар до замовлення')}}</div>
                <div class="comment-area mt-16" style="display:none" x-show="open" x-transition>
                    <lebel class="input">
                        <textarea wire:model="comment" placeholder=" "></textarea>
                        <span>{{__t('Введіть текст')}}</span>
                    </lebel>
                </div>
            </div>

            <button type="submit" class="main-btn mt-24 green @if(count($errors)) disabled @endif " @if(count($errors)) disabled @endif @click="sendAnaliticCheckout(3)">
                <span wire:loading.class="spinner" wire:target="submit"></span>
                <span wire:target="submit">{{__t('Замовлення підтверджую')}}</span>
            </button>
            <p class="fsz-12 color--gray text--center mt-24">{!! str_replace('[link]', '#', __t('Підтверджуючи замовлення, я приймаю умови <a href="[link]" target="_blank">Публічної оферти</a>')) !!}</p>
        </div>

    @endif
    </div>
</div>
{{-- /Оплата --}}