<?php

namespace App\Livewire\Partials;

use Illuminate\Support\Facades\App;
use Livewire\Component;
use Vis\Builder\Models\Language;

class Langmenu extends Component
{
    private string $view;

    private $page;

    public function mount($page, $view = 'livewire.partials.langmenu'): void
    {
        $this->view = $view;
        $this->page = $page ?? null;
    }

    public function render()
    {
        $languages = getSiteLanguages();
        $count = count($languages);
        $currentLang = App::getLocale();
        $lang = $currentLang === defaultLanguage() ? '/' : '/' . $currentLang . '/';
        $currentUrl = currentUrl();
        return view($this->view, [
            'languages' => $languages,
            'currentLang' => $currentLang,
            'lang' => $lang,
            'count' => $count,
            'currentUrl' => $currentUrl
        ]);
    }
}