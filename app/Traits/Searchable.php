<?php

namespace App\Traits;

use App\Observers\ElasticsearchObserver;
use Illuminate\Support\Collection;
use Vis\Builder\Models\Language;

trait Searchable
{
    public static function bootSearchable(): void
    {
        static::observe(ElasticsearchObserver::class);
    }

    public function getSearchIndex(): string
    {
        //return env('APP_NAME', 'shop_demo_').$this->getTable();
        return config('app.name').$this->getTable();
    }

    public function getSearchType(): string
    {
        return '_doc';
    }

    public function toSearchArray(): array
    {
        $this->title = preg_replace("/[\r\n]+/", '\\r\\n', $this->title);
        $this->title = str_replace("\t", '\t', $this->title);

        return [
            //'title' => implode('|', array_values(json_decode($this->title, true))),
            'title' => strtolower(implode('|', $this->prepareWordsArray($this->title)).'|'.$this->code),
            //'partial_title' => strtolower(implode('|', $this->prepareWordsArray($this->title)).'|'.$this->code),
            'code' => strtolower($this->code),
            //'part_number' => $this->part_number,
            //'is_active' => $this->is_active, //так было
            'is_active' => ($this->getPrice() > 0 && $this->quantity > 0) ? $this->is_active : 0, //только в наличии и не нулевые цены
            'price' => $this->preparePrice(),
            'options' => $this->getListCharacteristics(),
            'category' => $this->getCategories(),
            'top_category' => $this->prepareTopCategory(),
        ];
    }

    public function prepareWordsArray($value): array
    {
        $array = (is_array($value)) ? $value : json_decode($this->title, true);
        $array = array_values($array);
        $output = [];
        if (is_array($array)) {
            foreach ($array as $item) {
                $output[] = $item;
                $output[] = transliterator_transliterate('Any-Latin; Latin-ASCII', $item); //to latin
                $output[] = transliterator_transliterate('Latin-Russian/BGN', $item); //to rus
            }
            return $output;
        }
        return [];
    }

    private function getListCharacteristics(): Collection
    {
        return $this->characteristics->mapToGroups(function ($item) {
            return [$item->characteristic_id => $item->characteristic_option_id];
        });
    }

    private function getCategories(): Collection
    {
        return $this->categories->pluck('id')->push($this->category_id);
    }

    private function prepareTopCategory()
    {
        return $this->getTopCategory() ? $this->getTopCategory()->id : 1;
    }

    private function preparePrice()
    {
        if (method_exists($this, 'getPricesList')) {
            return $this->getPricesList();
        }

        return $this->price;
    }

}
