<?php

namespace App\Livewire\Forms\Profile;

use App\Models\User;
use Livewire\Attributes\Validate;
use Livewire\Form;

class EditDataForm extends Form
{
    #[Validate('required|min:2|max:60', message: ['required' => "Обов'язкове поле", 'min' => 'Не менше 2 символів', 'max' => 'Не більше 60 символів'])]
    public string $last_name = '';

    #[Validate('required|min:2|max:60', message: ['required' => "Обов'язкове поле", 'min' => 'Не менше 2 символів', 'max' => 'Не більше 60 символів'])]
    public string $first_name = '';

    #[Validate('nullable|max:60', message: ['max' => 'Не більше 60 символів'])]
    public string $patronymic = '';

    #[Validate('nullable|min:10|max:20', message: ['min' => 'Не менше 10 символів', 'max' => 'Не більше 20 символів'])]
    public string $phone = '';

    #[Validate('required|email|max:255', message: ['required' => "Обов'язкове поле", 'email' => 'Невірний формат email'])]
    public string $email = '';

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
        $user->update($this->only('last_name', 'first_name', 'patronymic', 'phone', 'email'));
    }
}
