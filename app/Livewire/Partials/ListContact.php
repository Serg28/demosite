<?php

namespace App\Livewire\Partials;

use App\Models\Setting;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;
use Livewire\Component;

class ListContact extends Component
{
    public $view;

    public function mount($view = 'livewire.partials.list-contact'): void
    {
        $this->view = $view;
    }

    #[Computed(persist: true)]
    private function getContacts()
    {
        return Cache::tags(['contacts'])->rememberForever(md5('livewire.ListContact_' . $this->view . App::getLocale()),
            function () {
                return Setting::where('group', 'contacts_front')->get();
            });
    }

    public function render()
    {
        return Cache::tags(['contacts'])->remember($this->view . '_' . \App::getLocale(), now()->addDay(),
            function () {
                return view($this->view)->with('data', $this->getContacts())->render();
            }
        );
    }
}
