<x-layouts.auth :title="__t('Скидання пароля')">
    <livewire:auth.reset-password :token="request('token')" />
</x-layouts.auth>
