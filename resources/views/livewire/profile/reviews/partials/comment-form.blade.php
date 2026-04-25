<div>
            <h3 class="fsz-24 fw-600 popup-heading">{{$inputLabel}}</h3>

            @if (session()->has('message'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
                    <p class="mt-24 fw-600">{{session('message')}}</p>
                </div>
            @endif

            @if (session()->has('error'))
                <p class="alert-message error">
                    {{session('error')}}
                </div>
            @endif

            @error($state.'.rating')
            <p class="alert-message error">
                {{$message}}
            </p>
            @enderror

            <form action="" autocomplete="off" class="@if($method=='postReply') replay_form @endif" wire:submit="{{$method}}" wire:loading.class="disabled opacity-50">
                @if($method!=='postReply')
                    <p class="mt-24 fw-600">{{__t('Поставте оцінку')}}</p>
                    <div class="add_rating">
                        <div class="rate-area">
                            <input type="radio" id="5-star" name="rating" value="5" wire:model.live="{{$state}}.rating" />
                            <label for="5-star" title="{{__t('Дивно')}}">
                                <div class="wrap_arrow">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 22 22" fill="none">
                                        <path d="M10.5493 0.938131C10.7309 0.560153 11.2691 0.560153 11.4507 0.938131L14.0643 6.37852C14.1372 6.53024 14.2815 6.63511 14.4484 6.65755L20.4301 7.46208C20.8457 7.51798 21.012 8.02984 20.7087 8.31934L16.3422 12.4862C16.2204 12.6024 16.1653 12.7721 16.1955 12.9377L17.2788 18.8753C17.3541 19.2878 16.9187 19.6042 16.5496 19.4051L11.2374 16.54C11.0892 16.4601 10.9108 16.4601 10.7626 16.54L5.4504 19.4051C5.08132 19.6042 4.6459 19.2878 4.72117 18.8753L5.80448 12.9377C5.83469 12.7721 5.77956 12.6024 5.65779 12.4862L1.29132 8.31934C0.987958 8.02984 1.15427 7.51798 1.56986 7.46208L7.55164 6.65755C7.71846 6.63511 7.86279 6.53024 7.93568 6.37852L10.5493 0.938131Z" fill="#e0e0e0"/>
                                    </svg>
                                </div>
                            </label>
                            <input type="radio" id="4-star" name="rating" value="4" wire:model.live="{{$state}}.rating" />
                            <label for="4-star" title="{{__t('Добре')}}">
                                <div class="wrap_arrow">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 22 22" fill="none">
                                        <path d="M10.5493 0.938131C10.7309 0.560153 11.2691 0.560153 11.4507 0.938131L14.0643 6.37852C14.1372 6.53024 14.2815 6.63511 14.4484 6.65755L20.4301 7.46208C20.8457 7.51798 21.012 8.02984 20.7087 8.31934L16.3422 12.4862C16.2204 12.6024 16.1653 12.7721 16.1955 12.9377L17.2788 18.8753C17.3541 19.2878 16.9187 19.6042 16.5496 19.4051L11.2374 16.54C11.0892 16.4601 10.9108 16.4601 10.7626 16.54L5.4504 19.4051C5.08132 19.6042 4.6459 19.2878 4.72117 18.8753L5.80448 12.9377C5.83469 12.7721 5.77956 12.6024 5.65779 12.4862L1.29132 8.31934C0.987958 8.02984 1.15427 7.51798 1.56986 7.46208L7.55164 6.65755C7.71846 6.63511 7.86279 6.53024 7.93568 6.37852L10.5493 0.938131Z" fill="#e0e0e0"/>
                                    </svg>
                                </div>
                            </label>
                            <input type="radio" id="3-star" name="rating" value="3" wire:model.live="{{$state}}.rating" />
                            <label for="3-star" title="{{__t('Середньо')}}">
                                <div class="wrap_arrow">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 22 22" fill="none">
                                        <path d="M10.5493 0.938131C10.7309 0.560153 11.2691 0.560153 11.4507 0.938131L14.0643 6.37852C14.1372 6.53024 14.2815 6.63511 14.4484 6.65755L20.4301 7.46208C20.8457 7.51798 21.012 8.02984 20.7087 8.31934L16.3422 12.4862C16.2204 12.6024 16.1653 12.7721 16.1955 12.9377L17.2788 18.8753C17.3541 19.2878 16.9187 19.6042 16.5496 19.4051L11.2374 16.54C11.0892 16.4601 10.9108 16.4601 10.7626 16.54L5.4504 19.4051C5.08132 19.6042 4.6459 19.2878 4.72117 18.8753L5.80448 12.9377C5.83469 12.7721 5.77956 12.6024 5.65779 12.4862L1.29132 8.31934C0.987958 8.02984 1.15427 7.51798 1.56986 7.46208L7.55164 6.65755C7.71846 6.63511 7.86279 6.53024 7.93568 6.37852L10.5493 0.938131Z" fill="#e0e0e0"/>
                                    </svg>
                                </div>
                            </label>
                            <input type="radio" id="2-star" name="rating" value="2" wire:model.live="{{$state}}.rating" />
                            <label for="2-star" title="{{__t('Не дуже добре')}}">
                                <div class="wrap_arrow">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 22 22" fill="none">
                                        <path d="M10.5493 0.938131C10.7309 0.560153 11.2691 0.560153 11.4507 0.938131L14.0643 6.37852C14.1372 6.53024 14.2815 6.63511 14.4484 6.65755L20.4301 7.46208C20.8457 7.51798 21.012 8.02984 20.7087 8.31934L16.3422 12.4862C16.2204 12.6024 16.1653 12.7721 16.1955 12.9377L17.2788 18.8753C17.3541 19.2878 16.9187 19.6042 16.5496 19.4051L11.2374 16.54C11.0892 16.4601 10.9108 16.4601 10.7626 16.54L5.4504 19.4051C5.08132 19.6042 4.6459 19.2878 4.72117 18.8753L5.80448 12.9377C5.83469 12.7721 5.77956 12.6024 5.65779 12.4862L1.29132 8.31934C0.987958 8.02984 1.15427 7.51798 1.56986 7.46208L7.55164 6.65755C7.71846 6.63511 7.86279 6.53024 7.93568 6.37852L10.5493 0.938131Z" fill="#e0e0e0"/>
                                    </svg>
                                </div>
                            </label>
                            <input type="radio" id="1-star" name="rating" value="1" wire:model.live="{{$state}}.rating" />
                            <label for="1-star" title="{{__t('Погано')}}">
                                <div class="wrap_arrow">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 22 22" fill="none">
                                        <path d="M10.5493 0.938131C10.7309 0.560153 11.2691 0.560153 11.4507 0.938131L14.0643 6.37852C14.1372 6.53024 14.2815 6.63511 14.4484 6.65755L20.4301 7.46208C20.8457 7.51798 21.012 8.02984 20.7087 8.31934L16.3422 12.4862C16.2204 12.6024 16.1653 12.7721 16.1955 12.9377L17.2788 18.8753C17.3541 19.2878 16.9187 19.6042 16.5496 19.4051L11.2374 16.54C11.0892 16.4601 10.9108 16.4601 10.7626 16.54L5.4504 19.4051C5.08132 19.6042 4.6459 19.2878 4.72117 18.8753L5.80448 12.9377C5.83469 12.7721 5.77956 12.6024 5.65779 12.4862L1.29132 8.31934C0.987958 8.02984 1.15427 7.51798 1.56986 7.46208L7.55164 6.65755C7.71846 6.63511 7.86279 6.53024 7.93568 6.37852L10.5493 0.938131Z" fill="#e0e0e0"/>
                                    </svg>
                                </div>
                            </label>
                        </div>
                    </div>
                @endif
                <lebel class="input mt-16">
                    <input type="text" wire:model="{{$state}}.name" placeholder=" " class="@error($state.'.name') error @enderror ">
                    <span>{{__t('Им\'я') }} *</span>
                </lebel>
                <lebel class="input mt-16">
                    <input type="text" wire:model="{{$state}}.email" placeholder=" " class="@error($state.'.email') error @enderror ">
                    <span>{{__t('E-mail') }} *</span>
                </lebel>
                @if($method!=='postReply')
                <lebel class="input mt-16">
                    <input type="text" wire:model="{{$state}}.plus_text" placeholder=" " class="@error($state.'.plus_text') error @enderror ">
                    <span>{{__t('Плюси')}} *</span>
                </lebel>
                <lebel class="input mt-16">
                    <input type="text" wire:model="{{$state}}.minus_text" placeholder=" " class="@error($state.'.minus_text') error @enderror ">
                    <span>{{__t('Мінуси')}} *</span>
                </lebel>
                @endif
                <lebel class="input mt-16">
                    <textarea name="review" id="{{$inputId}}" placeholder=" "
                              class="@error($state.'.body') error @enderror "
                              wire:model="{{$state}}.body"
                              oninput="detectAtSymbol()"
                    ></textarea>
                    <span>{{$method=='postReply' ? __t('Текст відповіді') : __t('Відгук') }} *</span>
                </lebel>
                {{--
                <label for="input-agree" class="checkbox flex v--center mt-24">
                    <input type="checkbox" id="input-agree">
                    <p>Повідомляти про відповіді по електронній пошті</p>
                </label> --}}
                <button class="main-btn blue-big mt-24" type="submit" >{{$button}}</button>
                <span class="fsz-12 mt-24 text--center flex">{{__t('Відправляючи коментар/відгук, ви погоджуєтеся з правилами модерації коментарів і відгуків')}}</span>
            </form>
</div>