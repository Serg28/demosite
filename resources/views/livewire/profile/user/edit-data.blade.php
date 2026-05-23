<div class="account__section">
    <h2 class="account__heading">{{ __t('Редагування даних') }}</h2>

    <form wire:submit="submit" class="account__form" autocomplete="off"
          wire:loading.class="opacity-50">
        <div class="form-field">
            <label class="form-label">{{ __t('Прізвище') }} *</label>
            <input type="text" wire:model="form.last_name"
                   class="input {{ $errors->has('form.last_name') ? 'input--error' : '' }}">
            @error('form.last_name') <span class="form-error">{{ $message }}</span> @enderror
        </div>

        <div class="form-field">
            <label class="form-label">{{ __t("Ім'я") }} *</label>
            <input type="text" wire:model="form.first_name"
                   class="input {{ $errors->has('form.first_name') ? 'input--error' : '' }}">
            @error('form.first_name') <span class="form-error">{{ $message }}</span> @enderror
        </div>

        <div class="form-field">
            <label class="form-label">{{ __t('По батькові') }}</label>
            <input type="text" wire:model="form.patronymic" class="input">
        </div>

        <div class="form-field">
            <label class="form-label">{{ __t('Телефон') }}</label>
            <input type="tel" wire:model="form.phone"
                   x-mask="+38(099) 999-99-99"
                   class="input {{ $errors->has('form.phone') ? 'input--error' : '' }}">
            @error('form.phone') <span class="form-error">{{ $message }}</span> @enderror
        </div>

        <div class="form-field">
            <label class="form-label">Email *</label>
            <input type="email" wire:model="form.email"
                   class="input {{ $errors->has('form.email') ? 'input--error' : '' }}">
            @error('form.email') <span class="form-error">{{ $message }}</span> @enderror
        </div>

        <div class="form-actions mt-4">
            <button type="submit" class="btn btn--primary" wire:loading.attr="disabled">
                {{ __t('Зберегти') }}
            </button>
            <button type="button" class="btn btn--ghost" wire:click="viewProfile">
                {{ __t('Скасувати') }}
            </button>
        </div>
    </form>
</div>
