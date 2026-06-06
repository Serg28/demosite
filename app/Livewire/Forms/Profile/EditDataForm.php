<?php

namespace App\Livewire\Forms\Profile;

use App\Models\User;
use Livewire\Attributes\Validate;
use Livewire\Form;

class EditDataForm extends Form
{
    #[Validate('required|min:2|max:60')]
    public string $last_name = '';

    #[Validate('required|min:2|max:60')]
    public string $first_name = '';

    #[Validate('nullable|max:60')]
    public string $patronymic = '';

    #[Validate('nullable|min:10|max:20')]
    public string $phone = '';

    #[Validate('required|email|max:255')]
    public string $email = '';

    protected function messages(): array
    {
        return [
            'last_name.required'  => __t('Вкажіть прізвище'),
            'last_name.min'       => __t('Прізвище занадто коротке'),
            'first_name.required' => __t("Вкажіть ім'я"),
            'first_name.min'      => __t("Ім'я занадто коротке"),
            'phone.min'           => __t('Номер телефону занадто короткий'),
            'email.required'      => __t('Вкажіть email'),
            'email.email'         => __t('Невірний формат email'),
        ];
    }

    public function fillFromUser(User $user): void
    {
        $this->last_name  = $user->last_name  ?? '';
        $this->first_name = $user->first_name ?? '';
        $this->patronymic = $user->patronymic ?? '';
        $this->phone      = $user->phone      ?? '';
        $this->email      = $user->email      ?? '';
    }

    public function save(User $user): void
    {
        $this->validate();

        $user->update($this->only('last_name', 'first_name', 'patronymic', 'phone', 'email'));
    }
}
