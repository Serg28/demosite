<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * Компонент фильтрации по чекбоксам.
 *
 * Этот компонент отвечает за отображение группы чекбоксов для фильтрации
 * по характеристикам товаров. Он включает в себя заголовок, поисковое поле,
 * список опций (чекбоксов) и кнопку "Показати ще" для отображения дополнительных опций.
 */
class FilterGroupCheckbox extends Component
{
    /**
     * @var mixed Характеристика, для которой отображается группа чекбоксов.
     */
    public mixed $characteristic;

    /**
     * @var array Результаты фильтрации, содержащие опции и их количество.
     */
    public array $results;

    /**
     * @var mixed Объект фильтра.
     */
    public mixed $filter;

    /**
     * @var array Массив выбранных фильтров.
     */
    public array $selectedFilters;

    /**
     * Создает экземпляр компонента.
     *
     * @param mixed $characteristic Характеристика, для которой отображается группа чекбоксов.
     * @param array $results Результаты фильтрации, содержащие опции и их количество.
     * @param mixed $filter Объект фильтра, используемый для проверки состояния чекбоксов.
     * @param array $selectedFilters Массив выбранных фильтров.
     */
    public function __construct($characteristic, $results, $filter, $selectedFilters)
    {
        $this->characteristic = $characteristic;
        $this->results = $results;
        $this->filter = $filter;
        $this->selectedFilters = $selectedFilters;
    }

    /**
     * Рендерит представление компонента.
     *
     * @return View Представление компонента.
     */
    public function render(): View
    {
        //$isOpened = !$this->characteristic->pivot->is_closed || in_array($this->characteristic->localizedSlug, $this->selectedFilters);
        $isOpened = !$this->characteristic->pivot || (!$this->characteristic->pivot->is_closed || in_array($this->characteristic->localizedSlug, $this->selectedFilters));
        $options = $this->characteristic->optionsCacheFiltered($this->results['optionsStart']);

        return view('components.filter-group-checkbox', [
            'isOpened' => $isOpened,
            'options' => $options,
        ]);
    }
}
