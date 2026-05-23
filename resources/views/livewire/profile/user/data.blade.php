<div class="account__section">
    <h2 class="account__heading">{{ __t('Персональні дані') }}</h2>

    @if(session()->has('success'))
        <div class="alert alert--success">{{ session('success') }}</div>
    @endif

    <div class="account__form">
        <div class="form-row">
            <span class="form-label">{{ __t('Прізвище') }}</span>
            <span class="form-value">{{ $form->last_name ?: '—' }}</span>
        </div>
        <div class="form-row">
            <span class="form-label">{{ __t("Ім'я") }}</span>
            <span class="form-value">{{ $form->first_name ?: '—' }}</span>
        </div>
        <div class="form-row">
            <span class="form-label">{{ __t('По батькові') }}</span>
            <span class="form-value">{{ $form->patronymic ?: '—' }}</span>
        </div>
        <div class="form-row">
            <span class="form-label">{{ __t('Телефон') }}</span>
            <span class="form-value">{{ $form->phone ?: '—' }}</span>
        </div>
        <div class="form-row">
            <span class="form-label">Email</span>
            <span class="form-value">{{ $form->email ?: '—' }}</span>
        </div>
    </div>

    <button type="button" class="btn btn--primary mt-4" wire:click="editProfile">
        {{ __t('Редагувати') }}
    </button>
</div>
