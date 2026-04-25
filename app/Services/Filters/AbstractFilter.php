<?php

namespace App\Services\Filters;

use App\Interfaces\SortInterface;
use App\Models\Characteristic;
use App\Models\CharacteristicOption;
use App\Services\Sort\FilterSorting;
use App\Traits\Referrer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;

abstract class AbstractFilter
{
    use Referrer;

    protected Model $category;
    protected SortInterface $sortService;

    protected array $filters = [];
    private array $price = [];

    protected $selectedFilterIdsCache = null;
    protected $characteristicSlugCache = [];

    private string $characteristicPrefix = '';

    public function __construct(Model $category, ?SortInterface $sortService = null)
    {
        $this->category = $category;
        $this->sortService = $sortService ?: new FilterSorting();
    }

    public function init(): self
    {
        $this->filterInit();

        return $this;
    }

    public function getViewType(): string
    {
        return Cookie::get('products-view') ?: 'grid';
    }

    public function minPrice(): int
    {
        // Проверяем, существует ли ключ 'min' в массиве $this->price
        return isset($this->price['min']) ? (int)$this->price['min'] : 0;
    }

    public function maxPrice(int $defaultPrice = 0): int
    {
        // Проверяем, существует ли ключ 'max' в массиве $this->price
        return isset($this->price['max']) ? (int)$this->price['max'] : $defaultPrice;
    }

    protected function createUrlPrice(): ?string
    {
        if (isset($this->price['min'], $this->price['max'])) {
            return 'price=' . $this->price['min'] . '-' . $this->price['max'];
        }

        return null;
    }

    public function getOrderField(): string
    {
        return $this->sortService->getOrderField();
    }

    public function getOrderDirect(): string
    {
        return $this->sortService->getOrderDirect();
    }

    public function getFilterSort(): string
    {
        return $this->sortService->getSorting();
    }

    public function getCurrentSortingText()
    {
        return $this->sortService->getCurrentSortingText();
    }

    public function defaultShow(): int
    {
        return $this->sortService->getDefaultShow();
    }

    public function getCountAll(): array
    {
        return $this->sortService->getCountAll();
    }

    public function getSortingAll(): array
    {
        return $this->sortService->getSortingAll();
    }

    public function getFilterShow(): int
    {
        return $this->sortService->getCountShow();
    }

    public function setFilterShow(string $countShow = null): void
    {
        if ($countShow) {
            $this->sortService->setCountShow($countShow);
        }
    }

    public function &getFilter(): array
    {
        return $this->filters;
    }

    public function getCountFilterSelected(): int
    {
        return count($this->filters);
    }

    public function issetFilters(): bool
    {
        return $this->getFilter() || $this->minPrice() || $this->maxPrice();
    }

    public function getSelectedFilters(): array
    {
        return $this->getFilter();
    }

    public function getRequestPath(): array|string|null
    {
        $currentUrl = request()->getRequestUri();
        return str_contains($currentUrl, 'livewire/update') ? request()->header('referer') : $currentUrl;
    }

    //Текущий url без GET-параметров
    private function prepareRequestPath(): array|string|null
    {
        // Получаем текущий URL
        $currentUrl = $this->getRequestPath();

        if (str_contains($currentUrl, '?')) {
            // Разбиваем URL по символу "?"
            $urlParts = explode('?', $currentUrl);
            // Берем только часть до символа "?"
            return $urlParts[0];
        }
        return $currentUrl;
    }

    //Оригинал - урл типа /характеристика1=опция,опция2
    public function filterInit(): void
    {
        //$collectUrls = explode('/', request()->path());
        $collectUrls = explode('/', $this->prepareRequestPath());
        foreach ($collectUrls as $slug) {
            if (strpos($slug, '=')) {
                [$key, $value] = explode('=', $slug, 2);

                if ($key === 'price') {
                    $prices = explode('-', $value);
                    $this->price = [
                        'min' => $prices[0],
                        'max' => $prices[1],
                    ];
                } elseif ($key === 'show') {
                    $this->sortService->setCountShow($value);
                } elseif ($key === 'sort') {
                    $this->sortService->setSorting($value);
                } else {
                    $options = explode(',', $value);
                    $op = [];
                    foreach ($options as $option) {
                        $op[$option] = $option;
                    }
                    $this->filters[$key] = $op;
                }
            }
        }
    }

