<p class="mt-8 fw-600">{{__t('Додати фото/відео')}}</p>
<div class="box-image-file">
    @if ($picture)
        <input type="file" wire:model.live="picture"  multiple style="display: none">
        <div class="list-file">
            @foreach($picture as $pic)
                <label id="picture-{{$loop->index}}">
                    <span wire:click="deletePicture({{$loop->index}})" class="pic-delete">X</span>
                    <img src="{{ $pic->temporaryUrl() }}" class="picture" >
                    <span class="name-pic" >{{$pic->getClientOriginalName()}}</span>
                </label>
            @endforeach
        </div>
        <label for="input-1" class="file">
            <div x-data="{ uploading: false, progress: 0 }"
                 x-on:livewire-upload-start="uploading = true"
                 x-on:livewire-upload-finish="uploading = false"
                 x-on:livewire-upload-error="uploading = false"
                 x-on:livewire-upload-progress="progress = $event.detail.progress">
                <input type="file" wire:model.live="file"   id="input-1"  multiple>
                <div class="pic-progress-box" x-show="uploading">
                    <progress max="100" x-bind:value="progress"></progress>
                </div>
                <span class="flex v--center color--blue fw-600 fsz-15">
                <span class="icon flex v--center h--center mr-12">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none">
                        <path d="M2.25 14.25H15.75V15.75H2.25V14.25ZM9.75 4.37132V12.75H8.25V4.37132L3.6967 8.92462L2.63604 7.86398L9 1.5L15.364 7.86398L14.3033 8.92462L9.75 4.37132Z" fill="#2264DC"/>
                    </svg>
                </span>{{__t('Додати файли')}}
            </span>
            </div>
        </label>

        <div class="picture-box-background" wire:loading.class="load-show">
            <svg xmlns:svg="http://www.w3.org/2000/svg" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.0" width="56px" height="14px" viewBox="0 0 128 32" xml:space="preserve">
                <circle fill="#CF051D" cx="0" cy="0" r="11" transform="translate(16 16)"><animateTransform attributeName="transform" type="scale" additive="sum" values="1;1.42;1;1;1;1;1;1;1;1" dur="750ms" repeatCount="indefinite"></animateTransform></circle>
                <circle fill="#CF051D" cx="0" cy="0" r="11" transform="translate(64 16)"><animateTransform attributeName="transform" type="scale" additive="sum" values="1;1;1;1;1.42;1;1;1;1;1" dur="750ms" repeatCount="indefinite"></animateTransform></circle>
                <circle fill="#CF051D" cx="0" cy="0" r="11" transform="translate(112 16)"><animateTransform attributeName="transform" type="scale" additive="sum" values="1;1;1;1;1;1;1;1.42;1;1" dur="750ms" repeatCount="indefinite"></animateTransform></circle>
            </svg>
        </div>
    @else
        <label for="input-1" class="file">
            <div x-data="{ uploading: false, progress: 0 }"
                 x-on:livewire-upload-start="uploading = true"
                 x-on:livewire-upload-finish="uploading = false"
                 x-on:livewire-upload-error="uploading = false"
                 x-on:livewire-upload-progress="progress = $event.detail.progress">
                <input type="file" wire:model.live="file"   id="input-1"  multiple>
                <div class="pic-progress-box" x-show="uploading" style="display: none;">
                    <progress max="100" x-bind:value="progress"></progress>
                </div>
                <span class="flex v--center color--blue fw-600 fsz-15">
                <span class="icon flex v--center h--center mr-12">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none">
                        <path d="M2.25 14.25H15.75V15.75H2.25V14.25ZM9.75 4.37132V12.75H8.25V4.37132L3.6967 8.92462L2.63604 7.86398L9 1.5L15.364 7.86398L14.3033 8.92462L9.75 4.37132Z" fill="#2264DC"/>
                    </svg>
                </span>{{__t('Додати файли')}}
            </span>
            </div>
        </label>
        @error('picture') <span class="error hidden">{{ $message }}</span> @enderror
        <div class="picture-box-background" wire:loading.class="load-show">
            <svg xmlns:svg="http://www.w3.org/2000/svg" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.0" width="56px" height="14px" viewBox="0 0 128 32" xml:space="preserve">
                <circle fill="#CF051D" cx="0" cy="0" r="11" transform="translate(16 16)"><animateTransform attributeName="transform" type="scale" additive="sum" values="1;1.42;1;1;1;1;1;1;1;1" dur="750ms" repeatCount="indefinite"></animateTransform></circle>
                <circle fill="#CF051D" cx="0" cy="0" r="11" transform="translate(64 16)"><animateTransform attributeName="transform" type="scale" additive="sum" values="1;1;1;1;1.42;1;1;1;1;1" dur="750ms" repeatCount="indefinite"></animateTransform></circle>
                <circle fill="#CF051D" cx="0" cy="0" r="11" transform="translate(112 16)"><animateTransform attributeName="transform" type="scale" additive="sum" values="1;1;1;1;1;1;1;1.42;1;1" dur="750ms" repeatCount="indefinite"></animateTransform></circle>
            </svg>
        </div>
    @endif
</div>
<span class="fsz-12 color--gray max-w">Перетягніть файли сюди чи натисніть на кнопку. Додавайте до 10 файлів у форматі .jpg, .gif, .png, .mov, .mp4 розміром файлу до 10 МБ</span>

