<div>
    <h2 class="popup-heading">{{$subject}}</h2>
    <form wire:submit="submit" autocomplete="off" id="{{$formId}}">
        <div class="flex-row">
            @csrf
            @if($recaptcha) <livewire:recaptcha :formId="$formId" /> @endif
        </div>
        <div class="flex-row">
            <div class="left">
                <p>{{__t('Про вас')}}</p>
                <input type="text"  @error('name') class="error" @enderror wire:model.live="name" name="name" placeholder="{{__t('Імя')}}">
                @error('name') <p class="error hidden">{{ $message }}</p> @enderror
            </div>
            <div class="right">
                <p>{{__t('Телефон')}}</p>
                <input type="tel" @error('phone') class="error" @enderror wire:model.live="phone" name="phone" placeholder="{{__t('Телефон')}}">
                @error('phone') <p class="error hidden">{{ $message }}</p> @enderror
            </div>
        </div>
        <div class="flex-row">
            <div class="left">
                <p>{{__t('Про вашу Тесла')}}</p>
                <select wire:model.live="model" name="model" id="model" class="select @error('model') error @enderror ">
                    <option value="">Модель</option>
                    <option value="model2">Model S</option>
                    <option value="model3">Model M</option>
                    <option value="model4">Model Y</option>
                    <option value="model5">Model 3</option>
                </select>
                @error('model') <p class="error hidden">{{ $message }}</p> @enderror
            </div>
            <div class="right">
                <p>{{__t('VIN-код')}}</p>
                <input type="text"  @error('vin') class="error" @enderror wire:model.live="vin" name="vin" placeholder="1ABCD111222333444">
                @error('vin') <p class="error hidden">{{ $message }}</p> @enderror
            </div>
        </div>
        <div class="flex-row">
            <div class="left">
                <p>{{__t('Дата реєстрації')}}</p>
                <input type="text"  @error('regdate') class="error" @enderror wire:model.live="regdate" name="regdate" placeholder="{{__t('Рік/місяць першої реєстрації')}}">
                @error('regdate') <p class="error hidden">{{ $message }}</p> @enderror


            </div>
            <div class="right">
                <p>{{__t('Пробіг')}}</p>
                <input type="text"  @error('mileage') class="error" @enderror wire:model.live="mileage" name="mileage" placeholder="{{__t('Розмір пробігу вашої Tesla')}}">
                @error('mileage') <p class="error hidden">{{ $message }}</p> @enderror

            </div>
        </div>
        <p>{{__t('Повідомлення (поле не обов\'язкове)')}}</p>
        <textarea @error('comment') class="error" @enderror wire:model.live="comment" name="comment" id="" placeholder="{{__t('Сообщение')}} {{__t('Вкажіть бажану ціну та розкажіть історію ремонтів')}}"></textarea>
        @error('comment') <p class="error hidden">{{ $message }}</p> @enderror

        <p>{{__t('Додати 2-3 фото автомобіля')}}</p>
        <div class="label-row">

            @if ($picture)
                <input type="file" wire:model.live="picture"  multiple style="display: none">
                @foreach($picture as $pic)
                    <label id="picture-{{$loop->index}}">
                        <span wire:click="deletePicture({{$loop->index}})" class="pic-delete">X</span>
                        <img src="{{ $pic->temporaryUrl() }}" class="picture" >
                        <span class="name-pic" >{{$pic->getClientOriginalName()}}</span>
                    </label>
                @endforeach
                <label for="input-1">
                        <img src="/img/lp.svg" alt="">
                        <span>{{__t('Додати фото')}}</span>
                    <div x-data="{ uploading: false, progress: 0 }"
                         x-on:livewire-upload-start="uploading = true"
                         x-on:livewire-upload-finish="uploading = false"
                         x-on:livewire-upload-error="uploading = false"
                         x-on:livewire-upload-progress="progress = $event.detail.progress">
                        <input type="file" wire:model.live="file"   id="input-1"  multiple>
                        <div class="pic-progress-box" x-show="uploading">
                            <progress max="100" x-bind:value="progress"></progress>
                        </div>
                    </div>
                </label>

                <div class="picture-box-background" wire:loading.class="load-show">
                    <svg xmlns:svg="http://www.w3.org/2000/svg" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.0" width="56px" height="14px" viewBox="0 0 128 32" xml:space="preserve">
                        <circle fill="#CF051D" cx="0" cy="0" r="11" transform="translate(16 16)">
                            <animateTransform attributeName="transform" type="scale" additive="sum" values="1;1.42;1;1;1;1;1;1;1;1" dur="750ms" repeatCount="indefinite"></animateTransform>
                        </circle>
                        <circle fill="#CF051D" cx="0" cy="0" r="11" transform="translate(64 16)">
                            <animateTransform attributeName="transform" type="scale" additive="sum" values="1;1;1;1;1.42;1;1;1;1;1" dur="750ms" repeatCount="indefinite"></animateTransform>
                        </circle>
                        <circle fill="#CF051D" cx="0" cy="0" r="11" transform="translate(112 16)">
                            <animateTransform attributeName="transform" type="scale" additive="sum" values="1;1;1;1;1;1;1;1.42;1;1" dur="750ms" repeatCount="indefinite"></animateTransform>
                        </circle>
                    </svg>
                </div>
            @else
                <label for="input-1">
                    <img src="/img/lp.svg" alt="">
                    <span >{{__t('Додати фото')}}</span>
                    <div x-data="{ uploading: false, progress: 0 }"
                         x-on:livewire-upload-start="uploading = true"
                         x-on:livewire-upload-finish="uploading = false"
                         x-on:livewire-upload-error="uploading = false"
                         x-on:livewire-upload-progress="progress = $event.detail.progress">
                        <input type="file" wire:model.live="file"   id="input-1"  multiple>
                        <div class="pic-progress-box" x-show="uploading" style="display: none;">
                            <progress max="100" x-bind:value="progress"></progress>
                        </div>
                    </div>
                </label>
                @error('picture') <span class="error hidden">{{ $message }}</span> @enderror
                <div class="picture-box-background" wire:loading.class="load-show">
                    <svg xmlns:svg="http://www.w3.org/2000/svg" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.0" width="56px" height="14px" viewBox="0 0 128 32" xml:space="preserve">
                        <circle fill="#CF051D" cx="0" cy="0" r="11" transform="translate(16 16)">
                            <animateTransform attributeName="transform" type="scale" additive="sum" values="1;1.42;1;1;1;1;1;1;1;1" dur="750ms" repeatCount="indefinite"></animateTransform>
                        </circle>
                        <circle fill="#CF051D" cx="0" cy="0" r="11" transform="translate(64 16)">
                            <animateTransform attributeName="transform" type="scale" additive="sum" values="1;1;1;1;1.42;1;1;1;1;1" dur="750ms" repeatCount="indefinite"></animateTransform>
                        </circle>
                        <circle fill="#CF051D" cx="0" cy="0" r="11" transform="translate(112 16)">
                            <animateTransform attributeName="transform" type="scale" additive="sum" values="1;1;1;1;1;1;1;1.42;1;1" dur="750ms" repeatCount="indefinite"></animateTransform>
                        </circle>
                    </svg>
                </div>
            @endif

        </div>
        <span>{{__t('Максимальний розмір завантаженого файлу 2 МБ.')}} </span>

        <div class="checkbox-row">
            <label for="input-checkbox">
                <input type="checkbox" class="checkbox @error('checkbox') error @enderror " wire:model.live="checkbox" name="checkbox" id="input-checkbox">
                <p>{{__t('Я подтверждаю, что прочитал и одобрил')}} <a href="{{route('privacy-policy')}}">{{__t('політику конфіденційності')}}</a></p>
            </label>
            @error('checkbox') <p class="error hidden">{{ $message }}</p> @enderror
        </div>
        <button type="submit" class="main-btn main-btn--red" name="submit" value="Submit" wire:loading.class="main-btn-disable" wire:loading.attr="disabled">
            <span wire:loading.class="hidden" wire:target="submit">{{__t('Відправити заявку')}}</span><span wire:loading wire:target="submit" > {{__t('Подождите...')}}</span>
        </button>

    </form>
{{--    @if (session()->has('success'))--}}
{{--        {{ session('success') }}--}}
{{--    @endif--}}
    @error('g_recaptcha_response') <p class="error hidden">{{ $message }}</p> @enderror
</div>
