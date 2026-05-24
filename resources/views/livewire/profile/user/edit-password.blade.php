<div class="card p-6">
    <h3 class="font-bold text-lg mb-4">{{ __t('Зміна пароля') }}</h3>

    @if(session()->has('password_success'))
        <div class="mb-4 px-4 py-3 rounded-lg bg-green-50 text-green-700 text-sm">{{ session('password_success') }}</div>
    @endif

    <form wire:submit="submit" autocomplete="off" wire:loading.class="opacity-50">
        <div class="space-y-4">
            <div>
                <label class="text-xs font-medium mb-1.5 block text-ink-muted">{{ __t('Поточний пароль') }} *</label>
                <input type="password" wire:model="form.old_password"
                       class="field {{ $errors->has('form.old_password') ? 'border-red-400' : '' }}">
                @error('form.old_password') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="text-xs font-medium mb-1.5 block text-ink-muted">{{ __t('Новий пароль') }} *</label>
                <input type="password" wire:model="form.new_password"
                       class="field {{ $errors->has('form.new_password') ? 'border-red-400' : '' }}">
                @error('form.new_password') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="text-xs font-medium mb-1.5 block text-ink-muted">{{ __t('Повторіть пароль') }} *</label>
                <input type="password" wire:model="form.re_password"
                       class="field {{ $errors->has('form.re_password') ? 'border-red-400' : '' }}">
                @error('form.re_password') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="pt-4">
            <button type="submit" class="btn btn-p" wire:loading.attr="disabled">
                {{ __t('Змінити пароль') }}
            </button>
        </div>
    </form>
</div>
