<?php

namespace App\Livewire\Search;

use App\Jobs\SaveStatisticSearch;
use App\Models\Category;
use App\Models\MenuSidebar;
use App\Models\Product;
use App\Services\LanguageCorrect;
use App\Traits\DetectSearchBot;
use App\Traits\LivewireRecaptchable;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Attributes\Validate;
use Livewire\Component;

class LiveForm extends Component
{
    use LivewireRecaptchable;
    use DetectSearchBot;

    #[Url(except: '')]
    //#[Validate('between:2,64')]
    public string|null $text = '';

    private $productLimit = 5;

    private $categoryLimit = 5;

    private function getFields()
    {
        return [
            "id",
            "title",
            "slug",
            "picture",
            "price",
            "price_old"
        ];
    }

    #[Computed(persist: true)]
    private function popularProducts()
    {
        return Product::whereIn('code', ['3456', '14121', '4308', '22912'] )->cardFields()->active()->rememberForever()->get();
    }

    public function submit()
    {
        if(!empty($this->text)) {
            $this->redirect(route('search-result.page') . '?text=' . $this->text);
        } else {
            $this->notify(__t("Введите поисковый запрос длиной не менее 2 символов"),'', 'error');
        }
    }

    public function render()
    {
        $query = $this->text;
        $products = collect();

        if (!empty($this->text)) {

            $translitQuery = $this->prepareQuery($query);

            $products = Product::search($this->text)->select($this->getFields())->take($this->productLimit)->get();

            if (!$products->count()) {
                $products = Product::search($translitQuery)->select($this->getFields())->take($this->productLimit)->get();
            }

            $this->saveStatisticSearch();
        }
        return view('livewire.search.live-form', [
                'popularProducts' => $this->popularProducts,
                'products' => $products,
                'count' => $products->count(),
            ]
        );
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
