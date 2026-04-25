<form class="add_review @if($method=='postReply') replay_form @endif" wire:submit="{{$method}}" wire:loading.class="disabled">
    <h4>{{$inputLabel}}</h4>
    @if (session()->has('message'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
            <div class="alert-message success" role="alert">
                <span class="font-medium">{{session('message')}}</span>
            </div>
        </div>
    @endif
    @csrf

    <div class="input_bl">
        <p class="input_label">{{__t('Имя') }}<span>*</span></p>
        <input name="name" type="text" placeholder="{{__t('Укажите ваше имя') }}"  wire:model.live="{{$state}}.name" class="input @error($state.'.name') error @enderror ">
    </div>
    <div class="input_bl">
        <p class="input_label">{{__t('E-mail') }}<span>*</span></p>
        <input name="email" type="text" placeholder="{{__t('Укажите ваш e-mail') }}"  wire:model.live="{{$state}}.email" class="input @error($state.'.email') error @enderror">
    </div>
    <div class="input_bl">
        <p class="input_label">{{$method=='postReply' ? __t('Текст ответа') : __t('Отзыв') }}<span>*</span></p>
        <textarea name="review" id="{{$inputId}}" cols="30" rows="10"
                  class="input @error($state.'.body') error @enderror "
                  wire:model.live.debounce="{{$state}}.body"
                  oninput="detectAtSymbol()"
                  placeholder="{{$method=='postReply' ? __t('Ваш ответ на комментарий') : __t('Напишите о товаре, сервисе и проблеме, которую решил данный товар. ') }}">
        </textarea>
    </div>

    @if($method!=='postReply')
    <div class="add_rating">
        <p>{{__t('Поставьте рейтинг товару') }}  <span>*</span></p>
        <div class="rate-area">
            <input type="radio" id="5-star" name="rating" value="5" wire:model.live="{{$state}}.rating" />
            <label for="5-star" title="{{__t('Удивительно')}}">
                <div class="wrap_arrow">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 22 22" fill="none">
                        <path d="M10.5493 0.938131C10.7309 0.560153 11.2691 0.560153 11.4507 0.938131L14.0643 6.37852C14.1372 6.53024 14.2815 6.63511 14.4484 6.65755L20.4301 7.46208C20.8457 7.51798 21.012 8.02984 20.7087 8.31934L16.3422 12.4862C16.2204 12.6024 16.1653 12.7721 16.1955 12.9377L17.2788 18.8753C17.3541 19.2878 16.9187 19.6042 16.5496 19.4051L11.2374 16.54C11.0892 16.4601 10.9108 16.4601 10.7626 16.54L5.4504 19.4051C5.08132 19.6042 4.6459 19.2878 4.72117 18.8753L5.80448 12.9377C5.83469 12.7721 5.77956 12.6024 5.65779 12.4862L1.29132 8.31934C0.987958 8.02984 1.15427 7.51798 1.56986 7.46208L7.55164 6.65755C7.71846 6.63511 7.86279 6.53024 7.93568 6.37852L10.5493 0.938131Z" fill="#322C4D80"/>
                    </svg>
                </div>
            </label>
            <input type="radio" id="4-star" name="rating" value="4" wire:model.live="{{$state}}.rating" />
            <label for="4-star" title="{{__t('Хорошо')}}">
                <div class="wrap_arrow">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 22 22" fill="none">
                        <path d="M10.5493 0.938131C10.7309 0.560153 11.2691 0.560153 11.4507 0.938131L14.0643 6.37852C14.1372 6.53024 14.2815 6.63511 14.4484 6.65755L20.4301 7.46208C20.8457 7.51798 21.012 8.02984 20.7087 8.31934L16.3422 12.4862C16.2204 12.6024 16.1653 12.7721 16.1955 12.9377L17.2788 18.8753C17.3541 19.2878 16.9187 19.6042 16.5496 19.4051L11.2374 16.54C11.0892 16.4601 10.9108 16.4601 10.7626 16.54L5.4504 19.4051C5.08132 19.6042 4.6459 19.2878 4.72117 18.8753L5.80448 12.9377C5.83469 12.7721 5.77956 12.6024 5.65779 12.4862L1.29132 8.31934C0.987958 8.02984 1.15427 7.51798 1.56986 7.46208L7.55164 6.65755C7.71846 6.63511 7.86279 6.53024 7.93568 6.37852L10.5493 0.938131Z" fill="#322C4D80"/>
                    </svg>
                </div>
            </label>
            <input type="radio" id="3-star" name="rating" value="3" wire:model.live="{{$state}}.rating" />
            <label for="3-star" title="{{__t('Средне')}}">
                <div class="wrap_arrow">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 22 22" fill="none">
                        <path d="M10.5493 0.938131C10.7309 0.560153 11.2691 0.560153 11.4507 0.938131L14.0643 6.37852C14.1372 6.53024 14.2815 6.63511 14.4484 6.65755L20.4301 7.46208C20.8457 7.51798 21.012 8.02984 20.7087 8.31934L16.3422 12.4862C16.2204 12.6024 16.1653 12.7721 16.1955 12.9377L17.2788 18.8753C17.3541 19.2878 16.9187 19.6042 16.5496 19.4051L11.2374 16.54C11.0892 16.4601 10.9108 16.4601 10.7626 16.54L5.4504 19.4051C5.08132 19.6042 4.6459 19.2878 4.72117 18.8753L5.80448 12.9377C5.83469 12.7721 5.77956 12.6024 5.65779 12.4862L1.29132 8.31934C0.987958 8.02984 1.15427 7.51798 1.56986 7.46208L7.55164 6.65755C7.71846 6.63511 7.86279 6.53024 7.93568 6.37852L10.5493 0.938131Z" fill="#322C4D80"/>
                    </svg>
                </div>
            </label>
            <input type="radio" id="2-star" name="rating" value="2" wire:model.live="{{$state}}.rating" />
            <label for="2-star" title="{{__t('Не очень хорошо')}}">
                <div class="wrap_arrow">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 22 22" fill="none">
                        <path d="M10.5493 0.938131C10.7309 0.560153 11.2691 0.560153 11.4507 0.938131L14.0643 6.37852C14.1372 6.53024 14.2815 6.63511 14.4484 6.65755L20.4301 7.46208C20.8457 7.51798 21.012 8.02984 20.7087 8.31934L16.3422 12.4862C16.2204 12.6024 16.1653 12.7721 16.1955 12.9377L17.2788 18.8753C17.3541 19.2878 16.9187 19.6042 16.5496 19.4051L11.2374 16.54C11.0892 16.4601 10.9108 16.4601 10.7626 16.54L5.4504 19.4051C5.08132 19.6042 4.6459 19.2878 4.72117 18.8753L5.80448 12.9377C5.83469 12.7721 5.77956 12.6024 5.65779 12.4862L1.29132 8.31934C0.987958 8.02984 1.15427 7.51798 1.56986 7.46208L7.55164 6.65755C7.71846 6.63511 7.86279 6.53024 7.93568 6.37852L10.5493 0.938131Z" fill="#322C4D80"/>
                    </svg>
                </div>
            </label>
            <input type="radio" id="1-star" name="rating" value="1" wire:model.live="{{$state}}.rating" />
            <label for="1-star" title="{{__t('Плохо')}}">
                <div class="wrap_arrow">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 22 22" fill="none">
                        <path d="M10.5493 0.938131C10.7309 0.560153 11.2691 0.560153 11.4507 0.938131L14.0643 6.37852C14.1372 6.53024 14.2815 6.63511 14.4484 6.65755L20.4301 7.46208C20.8457 7.51798 21.012 8.02984 20.7087 8.31934L16.3422 12.4862C16.2204 12.6024 16.1653 12.7721 16.1955 12.9377L17.2788 18.8753C17.3541 19.2878 16.9187 19.6042 16.5496 19.4051L11.2374 16.54C11.0892 16.4601 10.9108 16.4601 10.7626 16.54L5.4504 19.4051C5.08132 19.6042 4.6459 19.2878 4.72117 18.8753L5.80448 12.9377C5.83469 12.7721 5.77956 12.6024 5.65779 12.4862L1.29132 8.31934C0.987958 8.02984 1.15427 7.51798 1.56986 7.46208L7.55164 6.65755C7.71846 6.63511 7.86279 6.53024 7.93568 6.37852L10.5493 0.938131Z" fill="#322C4D80"/>
                    </svg>
                </div>
            </label>
        </div>
    </div>
    @endif

    {{--
    @error($state.'.body')
    <p class="alert-message error">
        {{$message}}
    </p>
    @enderror
    --}}
    <button wire:loading.attr="disabled" class="main-btn" type="submit" >{{$button}}</button>
</form>

