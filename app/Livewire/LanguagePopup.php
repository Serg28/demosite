<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;

/**
 * Livewire-компонент для отображения всплывающего окна с предложением перейти на украинскую версию сайта.
 *
 * Этот компонент проверяет текущую локаль приложения и наличие cookie, чтобы определить,
 * нужно ли отображать всплывающее окно. По-умолчанию окно выводится, если при первом входе на сайт была выбрана
 * русская версия сайта. После закрытия окна (срабатывании метода setUrl) устанавливается кука и окно больше не выводится
 *
 * @property bool $showPopup Определяет, следует ли отображать всплывающее окно выбора языка.
 * @property string|null $page URL текущей страницы.
 */
class LanguagePopup extends Component
{
    public $showPopup = false;

    public $page;

    public function mount()
    {
        // Проверяем, показывать ли popup
        $this->showPopup = App::getLocale() === 'ru' && !Cookie::has('language_popup');
    }

    public function rendered(){
        $this->dispatch('language-popup-initialized');
    }

    public function setUrl($url = null)
    {
        // Записываем в куки информацию о том, что popup уже был показан
        Cookie::queue('language_popup', true, 60 * 24 * 30); // На месяц

        // Скрываем popup
        $this->showPopup = false;

        if ($url) {
            return redirect($url);
        }
    }

    public function render()
    {
        return view('livewire.language-popup');
    }
}
