<?php

namespace App\Livewire\Search;

use App\Jobs\SaveStatisticSearch;
use App\Models\Product;
use App\Services\LanguageCorrect;
use App\Services\Sort\FilterSorting;
use App\Traits\DetectSearchBot;
use App\Traits\LivewireRecaptchable;
use App\Traits\LivewireShowMore;
use Livewire\Attributes\Url;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

class Page extends Component
{
    use LivewireRecaptchable;
    use DetectSearchBot;
    use WithPagination;
    use LivewireShowMore;

    #[Url(except: '')]
    //#[Validate('between:4,64')]
    public string|null $text = '';

    #[Url(except: '')]
    public string|null $sort = '';

    private $limit = 18;

    private FilterSorting $sortService;

    public function boot()
    {
        $this->sortService = new FilterSorting();
    }

    private function getFields()
    {
        return (new Product())->cardFields;
    }

    public function submit()
    {

    }

    public function render()
    {
        $query = $this->text;
        $products = collect();
        $count = 0;

        //$this->validate($this->getRules());

        if (!empty($this->text)) {
            $products = Product::search($query)->orderBy($this->sortService->getOrderField(), $this->sortService->getOrderDirect())->take(5000)->paginate($this->limit);

            if (!$products->count()) {
                $query = $this->prepareQuery($query);
                $products = Product::search($query)->orderBy($this->sortService->getOrderField(), $this->sortService->getOrderDirect())->take(5000)->paginate($this->limit);
            }

            $count = $products->total();
            $results['products'] = $products;
            $products = $this->handleLivewireShowMore($results);

            $this->saveStatisticSearch();

            //$correctWord = ($query!==$this->text && $products->count()) ? $query : '';
            //$oldWord = $this->text;
            //$query = ($this->text!==$query && $products->count()) ? $query : $this->text;
            //$this->text = $query;
        }
        return view('livewire.search.page', [
                'products' => $products,
                'results' => $results ?? null,
                'count' => $count,
                'filter' => $this->sortService
            ]
        );
    }

    //Установка номера страницы пагинации для использования в ключе кеша фильтра
    public function updatingPaginators($pageId, $pageName)
    {
        if (isset($pageName)) {
            $_GET[$pageName] = $pageId;
        }
    }

    private function prepareQuery($query)
    {
        try {
            return LanguageCorrect::keyboardLayoutConvertEnRuAuto($query);
        } catch (\Exception $e) {
            return $query;
        }
    }

    private function saveStatisticSearch($query = null): void
    {
        if (!$this->isSearchBot()) {
            $query = $query ?? $this->text;
            SaveStatisticSearch::dispatch($query, app('user') ?? false);
        }
    }
}
