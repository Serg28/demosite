<?php

namespace App\Livewire\Forms\Profile;

use App\Models\User;
use App\Models\UserContact;
use Livewire\Attributes\Validate;
use Livewire\Form;

class RecipientForm extends Form
{
    #[Validate('nullable|string|max:60')]
    public string $label = '';

    #[Validate('required|string|min:2|max:60')]
    public string $first_name = '';

    #[Validate('required|string|min:2|max:60')]
    public string $last_name = '';

    #[Validate('required|string|min:10|max:20')]
    public string $phone = '';

    #[Validate('nullable|email|max:100')]
    public string $email = '';

    public bool $is_primary = false;

    public function save(User $user): UserContact
    {
        $this->validate();

        if ($this->is_primary) {
            $user->recipients()->update(['is_primary' => false]);
        }

        return $user->contacts()->create([
            'type'       => 'recipient',
            'label'      => $this->label ?: null,
            'first_name' => $this->first_name,
            'last_name'  => $this->last_name,
            'phone'      => $this->phone,
            'email'      => $this->email ?: null,
            'is_primary' => $this->is_primary,
        ]);
    }

    public function reset(...$properties): void
    {
        $this->label      = '';
        $this->first_name = '';
        $this->last_name  = '';
        $this->phone      = '';
        $this->email      = '';
        $this->is_primary = false;

        parent::reset(...$properties);
    }
}
