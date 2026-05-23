<?php

namespace App\Livewire\Forms\Profile;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Validate;
use Livewire\Form;

class EditPasswordForm extends Form
{
    #[Validate('required|min:6|max:100', message: ['required' => "Обов'язкове поле", 'min' => 'Не менше 6 символів'])]
    public string $old_password = '';

    #[Validate('required|min:6|max:100', message: ['required' => "Обов'язкове поле", 'min' => 'Не менше 6 символів'])]
    public string $new_password = '';

    #[Validate('required', message: ["Обов'язкове поле"])]
    public string $re_password = '';

    public function save(User $user): void
    {
        if (! Hash::check($this->old_password, $user->password)) {
            throw ValidationException::withMessages([
                'form.old_password' => ['Невірний поточний пароль'],
            ]);
        }

        if ($this->new_password !== $this->re_password) {
            throw ValidationException::withMessages([
                'form.re_password' => ['Паролі не співпадають'],
            ]);
        }

        $user->update(['password' => Hash::make($this->new_password)]);

        $this->reset();
    }
}
