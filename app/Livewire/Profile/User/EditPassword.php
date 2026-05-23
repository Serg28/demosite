<?php

namespace App\Livewire\Profile\User;

use App\Livewire\Forms\Profile\EditPasswordForm;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class EditPassword extends Component
{
    public EditPasswordForm $form;

    public function submit(): void
    {
        $this->form->validate();

        try {
            $this->form->save(Auth::user());
            session()->flash('password_success', 'Пароль успішно змінено');
        } catch (ValidationException $e) {
            throw $e;
        }
    }

    public function render(): View
    {
        return view('livewire.profile.user.edit-password');
    }
}
