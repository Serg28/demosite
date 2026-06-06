<?php

namespace App\Livewire\Profile\Recipient;

use App\Livewire\Forms\Profile\RecipientForm;
use App\Models\UserContact;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Manager extends Component
{
    public RecipientForm $form;

    public bool $showForm = false;

    public ?int $editingId = null;

    #[Computed]
    public function recipients(): \Illuminate\Database\Eloquent\Collection
    {
        return Auth::user()->recipients()->orderByDesc('is_primary')->orderByDesc('id')->get();
    }

    public function edit(int $id): void
    {
        $contact = UserContact::where('user_id', Auth::id())->recipients()->findOrFail($id);
        $this->form->fillFromContact($contact);
        $this->editingId = $id;
        $this->showForm  = true;
    }

    public function save(): void
    {
        if ($this->editingId) {
            $this->update();
        } else {
            $this->add();
        }
    }

    public function add(): void
    {
        $this->form->save(Auth::user());
        $this->form->reset();
        $this->showForm = false;
        $this->dispatch('recipient-added');
    }

    public function update(): void
    {
        $contact = UserContact::where('user_id', Auth::id())->recipients()->findOrFail($this->editingId);
        $this->form->update($contact);
        $this->form->reset();
        $this->editingId = null;
        $this->showForm  = false;
        $this->dispatch('recipient-updated');
    }

    public function delete(int $id): void
    {
        $contact = UserContact::where('user_id', Auth::id())->recipients()->find($id);
        $contact?->delete();
    }

    public function setPrimary(int $id): void
    {
        $user = Auth::user();
        $contact = UserContact::where('user_id', $user->id)
            ->recipients()
            ->find($id);

        if (! $contact) {
            return;
        }

        $user->recipients()->update(['is_primary' => false]);
        $contact->update(['is_primary' => true]);
    }

    public function cancelForm(): void
    {
        $this->form->reset();
        $this->editingId = null;
        $this->showForm  = false;
    }

    public function render(): View
    {
        return view('livewire.profile.recipient.manager');
    }
}
