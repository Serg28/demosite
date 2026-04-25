<?php

namespace App\Livewire\Form\Faqs;

use Livewire\Attributes\Validate;
use Livewire\Component;
use App\Models\Faq;
use Livewire\Attributes\Rule;
use Livewire\Attributes\On;


class Application extends Component
{
    #[Validate('required|between:2,255')]
    public string|null $keyword = '';

    public function getSearch()
    {
        //$this->searchResults = Faq::search($this->keyword)->get();
    }

    public function placeholder()
    {
        return view('livewire.form.faqs.application_empty');
    }

    public function render()
    {
        return view('livewire.form.faqs.application')->with('searchResults', Faq::search($this->keyword)->get());
    }
    public function rendered(){
        $this->dispatch('form-faqs-application-initialized');
    }
}
