<?php

namespace App\Livewire\Profile\User;

use App\Livewire\Forms\Profile\EditDataForm;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class EditData extends Component
{
    public EditDataForm $form;

    public bool $editMode = false;

    public function mount(): void
    {
        $this->form->fillFromUser(Auth::user());
    }

    public function submit(): void
    {
        $this->form->validate();
        $this->form->save(Auth::user());

        $this->editMode = false;
        $this->dispatch('profile-updated');
        session()->flash('success', 'Дані успішно оновлено');
    }

    public function editProfile(): void
    {
        $this->editMode = true;
    }

    public function viewProfile(): void
    {
        $this->editMode = false;
        $this->form->fillFromUser(Auth::user());
    }

    public function render(): View
    {
        return view($this->editMode
            ? 'livewire.profile.user.edit-data'
            : 'livewire.profile.user.data'
        );
    }
}
