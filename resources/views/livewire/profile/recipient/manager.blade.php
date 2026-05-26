<div>
    <div class="flex items-center justify-between mb-4 flex-wrap gap-3">
        <div>
            <h2 class="font-bold text-xl">{{ __t('Отримувачі') }}</h2>
            <p class="text-sm text-ink-muted">{{ __t('Контакти тих, хто отримуватиме ваші замовлення') }}</p>
        </div>
        @if(!$showForm)
            <button wire:click="$set('showForm', true)" class="btn btn-p btn-sm">
                + {{ __t('Додати отримувача') }}
            </button>
        @endif
    </div>

    {{-- Add form --}}
    @if($showForm)
        <div class="card p-5 mb-4">
            <h3 class="font-bold text-base mb-1">{{ __t('Новий отримувач') }}</h3>
            <p class="text-sm text-ink-muted mb-4">{{ __t('Контакт буде доступний при оформленні замовлення') }}</p>
            <div class="space-y-3">
                <div>
                    <label class="text-xs font-medium mb-1 block text-ink-muted">{{ __t('Мітка (Дружина, Мама, Колега, ...)') }}</label>
                    <input type="text" wire:model="form.label" class="field" placeholder="{{ __t('Необов\'язково') }}">
                    @error('form.label') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs font-medium mb-1 block text-ink-muted">{{ __t('Ім\'я') }} *</label>
                        <input type="text" wire:model="form.first_name" class="field">
                        @error('form.first_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="text-xs font-medium mb-1 block text-ink-muted">{{ __t('Прізвище') }} *</label>
                        <input type="text" wire:model="form.last_name" class="field">
                        @error('form.last_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <x-phone-input wire:model="form.phone" :label="__t('Телефон') . ' *'" />
                <x-email-input wire:model="form.email" label="Email" />
                <label class="flex items-center gap-2 text-sm cursor-pointer">
                    <input type="checkbox" wire:model="form.is_primary" class="rounded text-brand accent-brand">
                    {{ __t('Зробити основним') }}
                </label>
                <div class="flex gap-2 pt-1">
                    <button wire:click="add" class="btn btn-p flex-1">{{ __t('Зберегти') }}</button>
                    <button wire:click="cancelForm" class="btn btn-o flex-1">{{ __t('Скасувати') }}</button>
                </div>
            </div>
        </div>
    @endif

    {{-- List --}}
    @if($this->recipients->isEmpty())
        <div class="card p-10 text-center">
            <div class="text-4xl mb-3">👥</div>
            <p class="text-ink-muted mb-4">{{ __t('Отримувачів ще немає') }}</p>
            <button wire:click="$set('showForm', true)" class="btn btn-p btn-sm">
                + {{ __t('Додати отримувача') }}
            </button>
        </div>
    @else
        <div class="grid sm:grid-cols-2 gap-3">
            @foreach($this->recipients as $recipient)
                <div class="card p-5 {{ $recipient->is_primary ? 'ring-2 ring-brand' : '' }}" wire:key="rec-{{ $recipient->id }}">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-full bg-brand-light text-brand flex items-center justify-center font-bold flex-shrink-0 uppercase">
                            {{ $recipient->initials() }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1 flex-wrap">
                                <p class="font-bold">{{ $recipient->fullName() }}</p>
                                @if($recipient->is_primary)
                                    <span class="badge bdg-new text-[10px]">{{ __t('Основний') }}</span>
                                @endif
                            </div>
                            @if($recipient->label)
                                <p class="text-xs text-ink-muted mb-2">{{ $recipient->label }}</p>
                            @endif
                            <p class="text-sm">📞 {{ $recipient->phone }}</p>
                            @if($recipient->email)
                                <p class="text-sm text-ink-muted truncate">✉ {{ $recipient->email }}</p>
                            @endif
                        </div>
                        <div class="flex flex-col items-end gap-2 flex-shrink-0">
                            <button wire:click="delete({{ $recipient->id }})"
                                    wire:confirm="{{ __t('Видалити отримувача?') }}"
                                    class="text-gray-400 hover:text-red-500 text-xl leading-none transition-colors">
                                ×
                            </button>
                            @if(!$recipient->is_primary)
                                <button wire:click="setPrimary({{ $recipient->id }})" class="text-xs text-brand hover:underline whitespace-nowrap">
                                    {{ __t('Зробити осн.') }}
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
