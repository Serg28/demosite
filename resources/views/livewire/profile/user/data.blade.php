<div class="card p-6 mb-4">
    <div class="flex items-start justify-between gap-4 mb-5 flex-wrap">
        <div>
            <h2 class="font-bold text-xl mb-1">{{ __t('Дані профілю') }}</h2>
        </div>
        <button type="button" class="btn btn-o btn-sm" wire:click="editProfile">
            ✎ {{ __t('Редагувати') }}
        </button>
    </div>

    @if(session()->has('success'))
        <div class="mb-4 px-4 py-3 rounded-lg bg-green-50 text-green-700 text-sm">{{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-5">
        <div>
            <p class="text-xs text-ink-muted mb-1">{{ __t("Ім'я") }}</p>
            <p class="font-medium">{{ $form->first_name ?: '—' }}</p>
        </div>
        <div>
            <p class="text-xs text-ink-muted mb-1">{{ __t('Прізвище') }}</p>
            <p class="font-medium">{{ $form->last_name ?: '—' }}</p>
        </div>
        <div>
            <p class="text-xs text-ink-muted mb-1">{{ __t('По батькові') }}</p>
            <p class="font-medium">{{ $form->patronymic ?: '—' }}</p>
        </div>
        <div>
            <p class="text-xs text-ink-muted mb-1">{{ __t('Телефон') }}</p>
            <p class="font-medium">{{ $form->phone ?: '—' }}</p>
        </div>
        <div>
            <p class="text-xs text-ink-muted mb-1">Email</p>
            <p class="font-medium">{{ $form->email ?: '—' }}</p>
        </div>
    </div>
</div>
