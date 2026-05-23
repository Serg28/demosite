<div class="account__section mt-8">
    <h3 class="account__heading account__heading--sm">{{ __t('Зміна пароля') }}</h3>

    @if(session()->has('password_success'))
        <div class="alert alert--success">{{ session('password_success') }}</div>
    @endif

    <form wire:submit="submit" class="account__form" autocomplete="off"
          wire:loading.class="opacity-50">
        <div class="form-field">
            <label class="form-label">{{ __t('Поточний пароль') }} *</label>
            <input type="password" wire:model="form.old_password"
                   class="input {{ $errors->has('form.old_password') ? 'input--error' : '' }}">
            @error('form.old_password') <span class="form-error">{{ $message }}</span> @enderror
        </div>

        <div class="form-field">
            <label class="form-label">{{ __t('Новий пароль') }} *</label>
            <input type="password" wire:model="form.new_password"
                   class="input {{ $errors->has('form.new_password') ? 'input--error' : '' }}">
            @error('form.new_password') <span class="form-error">{{ $message }}</span> @enderror
        </div>

        <div class="form-field">
            <label class="form-label">{{ __t('Повторіть пароль') }} *</label>
            <input type="password" wire:model="form.re_password"
                   class="input {{ $errors->has('form.re_password') ? 'input--error' : '' }}">
            @error('form.re_password') <span class="form-error">{{ $message }}</span> @enderror
        </div>

        <div class="form-actions mt-4">
            <button type="submit" class="btn btn--primary" wire:loading.attr="disabled">
                {{ __t('Змінити пароль') }}
            </button>
        </div>
    </form>
</div>
