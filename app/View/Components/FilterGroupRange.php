<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * Компонент фильтрации по диапазону.
 *
 * Этот компонент отвечает за отображение диапазона фильтрации по значениям.
 * Он включает в себя заголовок, и поле для ввода диапазона.
 */
class FilterGroupRange extends Component
{
    /**
     * @var array Результаты фильтрации, содержащие опции и их количество.
     */
    public array $results;

    /**
     * @var mixed Объект фильтра, используемый для проверки состояния фильтров.
     */
    public mixed $filter;

    /**
     * @var mixed|string Текст заголовка
     */
    public mixed $title;

    /**
     * Создает экземпляр компонента.
     *
     * @param array $results Результаты фильтрации, содержащие опции и их количество.
     * @param mixed $filter Объект фильтра, используемый для проверки состояния фильтров.
     * @param mixed $title Текст заголовка.
     */
    public function __construct(array $results, mixed $filter, $title)
    {
        $this->results = $results;
        $this->filter = $filter;
        $this->title = $title ?: '';
    }

    /**
     * Рендерит представление компонента.
     *
     * @return View Представление компонента.
     */
    public function render(): View
    {
        return view('components.filter-group-range');
    }
}