    //урл в виде /prefix-характеристика1-опция1-or-опция2/
    /*public function filterInit(): void
    {
        $collectUrls = explode('/', $this->prepareRequestPath());
        foreach ($collectUrls as $slug) {
            if (str_contains($slug, $this->characteristicPrefix)) {

                $filter = str_replace($this->characteristicPrefix, "", $slug);
                [$key, $value] = explode("-", $filter, 2);

                if ($key === 'price') {
                    $prices = explode('-', $value);
                    $this->price = [
                        'min' => $prices[0],
                        'max' => $prices[1],
                    ];
                } elseif ($key === 'show') {
                    $this->sortService->setCountShow($value);
                } elseif ($key === 'sort') {
                    $this->sortService->setSorting($value);
                } else {
                    $options = explode('-or-', $value);
                    $op = [];
                    foreach ($options as $option) {
                        $op[$option] = $option;
                    }
                    $this->filters[$key] = $op;
                }
            }
        }
    }*/

    //Оригинал - без кеширования
    /*public function getSelectedFilterIds(): array
    {
        $filtersIds = [];

        foreach ($this->getSelectedFilters() as $category => $options) {
            $optionModel = $this->getCharacteristicOptionIds($options);
            $categoryModel = $this->getCharacteristic($category);

            if ($categoryModel) {
                $filtersIds[$categoryModel->id] = $optionModel;
            } else {
                $filtersIds[0] = [];
            }
        }

        return $filtersIds;
    }*/

    //Вариант с кешированием
    public function getSelectedFilterIds(): array
    {
        // Если результат уже вычислен и сохранен в кэше, вернуть его
        if ($this->selectedFilterIdsCache !== null) {
            return $this->selectedFilterIdsCache;
        }

        $filtersIds = [];

        foreach ($this->getSelectedFilters() as $category => $options) {
            $optionModel = $this->getCharacteristicOptionIds($options);
            $categoryModel = $this->getCharacteristic($category);

            if ($categoryModel) {
                $filtersIds[$categoryModel->id] = $optionModel;
                // Кешируем слаг характеристики
                $this->characteristicSlugCache[$categoryModel->id] = $categoryModel->localizedSlug;
            } /*else {
                $filtersIds[0] = [];
            }*/
        }

        // Сохранить результат в кэш
        $this->selectedFilterIdsCache = $filtersIds;

        return $filtersIds;
    }

    public function getLocalizedSelectedFilters($locale = false): array
    {
        $filters = [];

        foreach ($this->getSelectedFilters() as $category => $options) {
            $categoryModel = $this->getCharacteristic($category);
            if ($categoryModel) {
                $optionModel = $this->getLocalizedCharacteristicOptionSlugs($options, $locale, $categoryModel->id);
                $filters[$categoryModel->getUrlOrSlug($locale)] = $optionModel;
            } else {
                $filters[0] = [];
            }
        }

        return $filters;
    }

    private function getCharacteristicOptionIds(array $options): array
    {
        return CharacteristicOption::slugIn($options)
            ->remember(20 * 60)->cacheTags(['characteristicoptions'])
            ->pluck('id')->toArray();
    }

    private function getCharacteristic($category): ?Characteristic
    {
        return Characteristic::slug($category)
            ->remember(20 * 60)->cacheTags(['characteristics'])
            ->first();
    }

    private function getLocalizedCharacteristicOptionSlugs(array $options, $locale, $characteristicId): array
    {
        $cacheKey = "filtered_options_{$characteristicId}_{$locale}_".md5(serialize($options));

        return Cache::rememberForever($cacheKey, function () use ($options, $locale, $characteristicId) {
            return CharacteristicOption::slugIn($options)->cacheTags(['characteristicoptions'])
                ->where('characteristic_id', $characteristicId)
                ->get()
                ->map(fn ($option) => $option->getUrlOrSlug($locale))
                ->toArray();
        });
    }


    //Оригинал - урл типа /характеристика1=опция,опция2
    public function createUrl(array $filterOptions): string
    {
        $strUrlCollection = [];

        foreach ($filterOptions as $category => $options) {
            if (count($options)) {
                $strUrlCollection[] = $category . '=' . implode(',', $options);
            }
        }

        return count($strUrlCollection) ? '/' . implode('/', $strUrlCollection) : '';
    }
    //Тоже в виде /характеристика1=опция,опция2
    /*public function createUrl(array $filterOptions): string
    {
        $strUrlCollection = array_filter(array_map(
            static fn ($characteristic, $options) => count($options) ? "$characteristic=" . implode(',', $options) : null,
            array_keys($filterOptions),
            $filterOptions
        ));

        return count($strUrlCollection) ? '/' . implode('/', $strUrlCollection) : '';
    }*/

    //Вариант урл в виде /prefix-характеристика1-опция1-or-опция2/
    /*public function createUrl(array $filterOptions): string
    {
        $strUrlCollection = [];

        foreach ($filterOptions as $category => $options) {
            if (count($options)) {
                $strUrlCollection[] = $this->characteristicPrefix.$category . '-' . implode('-or-', $options);
            }
        }

        return count($strUrlCollection) ? '/' . implode('/', $strUrlCollection) : '';
    }*/


