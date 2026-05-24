<div class="card p-6 mb-4">
    <div class="flex items-start justify-between gap-4 mb-5 flex-wrap">
        <h2 class="font-bold text-xl">{{ __t('Дані профілю') }}</h2>
        <button type="button" class="btn btn-o btn-sm" wire:click="viewProfile">
            {{ __t('Скасувати') }}
        </button>
    </div>

    <form wire:submit="submit" autocomplete="off" wire:loading.class="opacity-50">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="text-xs font-medium mb-1.5 block text-ink-muted">{{ __t("Ім'я") }} *</label>
                <input type="text" wire:model="form.first_name"
                       class="field {{ $errors->has('form.first_name') ? 'border-red-400' : '' }}">
                @error('form.first_name') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="text-xs font-medium mb-1.5 block text-ink-muted">{{ __t('Прізвище') }} *</label>
                <input type="text" wire:model="form.last_name"
                       class="field {{ $errors->has('form.last_name') ? 'border-red-400' : '' }}">
                @error('form.last_name') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="text-xs font-medium mb-1.5 block text-ink-muted">{{ __t('По батькові') }}</label>
                <input type="text" wire:model="form.patronymic" class="field">
            </div>
            <div>
                <label class="text-xs font-medium mb-1.5 block text-ink-muted">{{ __t('Телефон') }}</label>
                <input type="tel" wire:model="form.phone"
                       x-mask="+38(099) 999-99-99"
                       class="field {{ $errors->has('form.phone') ? 'border-red-400' : '' }}">
                @error('form.phone') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>
            <div class="sm:col-span-2">
                <label class="text-xs font-medium mb-1.5 block text-ink-muted">Email *</label>
                <input type="email" wire:model="form.email"
                       class="field {{ $errors->has('form.email') ? 'border-red-400' : '' }}">
                @error('form.email') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="flex gap-2 pt-4">
            <button type="submit" class="btn btn-p" wire:loading.attr="disabled">
                {{ __t('Зберегти зміни') }}
            </button>
            <button type="button" class="btn btn-o" wire:click="viewProfile">
                {{ __t('Скасувати') }}
            </button>
        </div>
    </form>
</div>
