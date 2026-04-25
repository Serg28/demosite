<?php

namespace App\Livewire\Category;

use App\Models\Category;
use App\Services\Category as CategoryService;
use App\Traits\LivewireShowMore;
//use App\Traits\Referrer;
use Lean\LivewireAccess\FrontendAccess;
use Lean\LivewireAccess\WithExplicitAccess;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class FilterProducts extends Component
{

    use WithExplicitAccess;
    use WithPagination;
    //use Referrer;
    use LivewireShowMore;

    protected CategoryService $categoryService;

    public $page;

    #[FrontendAccess]
    public bool $mobileFilterActive = false;

    public function mount(Category $page): void
    {
        $this->page = $page;
    }

    public function boot(CategoryService $categoryService): void
    {
        $this->categoryService = $categoryService;
        $this->mobileFilterActive = session()->get('mobileFilterActive') ?? false;
    }

    //Клик на кнопке закрытия мобильного фильтра
    public function close(): void
    {
        $this->mobileFilterActive = false;
        session()->put('mobileFilterActive', false);
    }

    //Клик на кнопке открытия мобильного фильтра
    public function open(): void
    {
        $this->mobileFilterActive = true;
        session()->put('mobileFilterActive', true);
    }

    //Установка номера страницы пагинации для использования в ключе кеша фильтра
    public function updatingPaginators($pageId, $pageName)
    {
        if (isset($pageName)) {
            $_GET[$pageName] = $pageId;
        }
    }

    #[On('filter-changed')]
    public function filterChanged(): void
    {
        $this->mobileFilterActive = true;
        $this->resetPage();
    }

    public function render()
    {
        //Делаем запрос
        //$data = $this->categoryService->getDataProductsWithFilters($this->page);
        $data = $this->categoryService->getElasticsearchData($this->page);

        //Результат со списком найденных товаров
        $results = $data['results'] ?? '';

        //С функционалом Показать еще - загружаем товары в products
        //Иначе можно сделать так: $products = $results['products']  или просто в шаблоне использовать $results['products'] вместо $products
        $products = $this->handleLivewireShowMore($data['results']);

        $filter = $data['filter'] ?? ''; //Объект текущего фильтра (выбранные значения, сортировка и т.д.)
        $count = $data['results']['count'] ?? 0; //Количество найденный результатов
        $categoriesFiltered = $data['results']['categories'] ?? null; //Непустые категории

        $characteristics = $this->categoryService->characteristicsForFilters($this->page); //Все характеристики, доступные для фильтра в категории
        $selectedFilters = array_keys($filter->getSelectedFilters() ?? []); //Характеристики, выбранные в фильтре

        $this->dispatch('filter-product-updated');

        return view($this->getView(),
            compact('filter', 'results', 'count', 'characteristics', 'selectedFilters', 'categoriesFiltered','products'));
    }

    public function getView()
    {
        return $this->page->getTemplate('partials.livewire-filter-products');
    }
}