    /**
     * Генерирует URL с локализованными выбранными фильтрами, исключая цену, сортировку и категорию.
     *
     * @param string $locale Локаль, для которой формируется URL.
     * @return string URL с локализованными фильтрами.
     */
    public function getLocalizedUrlFilters($locale): string
    {
        $localizedFilters = $this->getLocalizedSelectedFilters($locale);
        return $this->createUrl($localizedFilters);
    }

    /**
     * Генерирует URL с локализованными выбранными фильтрами, включая сортировку, цену, но исключая категорию.
     *
     * @param string $locale Локаль, для которой формируется URL.
     * @return string URL с локализованными фильтрами, включая сортировку и цену.
     */
    public function localizedUrlWithParam($locale): string
    {
        return $this->getLocalizedUrlFilters($locale) . $this->urlWithParam();
    }

    public function isChecked(CharacteristicOption $option): bool
    {
        return isset($this->filters[$option->characteristic->localizedSlug][$option->localizedSlug]);
    }

    public function urlFilter(CharacteristicOption $option): string
    {
        $filterOptions = $this->filters;
        $characteristicSlug = $option->characteristic->localizedSlug;

        if (isset($filterOptions[$characteristicSlug][$option->localizedSlug])) {
            unset($filterOptions[$characteristicSlug][$option->localizedSlug]);
        } else {
            $filterOptions[$characteristicSlug][$option->localizedSlug] = $option->localizedSlug;
        }

        return $this->getCategoryUrl() . $this->createUrl($filterOptions) . $this->urlWithParam();
    }

    public function withoutPrice(): string
    {
        return $this->model->getUrl() . $this->createUrl($this->getFilter());
    }

    public function getCategoryUrl(): string
    {
        return $this->model->getUrl();
    }

    public function getUrlShowCount(int $count): string
    {
        return $this->getCategoryUrl() . $this->createUrl($this->getFilter()) . $this->urlWithParam(null, $count);
    }

    public function getUrlSort(string $sorting): string
    {
        return $this->getCategoryUrl() . $this->createUrl($this->getFilter()) . $this->urlWithParam($sorting, null);
    }

    public function getUrlWithoutShow(): string
    {
        return $this->getCategoryUrl() . $this->createUrl($this->getFilter());
    }

    public function getUrlFiltersWithoutShow(): string
    {
        return $this->createUrl($this->getFilter());
    }

    // Метод для создания URL фильтра исключительно для указанной выбранной характеристики (например, бренд)
    // Если данная характеристика выбрана в фильтре, будет сформирован урл только на этот фильтр без любых других фильтром и сортировок
    public function createUrlForSpecificCharacteristic(int $characteristicId): string
    {
        $filterOptions = $this->getSelectedFilterIds();
        $specificFilterOptions = [];

        if (isset($filterOptions[$characteristicId])) {
            // Используем кешированный слаг характеристики
            $characteristicSlug = $this->characteristicSlugCache[$characteristicId] ?? null;
            if ($characteristicSlug) {
                $specificFilterOptions[$characteristicSlug] = $filterOptions[$characteristicId];
            }
        }

        return $this->createUrl($specificFilterOptions);
    }

    //Формируем строку ЦЕНА, СОРТИРОВКА, КОЛИЧЕСТВО ЗАПИСЕЙ
    private function urlWithParam(string $sorting = null, int $countShow = null): string
    {
        $params = [];

        if ($this->createUrlPrice()) {
            $params[] = $this->createUrlPrice();
        }

        $params = array_merge($params, $this->sortService->urlParams($sorting, $countShow));
        return count($params) ? '/' . implode('/', $params) : '';
    }


    /**
     * Удаляет указанный параметр и его значение из заданного URL.
     *
     * Этот метод принимает URL и имя параметра, затем удаляет параметр и его значение из URL вида /path/param=value.
     * Возвращает изменённый URL без указанного параметра в виде /path
     *
     * @param string $parameter Имя параметра, который нужно удалить из URL.
     * @param string|null $url Оригинальный URL, из которого нужно удалить параметр.
     * @return string Изменённый URL без указанного параметра.
     */

    public function urlWithoutParam(string $parameter, string $url = null): string
    {
        $url = (!empty($url)) ? $url : currentUrl();

        // Создаем шаблон регулярного выражения для удаления части строки 'parameter=xxxx-xxxx'
        $pattern = '/\/' . preg_quote($parameter, '/') . '=[^\/]+/';

        // Используем регулярное выражение для удаления параметра и его значения
        $newUrl = preg_replace($pattern, '', $url);

        // Удаляем лишние символы (например, возможный '/' в конце)
        return rtrim($newUrl, '/');
    }
}
